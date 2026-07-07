<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAuditController extends Controller
{
    public function index(): View
    {
        $auditLogs = DB::select('SELECT * FROM audit_logs ORDER BY action_date DESC');

        return view('admin.audit', compact('auditLogs'));
    }
}
