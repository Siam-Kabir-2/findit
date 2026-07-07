<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class AdminItemController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(): View
    {
        $items = DB::select("
            SELECT i.*, u.name AS user_name, c.category_name, l.location_name
            FROM items i
            JOIN users u ON u.user_id = i.user_id
            JOIN categories c ON c.category_id = i.category_id
            JOIN locations l ON l.location_id = i.location_id
            ORDER BY i.created_at DESC
        ");

        return view('admin.items', compact('items'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:PENDING,FOUND,CLAIMED,RETURNED,REJECTED'],
        ]);

        try {
            $this->plsql->updateItemStatus($id, strtoupper($data['status']));
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Item status updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->plsql->deleteItem($id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Item deleted.');
    }
}
