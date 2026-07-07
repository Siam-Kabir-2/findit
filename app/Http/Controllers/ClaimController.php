<?php

namespace App\Http\Controllers;

use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class ClaimController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function store(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'claim_message' => ['nullable', 'string', 'max:500'],
            'proof_description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->plsql->submitClaim(
                $id,
                (int) Auth::guard('web')->id(),
                $data['claim_message'] ?? null,
                $data['proof_description'] ?? null
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['claim_message' => $e->getMessage()]);
        }

        return redirect()->route('claims.mine')->with('success', 'Claim submitted successfully.');
    }

    public function myClaims(): View
    {
        $userId = Auth::guard('web')->id();

        $claims = DB::select("
            SELECT c.*, i.item_name, i.item_type, i.status AS item_status
            FROM claims c
            JOIN items i ON i.item_id = c.item_id
            WHERE c.user_id = :user_id
            ORDER BY c.created_at DESC
        ", ['user_id' => $userId]);

        return view('claims.mine', compact('claims'));
    }
}
