<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Business rules formerly implemented in Oracle findit_pkg.
 * Uses MySQL transactions; audit rows are also written by DB triggers.
 */
class FinditPlsqlService
{
    protected function fail(string $message): never
    {
        throw new RuntimeException($message);
    }

    protected function setActor(?string $actor): void
    {
        DB::statement('SET @findit_actor = ?', [$actor]);
    }

    public function registerUser(string $name, string $email, string $password, ?string $phone, ?string $address): int
    {
        return (int) DB::transaction(function () use ($name, $email, $password, $phone, $address) {
            $exists = DB::selectOne(
                'SELECT COUNT(*) AS cnt FROM users WHERE LOWER(email) = LOWER(?)',
                [$email]
            );

            if ((int) ($exists->cnt ?? 0) > 0) {
                $this->fail('Email already registered');
            }

            return DB::table('users')->insertGetId([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'address' => $address,
            ], 'user_id');
        });
    }

    public function addItem(array $data): int
    {
        return (int) DB::transaction(function () use ($data) {
            $type = strtoupper((string) $data['item_type']);

            if (! in_array($type, ['LOST', 'FOUND'], true)) {
                $this->fail('Item type must be LOST or FOUND');
            }

            return DB::table('items')->insertGetId([
                'user_id' => $data['user_id'],
                'category_id' => $data['category_id'],
                'location_id' => $data['location_id'],
                'item_name' => $data['item_name'],
                'item_description' => $data['item_description'] ?? null,
                'item_type' => $type,
                'item_image' => $data['item_image'] ?? null,
                'lost_or_found_date' => $data['lost_or_found_date'],
                'status' => 'PENDING',
            ], 'item_id');
        });
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        DB::transaction(function () use ($itemId, $status) {
            $status = strtoupper($status);
            $allowed = ['PENDING', 'FOUND', 'CLAIMED', 'RETURNED', 'REJECTED'];

            if (! in_array($status, $allowed, true)) {
                $this->fail('Invalid item status');
            }

            $updated = DB::table('items')->where('item_id', $itemId)->update(['status' => $status]);

            if ($updated === 0) {
                $this->fail('Item not found');
            }
        });
    }

    public function submitClaim(int $itemId, int $userId, ?string $message, ?string $proof): int
    {
        return (int) DB::transaction(function () use ($itemId, $userId, $message, $proof) {
            $item = DB::selectOne('SELECT user_id, status FROM items WHERE item_id = ?', [$itemId]);

            if (! $item) {
                $this->fail('Item not found');
            }

            if ((int) $item->user_id === $userId) {
                $this->fail('Cannot claim your own item');
            }

            if (in_array($item->status, ['CLAIMED', 'RETURNED', 'REJECTED'], true)) {
                $this->fail('Item is not available for claims');
            }

            $pending = DB::selectOne(
                'SELECT COUNT(*) AS cnt FROM claims WHERE item_id = ? AND user_id = ? AND claim_status = ?',
                [$itemId, $userId, 'PENDING']
            );

            if ((int) ($pending->cnt ?? 0) > 0) {
                $this->fail('You already have a pending claim for this item');
            }

            return DB::table('claims')->insertGetId([
                'item_id' => $itemId,
                'user_id' => $userId,
                'claim_message' => $message,
                'proof_description' => $proof,
                'claim_status' => 'PENDING',
            ], 'claim_id');
        });
    }

    public function approveClaim(int $claimId, string $adminName): void
    {
        DB::transaction(function () use ($claimId, $adminName) {
            $this->setActor($adminName ?: null);

            $claim = DB::selectOne(
                'SELECT item_id, claim_status FROM claims WHERE claim_id = ?',
                [$claimId]
            );

            if (! $claim) {
                $this->fail('Claim not found');
            }

            if ($claim->claim_status !== 'PENDING') {
                $this->fail('Only pending claims can be approved');
            }

            DB::table('claims')->where('claim_id', $claimId)->update(['claim_status' => 'APPROVED']);

            DB::table('claims')
                ->where('item_id', $claim->item_id)
                ->where('claim_id', '<>', $claimId)
                ->where('claim_status', 'PENDING')
                ->update(['claim_status' => 'REJECTED']);

            DB::table('items')->where('item_id', $claim->item_id)->update(['status' => 'CLAIMED']);

            DB::table('audit_logs')->insert([
                'table_name' => 'CLAIMS',
                'record_id' => $claimId,
                'action_type' => 'UPDATE',
                'old_status' => 'PENDING',
                'new_status' => 'APPROVED',
                'action_by' => $adminName ?: 'SYSTEM',
            ]);
        });
    }

    public function rejectClaim(int $claimId, string $adminName): void
    {
        DB::transaction(function () use ($claimId, $adminName) {
            $this->setActor($adminName ?: null);

            $claim = DB::selectOne(
                'SELECT claim_status FROM claims WHERE claim_id = ?',
                [$claimId]
            );

            if (! $claim) {
                $this->fail('Claim not found');
            }

            if ($claim->claim_status !== 'PENDING') {
                $this->fail('Only pending claims can be rejected');
            }

            DB::table('claims')->where('claim_id', $claimId)->update(['claim_status' => 'REJECTED']);

            DB::table('audit_logs')->insert([
                'table_name' => 'CLAIMS',
                'record_id' => $claimId,
                'action_type' => 'UPDATE',
                'old_status' => 'PENDING',
                'new_status' => 'REJECTED',
                'action_by' => $adminName ?: 'SYSTEM',
            ]);
        });
    }

    public function addCategory(string $name): int
    {
        try {
            return (int) DB::table('categories')->insertGetId([
                'category_name' => $name,
            ], 'category_id');
        } catch (Throwable $e) {
            if ($this->isDuplicateKey($e)) {
                $this->fail('Category already exists');
            }

            throw $e;
        }
    }

    public function addLocation(string $name, ?string $description, ?float $latitude = null, ?float $longitude = null): int
    {
        if ($latitude === null || $longitude === null) {
            [$latitude, $longitude] = app(CampusGeocoder::class)->resolve($name);
        }

        try {
            return (int) DB::table('locations')->insertGetId([
                'location_name' => $name,
                'description' => $description,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ], 'location_id');
        } catch (Throwable $e) {
            if ($this->isDuplicateKey($e)) {
                $this->fail('Location already exists');
            }

            throw $e;
        }
    }

    public function deleteCategory(int $id): void
    {
        $count = DB::selectOne(
            'SELECT COUNT(*) AS cnt FROM items WHERE category_id = ?',
            [$id]
        );

        if ((int) ($count->cnt ?? 0) > 0) {
            $this->fail('Cannot delete category with linked items');
        }

        DB::table('categories')->where('category_id', $id)->delete();
    }

    public function deleteLocation(int $id): void
    {
        $count = DB::selectOne(
            'SELECT COUNT(*) AS cnt FROM items WHERE location_id = ?',
            [$id]
        );

        if ((int) ($count->cnt ?? 0) > 0) {
            $this->fail('Cannot delete location with linked items');
        }

        DB::table('locations')->where('location_id', $id)->delete();
    }

    public function deleteUser(int $id): void
    {
        $deleted = DB::table('users')->where('user_id', $id)->delete();

        if ($deleted === 0) {
            $this->fail('User not found');
        }
    }

    public function deleteItem(int $id): void
    {
        $deleted = DB::table('items')->where('item_id', $id)->delete();

        if ($deleted === 0) {
            $this->fail('Item not found');
        }
    }

    public function dashboardStats(): array
    {
        $row = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM users) AS total_users,
                (SELECT COUNT(*) FROM items) AS total_items,
                (SELECT COUNT(*) FROM claims WHERE claim_status = 'PENDING') AS pending_claims,
                (SELECT COUNT(*) FROM claims WHERE claim_status = 'APPROVED') AS approved_claims,
                (SELECT COUNT(*) FROM items WHERE item_type = 'LOST') AS lost_items,
                (SELECT COUNT(*) FROM items WHERE item_type = 'FOUND') AS found_items
        ");

        return [
            'total_users' => (int) ($row->total_users ?? 0),
            'total_items' => (int) ($row->total_items ?? 0),
            'pending_claims' => (int) ($row->pending_claims ?? 0),
            'approved_claims' => (int) ($row->approved_claims ?? 0),
            'lost_items' => (int) ($row->lost_items ?? 0),
            'found_items' => (int) ($row->found_items ?? 0),
        ];
    }

    protected function isDuplicateKey(Throwable $e): bool
    {
        $code = (int) $e->getCode();
        $message = $e->getMessage();

        return $code === 23000
            || str_contains($message, 'Duplicate entry')
            || str_contains($message, '1062');
    }
}
