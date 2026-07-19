<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PreferenceController extends Controller
{
    public const COOKIE_BOARD_VIEW = 'findit_board_view';

    public function updateBoardView(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'board_view' => ['required', 'in:comfortable,compact'],
        ]);

        $minutes = 60 * 24 * 365;

        Cookie::queue(
            Cookie::make(self::COOKIE_BOARD_VIEW, $data['board_view'], $minutes, '/', null, false, false, false, 'lax')
        );

        return back()->with('success', 'Display preference updated.');
    }
}
