<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinditPlsqlService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(): View
    {
        $stats = $this->plsql->dashboardStats();

        $recentClaims = DB::select("
            SELECT c.claim_id, c.claim_status, c.created_at,
                   u.name AS claimant_name, i.item_name
            FROM claims c
            JOIN users u ON u.user_id = c.user_id
            JOIN items i ON i.item_id = c.item_id
            ORDER BY c.created_at DESC
        ");
        $recentClaims = array_slice($recentClaims, 0, 6);

        $recentItems = DB::select("
            SELECT i.item_id, i.item_name, i.item_type, i.status, i.created_at,
                   u.name AS user_name
            FROM items i
            JOIN users u ON u.user_id = i.user_id
            ORDER BY i.created_at DESC
        ");
        $recentItems = array_slice($recentItems, 0, 6);

        $recentAudit = DB::select("
            SELECT audit_id, table_name, record_id, action_type, old_status, new_status, action_by, action_date
            FROM audit_logs
            ORDER BY action_date DESC
        ");
        $recentAudit = array_slice($recentAudit, 0, 8);

        return view('admin.dashboard', compact('stats', 'recentClaims', 'recentItems', 'recentAudit'));
    }
}
