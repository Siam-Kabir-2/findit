<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $stats = (object) [
            'total_items' => Item::query()->where('user_id', $userId)->count(),
            'total_claims' => Auth::user()->claims()->count(),
            'pending_claims' => Auth::user()->claims()->where('claim_status', 'PENDING')->count(),
            'approved_claims' => Auth::user()->claims()->where('claim_status', 'APPROVED')->count(),
        ];

        $recentItems = Item::query()
            ->with(['category', 'location'])
            ->where('user_id', $userId)
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.user', compact('stats', 'recentItems'));
    }
}
