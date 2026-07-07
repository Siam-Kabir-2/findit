<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;

class FinditPlsqlService
{
    protected function pdo(): PDO
    {
        return DB::connection()->getPdo();
    }

    protected function exec(string $sql, array $params = []): void
    {
        try {
            $stmt = $this->pdo()->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new RuntimeException($this->cleanOracleError($e->getMessage()), (int) $e->getCode(), $e);
        }
    }

    protected function execReturningId(string $plsql, array $params, string $currvalSql): int
    {
        $this->exec($plsql, $params);
        $row = DB::selectOne($currvalSql);

        return (int) ($row->id ?? 0);
    }

    protected function cleanOracleError(string $message): string
    {
        if (preg_match('/ORA-\d+:\s*(.+?)(?:\n|$)/', $message, $m)) {
            return trim($m[1]);
        }

        return $message;
    }

    public function registerUser(string $name, string $email, string $password, ?string $phone, ?string $address): int
    {
        return $this->execReturningId(
            'DECLARE v_id NUMBER; BEGIN findit_pkg.register_user(:name, :email, :password, :phone, :address, v_id); END;',
            [
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
                ':phone' => $phone,
                ':address' => $address,
            ],
            'SELECT seq_users.CURRVAL AS id FROM dual'
        );
    }

    public function addItem(array $data): int
    {
        return $this->execReturningId(
            'DECLARE v_id NUMBER; BEGIN findit_pkg.add_item(:user_id, :category_id, :location_id, :item_name, :item_description, :item_type, :item_image, TO_DATE(:item_date, \'YYYY-MM-DD\'), v_id); END;',
            [
                ':user_id' => $data['user_id'],
                ':category_id' => $data['category_id'],
                ':location_id' => $data['location_id'],
                ':item_name' => $data['item_name'],
                ':item_description' => $data['item_description'] ?? null,
                ':item_type' => $data['item_type'],
                ':item_image' => $data['item_image'] ?? null,
                ':item_date' => $data['lost_or_found_date'],
            ],
            'SELECT seq_items.CURRVAL AS id FROM dual'
        );
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        $this->exec(
            'BEGIN findit_pkg.update_item_status(:item_id, :status); END;',
            [':item_id' => $itemId, ':status' => $status]
        );
    }

    public function submitClaim(int $itemId, int $userId, ?string $message, ?string $proof): int
    {
        return $this->execReturningId(
            'DECLARE v_id NUMBER; BEGIN findit_pkg.submit_claim(:item_id, :user_id, :message, :proof, v_id); END;',
            [
                ':item_id' => $itemId,
                ':user_id' => $userId,
                ':message' => $message,
                ':proof' => $proof,
            ],
            'SELECT seq_claims.CURRVAL AS id FROM dual'
        );
    }

    public function approveClaim(int $claimId, string $adminName): void
    {
        $this->exec(
            'BEGIN findit_pkg.approve_claim(:claim_id, :admin_name); END;',
            [':claim_id' => $claimId, ':admin_name' => $adminName]
        );
    }

    public function rejectClaim(int $claimId, string $adminName): void
    {
        $this->exec(
            'BEGIN findit_pkg.reject_claim(:claim_id, :admin_name); END;',
            [':claim_id' => $claimId, ':admin_name' => $adminName]
        );
    }

    public function addCategory(string $name): int
    {
        return $this->execReturningId(
            'DECLARE v_id NUMBER; BEGIN findit_pkg.add_category(:name, v_id); END;',
            [':name' => $name],
            'SELECT seq_categories.CURRVAL AS id FROM dual'
        );
    }

    public function addLocation(string $name, ?string $description): int
    {
        return $this->execReturningId(
            'DECLARE v_id NUMBER; BEGIN findit_pkg.add_location(:name, :description, v_id); END;',
            [':name' => $name, ':description' => $description],
            'SELECT seq_locations.CURRVAL AS id FROM dual'
        );
    }

    public function deleteCategory(int $id): void
    {
        $this->exec('BEGIN findit_pkg.delete_category(:id); END;', [':id' => $id]);
    }

    public function deleteLocation(int $id): void
    {
        $this->exec('BEGIN findit_pkg.delete_location(:id); END;', [':id' => $id]);
    }

    public function deleteUser(int $id): void
    {
        $this->exec('BEGIN findit_pkg.delete_user(:id); END;', [':id' => $id]);
    }

    public function deleteItem(int $id): void
    {
        $this->exec('BEGIN findit_pkg.delete_item(:id); END;', [':id' => $id]);
    }

    public function dashboardStats(): array
    {
        $row = DB::selectOne("
            SELECT
                findit_pkg.get_total_users AS total_users,
                findit_pkg.get_total_items AS total_items,
                findit_pkg.get_pending_claims AS pending_claims,
                findit_pkg.get_approved_claims AS approved_claims,
                findit_pkg.get_lost_items AS lost_items,
                findit_pkg.get_found_items AS found_items
            FROM dual
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
}
