<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $recentItems = DB::select("
            SELECT i.*, u.name AS user_name, c.category_name, l.location_name
            FROM items i
            JOIN users u ON u.user_id = i.user_id
            JOIN categories c ON c.category_id = i.category_id
            JOIN locations l ON l.location_id = i.location_id
            ORDER BY i.created_at DESC
        ");

        $recentItems = array_slice($recentItems, 0, 8);

        return view('home', compact('recentItems'));
    }
}
