<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $recentItems = Item::query()
            ->with(['user', 'category', 'location'])
            ->latest('created_at')
            ->limit(6)
            ->get();

        $boardStats = (object) [
            'total_items' => Item::query()->count(),
            'reunited' => Item::query()->whereIn('status', ['CLAIMED', 'RETURNED'])->count(),
            'locations' => \App\Models\Location::query()->count(),
        ];

        return view('home', compact('recentItems', 'boardStats'));
    }
}
