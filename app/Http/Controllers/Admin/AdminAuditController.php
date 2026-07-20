<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAuditController extends Controller
{
    public function index(): View
    {
        $rows = $this->fetchEnrichedLogs();

        $events = collect($rows)->map(fn ($row) => $this->presentEvent($row))->all();

        $stats = [
            'total' => count($events),
            'items' => collect($events)->where('entity', 'item')->count(),
            'claims' => collect($events)->where('entity', 'claim')->count(),
            'today' => collect($events)->where('is_today', true)->count(),
        ];

        return view('admin.audit', compact('events', 'stats'));
    }

    /**
     * @return list<object>
     */
    public static function fetchEnrichedLogs(?int $limit = null): array
    {
        $sql = "
            SELECT
                a.audit_id,
                a.table_name,
                a.record_id,
                a.action_type,
                a.old_status,
                a.new_status,
                a.action_by,
                a.action_date,
                CASE
                    WHEN UPPER(a.table_name) = 'ITEMS' THEN i.item_name
                    WHEN UPPER(a.table_name) = 'CLAIMS' THEN ci.item_name
                    ELSE NULL
                END AS subject_name,
                CASE
                    WHEN UPPER(a.table_name) = 'ITEMS' THEN i.item_type
                    WHEN UPPER(a.table_name) = 'CLAIMS' THEN ci.item_type
                    ELSE NULL
                END AS item_type,
                CASE
                    WHEN UPPER(a.table_name) = 'ITEMS' THEN i.item_id
                    WHEN UPPER(a.table_name) = 'CLAIMS' THEN c.item_id
                    ELSE NULL
                END AS item_id,
                CASE
                    WHEN UPPER(a.table_name) = 'ITEMS' THEN iu.name
                    WHEN UPPER(a.table_name) = 'CLAIMS' THEN cu.name
                    ELSE NULL
                END AS related_user
            FROM audit_logs a
            LEFT JOIN items i
                ON UPPER(a.table_name) = 'ITEMS' AND i.item_id = a.record_id
            LEFT JOIN users iu
                ON iu.user_id = i.user_id
            LEFT JOIN claims c
                ON UPPER(a.table_name) = 'CLAIMS' AND c.claim_id = a.record_id
            LEFT JOIN items ci
                ON ci.item_id = c.item_id
            LEFT JOIN users cu
                ON cu.user_id = c.user_id
            ORDER BY a.action_date DESC, a.audit_id DESC
        ";

        if ($limit !== null) {
            $sql .= ' LIMIT '.(int) $limit;
        }

        return DB::select($sql);
    }

    public static function presentEvent(object $row): object
    {
        $entity = strtoupper((string) $row->table_name) === 'CLAIMS' ? 'claim' : 'item';
        $action = strtoupper((string) $row->action_type);
        $name = $row->subject_name
            ?: ($entity === 'claim' ? 'Claim #'.$row->record_id : 'Item #'.$row->record_id);
        $user = $row->related_user;
        $old = $row->old_status;
        $new = $row->new_status;
        $statusChanged = $old && $new && $old !== $new;

        if ($entity === 'item') {
            [$headline, $detail, $tone] = match ($action) {
                'INSERT' => [
                    'New item posted',
                    $user ? "{$name} was listed by {$user}." : "{$name} was added to the board.",
                    'create',
                ],
                'DELETE' => [
                    'Item removed',
                    "{$name} was deleted from the board.",
                    'danger',
                ],
                default => $statusChanged
                    ? [
                        'Item status changed',
                        "{$name} moved from {$old} to {$new}.",
                        'update',
                    ]
                    : [
                        'Item updated',
                        "{$name} was edited".($user ? " (owner: {$user})" : '').'.',
                        'neutral',
                    ],
            };
        } else {
            [$headline, $detail, $tone] = match ($action) {
                'INSERT' => [
                    'Claim submitted',
                    $user
                        ? "{$user} claimed ownership of {$name}."
                        : "A claim was filed for {$name}.",
                    'create',
                ],
                'DELETE' => [
                    'Claim removed',
                    "A claim on {$name} was deleted.",
                    'danger',
                ],
                default => match (strtoupper((string) $new)) {
                    'APPROVED' => [
                        'Claim approved',
                        "Ownership of {$name} was approved".($user ? " for {$user}" : '').'.',
                        'success',
                    ],
                    'REJECTED' => [
                        'Claim rejected',
                        "The claim on {$name}".($user ? " by {$user}" : '').' was rejected.',
                        'danger',
                    ],
                    default => $statusChanged
                        ? [
                            'Claim status changed',
                            "Claim on {$name} moved from {$old} to {$new}.",
                            'update',
                        ]
                        : [
                            'Claim updated',
                            "Claim details for {$name} were updated.",
                            'neutral',
                        ],
                },
            };
        }

        $when = Carbon::parse($row->action_date);

        return (object) [
            'audit_id' => $row->audit_id,
            'entity' => $entity,
            'action' => $action,
            'tone' => $tone,
            'headline' => $headline,
            'detail' => $detail,
            'subject' => $name,
            'item_id' => $row->item_id,
            'item_type' => $row->item_type,
            'record_id' => $row->record_id,
            'old_status' => $old,
            'new_status' => $new,
            'status_changed' => $statusChanged,
            'actor' => self::friendlyActor($row->action_by),
            'when_label' => $when->diffForHumans(),
            'when_exact' => $when->format('M j, Y · g:i A'),
            'is_today' => $when->isToday(),
        ];
    }

    protected static function friendlyActor(?string $actor): string
    {
        if (! $actor) {
            return 'Unknown';
        }

        if (str_contains($actor, '@localhost') || str_contains($actor, '@%')) {
            return 'System (database)';
        }

        return $actor;
    }
}
