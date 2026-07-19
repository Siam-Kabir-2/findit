<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class AdminClaimController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(Request $request): View
    {
        $status = strtoupper((string) $request->query('status', 'PENDING'));
        $allowed = ['ALL', 'PENDING', 'APPROVED', 'REJECTED'];
        if (! in_array($status, $allowed, true)) {
            $status = 'PENDING';
        }

        $sql = "
            SELECT c.*, u.name AS claimant_name, u.email AS claimant_email,
                   i.item_name, i.item_type, i.status AS item_status
            FROM claims c
            JOIN users u ON u.user_id = c.user_id
            JOIN items i ON i.item_id = c.item_id
            WHERE 1=1
        ";
        $bindings = [];

        if ($status !== 'ALL') {
            $sql .= ' AND c.claim_status = :status';
            $bindings['status'] = $status;
        }

        $sql .= ' ORDER BY c.created_at DESC';

        $claims = DB::select($sql, $bindings);

        $counts = DB::selectOne("
            SELECT
                COUNT(*) AS total_all,
                SUM(CASE WHEN claim_status = 'PENDING' THEN 1 ELSE 0 END) AS total_pending,
                SUM(CASE WHEN claim_status = 'APPROVED' THEN 1 ELSE 0 END) AS total_approved,
                SUM(CASE WHEN claim_status = 'REJECTED' THEN 1 ELSE 0 END) AS total_rejected
            FROM claims
        ");

        return view('admin.claims', compact('claims', 'status', 'counts'));
    }

    public function approve(int $id): RedirectResponse
    {
        $adminName = Auth::guard('admin')->user()->name;

        try {
            $this->plsql->approveClaim($id, $adminName);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Claim approved. The item is now marked as claimed.');
    }

    public function reject(int $id): RedirectResponse
    {
        $adminName = Auth::guard('admin')->user()->name;

        try {
            $this->plsql->rejectClaim($id, $adminName);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'The claim has been rejected.');
    }
}
