@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('heading', 'Audit Logs')

@section('content')
<p class="meta" style="margin-bottom:1rem;">Populated by MySQL triggers on ITEMS and CLAIMS.</p>
<div class="panel table-wrap">
    <table class="data">
        <thead>
        <tr>
            <th>ID</th>
            <th>Table</th>
            <th>Record</th>
            <th>Action</th>
            <th>Old</th>
            <th>New</th>
            <th>By</th>
            <th>When</th>
        </tr>
        </thead>
        <tbody>
        @forelse($auditLogs as $log)
            <tr>
                <td>{{ $log->audit_id }}</td>
                <td>{{ $log->table_name }}</td>
                <td>{{ $log->record_id }}</td>
                <td>{{ $log->action_type }}</td>
                <td>{{ $log->old_status }}</td>
                <td>{{ $log->new_status }}</td>
                <td>{{ $log->action_by }}</td>
                <td>{{ $log->action_date }}</td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty">No audit entries yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
