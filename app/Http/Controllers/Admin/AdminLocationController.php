<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class AdminLocationController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(): View
    {
        $locations = DB::select('SELECT * FROM locations ORDER BY location_name');

        return view('admin.locations', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'location_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->plsql->addLocation(
                $data['location_name'],
                $data['description'] ?? null
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['location_name' => $e->getMessage()]);
        }

        return back()->with('success', 'Location added.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->plsql->deleteLocation($id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Location removed.');
    }
}
