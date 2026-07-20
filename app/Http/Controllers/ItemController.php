<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class ItemController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(Request $request): View
    {
        $query = Item::query()
            ->with(['user', 'category', 'location'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = '%'.$request->input('q').'%';
            $query->whereRaw('LOWER(item_name) LIKE LOWER(?)', [$q]);
        }

        if ($request->filled('type')) {
            $query->where('item_type', strtoupper($request->input('type')));
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->input('status')));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', (int) $request->input('location_id'));
        }

        $items = $query->get();
        $categories = Category::query()->orderBy('category_name')->get();
        $locations = Location::query()->orderBy('location_name')->get();
        $boardView = $request->cookie(PreferenceController::COOKIE_BOARD_VIEW, 'comfortable');

        return view('items.index', compact('items', 'categories', 'locations', 'boardView'));
    }

    public function show(Request $request, Item $item): View
    {
        $item->load(['user', 'category', 'location']);

        if ($item->location) {
            $item->location->ensureCoordinates();
        }

        $recent = collect(json_decode($request->cookie('findit_recent_items', '[]'), true) ?: []);
        $recent = $recent->reject(fn ($id) => (int) $id === (int) $item->item_id)->prepend($item->item_id)->take(8)->values();
        Cookie::queue(Cookie::make('findit_recent_items', $recent->toJson(), 60 * 24 * 30, '/', null, false, false, false, 'lax'));

        return view('items.show', compact('item'));
    }

    public function create(): View
    {
        $this->authorize('create', Item::class);

        $categories = Category::query()->orderBy('category_name')->get();
        $locations = Location::query()->orderBy('location_name')->get();

        return view('items.create', compact('categories', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Item::class);

        $data = $request->validate([
            'item_name' => ['required', 'string', 'max:100'],
            'item_description' => ['nullable', 'string', 'max:500'],
            'item_type' => ['required', 'in:LOST,FOUND'],
            'category_id' => ['required', 'integer', 'exists:categories,category_id'],
            'location_id' => ['required', 'integer', 'exists:locations,location_id'],
            'lost_or_found_date' => ['required', 'date'],
            'item_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;

        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('items', 'public');
        }

        try {
            $this->plsql->addItem([
                'user_id' => Auth::id(),
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

        return redirect()->route('items.mine')->with('success', 'Your item has been posted.');
    }

    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        $categories = Category::query()->orderBy('category_name')->get();
        $locations = Location::query()->orderBy('location_name')->get();

        return view('items.edit', compact('item', 'categories', 'locations'));
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $data = $request->validate([
            'item_name' => ['required', 'string', 'max:100'],
            'item_description' => ['nullable', 'string', 'max:500'],
            'item_type' => ['required', 'in:LOST,FOUND'],
            'category_id' => ['required', 'integer', 'exists:categories,category_id'],
            'location_id' => ['required', 'integer', 'exists:locations,location_id'],
            'lost_or_found_date' => ['required', 'date'],
            'status' => ['required', 'in:PENDING,FOUND,CLAIMED,RETURNED,REJECTED'],
            'item_image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('item_image')) {
            if ($item->item_image) {
                Storage::disk('public')->delete($item->item_image);
            }
            $data['item_image'] = $request->file('item_image')->store('items', 'public');
        }

        $item->update([
            'item_name' => $data['item_name'],
            'item_description' => $data['item_description'] ?? null,
            'item_type' => strtoupper($data['item_type']),
            'category_id' => (int) $data['category_id'],
            'location_id' => (int) $data['location_id'],
            'lost_or_found_date' => $data['lost_or_found_date'],
            'status' => strtoupper($data['status']),
            'item_image' => $data['item_image'] ?? $item->item_image,
        ]);

        return redirect()->route('items.mine')->with('success', 'Your changes have been saved.');
    }

    public function myItems(): View
    {
        $items = Item::query()
            ->with(['category', 'location'])
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->get();

        return view('items.mine', compact('items'));
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        try {
            $this->plsql->deleteItem((int) $item->item_id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('items.mine')->with('success', 'The item has been removed.');
    }
}
