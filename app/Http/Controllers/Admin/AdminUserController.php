<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class AdminUserController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function index(): View
    {
        $users = DB::select('SELECT user_id, name, email, phone, address, created_at FROM users ORDER BY user_id');

        return view('admin.users', compact('users'));
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->plsql->deleteUser($id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'User deleted.');
    }
}
