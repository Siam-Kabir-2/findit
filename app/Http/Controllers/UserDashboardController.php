<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::guard('web')->id();

        $stats = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM items WHERE user_id = :uid1) AS total_items,
                (SELECT COUNT(*) FROM claims WHERE user_id = :uid2) AS total_claims,
                (SELECT COUNT(*) FROM claims WHERE user_id = :uid3 AND claim_status = 'PENDING') AS pending_claims,
                (SELECT COUNT(*) FROM claims WHERE user_id = :uid4 AND claim_status = 'APPROVED') AS approved_claims
            FROM dual
        ", [
            'uid1' => $userId,
            'uid2' => $userId,
            'uid3' => $userId,
            'uid4' => $userId,
        ]);

        $recentItems = DB::select("
            SELECT i.*, c.category_name, l.location_name
            FROM items i
            JOIN categories c ON c.category_id = i.category_id
            JOIN locations l ON l.location_id = i.location_id
            WHERE i.user_id = :user_id
            ORDER BY i.created_at DESC
        ", ['user_id' => $userId]);

        $recentItems = array_slice($recentItems, 0, 5);

        return view('dashboard.user', compact('stats', 'recentItems'));
    }
}
