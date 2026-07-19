<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use App\Services\FinditPlsqlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class ClaimController extends Controller
{
    public function __construct(
        protected FinditPlsqlService $plsql
    ) {}

    public function store(Request $request, Item $item): RedirectResponse
    {
        $data = $request->validate([
            'claim_message' => ['nullable', 'string', 'max:500'],
            'proof_description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->plsql->submitClaim(
                (int) $item->item_id,
                (int) Auth::id(),
                $data['claim_message'] ?? null,
                $data['proof_description'] ?? null
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['claim_message' => $e->getMessage()]);
        }

        return redirect()->route('claims.mine')->with('success', 'Your claim has been submitted for review.');
    }

    public function myClaims(): View
    {
        $claims = Claim::query()
            ->with('item')
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->get();

        return view('claims.mine', compact('claims'));
    }
}
