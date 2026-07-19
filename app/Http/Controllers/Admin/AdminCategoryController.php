<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class AdminCategoryController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(): View
    {
        $categories = DB::select('SELECT * FROM categories ORDER BY category_name');

        return view('admin.categories', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:100'],
        ]);

        try {
            $this->plsql->addCategory($data['category_name']);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['category_name' => $e->getMessage()]);
        }

        return back()->with('success', 'Category added.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->plsql->deleteCategory($id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Category removed.');
    }
}
