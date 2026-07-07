<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class ItemController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(Request $request): View
    {
        $sql = "
            SELECT i.*, u.name AS user_name, c.category_name, l.location_name
            FROM items i
            JOIN users u ON u.user_id = i.user_id
            JOIN categories c ON c.category_id = i.category_id
            JOIN locations l ON l.location_id = i.location_id
            WHERE 1=1
        ";
        $bindings = [];

        if ($request->filled('q')) {
            $sql .= ' AND LOWER(i.item_name) LIKE LOWER(:q)';
            $bindings['q'] = '%'.$request->input('q').'%';
        }

        if ($request->filled('type')) {
            $sql .= ' AND i.item_type = :type';
            $bindings['type'] = strtoupper($request->input('type'));
        }

        if ($request->filled('status')) {
            $sql .= ' AND i.status = :status';
            $bindings['status'] = strtoupper($request->input('status'));
        }

        if ($request->filled('category_id')) {
            $sql .= ' AND i.category_id = :category_id';
            $bindings['category_id'] = (int) $request->input('category_id');
        }

        if ($request->filled('location_id')) {
            $sql .= ' AND i.location_id = :location_id';
            $bindings['location_id'] = (int) $request->input('location_id');
        }

        $sql .= ' ORDER BY i.created_at DESC';

        $items = DB::select($sql, $bindings);
        $categories = DB::select('SELECT * FROM categories ORDER BY category_name');
        $locations = DB::select('SELECT * FROM locations ORDER BY location_name');

        return view('items.index', compact('items', 'categories', 'locations'));
    }

    public function show(int $id): View
    {
        $rows = DB::select("
            SELECT i.*, u.name AS user_name, u.email AS user_email, c.category_name, l.location_name
            FROM items i
            JOIN users u ON u.user_id = i.user_id
            JOIN categories c ON c.category_id = i.category_id
            JOIN locations l ON l.location_id = i.location_id
            WHERE i.item_id = :id
        ", ['id' => $id]);

        abort_if(empty($rows), 404);

        $item = $rows[0];

        return view('items.show', compact('item'));
    }

    public function create(): View
    {
        $categories = DB::select('SELECT * FROM categories ORDER BY category_name');
        $locations = DB::select('SELECT * FROM locations ORDER BY location_name');

        return view('items.create', compact('categories', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_name' => ['required', 'string', 'max:100'],
            'item_description' => ['nullable', 'string', 'max:500'],
            'item_type' => ['required', 'in:LOST,FOUND'],
            'category_id' => ['required', 'integer'],
            'location_id' => ['required', 'integer'],
            'lost_or_found_date' => ['required', 'date'],
            'item_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;

        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('items', 'public');
        }

        try {
            $this->plsql->addItem([
                'user_id' => Auth::guard('web')->id(),
                'category_id' => (int) $data['category_id'],
                'location_id' => (int) $data['location_id'],
                'item_name' => $data['item_name'],
                'item_description' => $data['item_description'] ?? null,
                'item_type' => strtoupper($data['item_type']),
                'item_image' => $imagePath,
                'lost_or_found_date' => $data['lost_or_found_date'],
            ]);
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors(['item_name' => $e->getMessage()]);
        }

        return redirect()->route('items.mine')->with('success', 'Item posted successfully.');
    }

    public function myItems(): View
    {
        $userId = Auth::guard('web')->id();

        $items = DB::select("
            SELECT i.*, c.category_name, l.location_name
            FROM items i
            JOIN categories c ON c.category_id = i.category_id
            JOIN locations l ON l.location_id = i.location_id
            WHERE i.user_id = :user_id
            ORDER BY i.created_at DESC
        ", ['user_id' => $userId]);

        return view('items.mine', compact('items'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = Item::find($id);

        abort_if(! $item, 404);

        if ((int) $item->user_id !== (int) Auth::guard('web')->id()) {
            abort(403);
        }

        try {
            $this->plsql->deleteItem($id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('items.mine')->with('success', 'Item deleted successfully.');
    }
}
