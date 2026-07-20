@extends('layouts.admin')

@section('title', 'Users')
@section('heading', 'Manage Users')

@section('content')
<div class="panel table-wrap">
    <div style="margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h3 class="font-display" style="margin:0;font-size:1.15rem;">Registered Users</h3>
            <p class="meta" style="margin:0;margin-top:0.25rem;">Directory of all students, faculty, and staff.</p>
        </div>
        <span class="badge" style="background:var(--paper-2);color:var(--ink);">{{ count($users) }} total users</span>
    </div>
    
    @if(count($users))
        <table class="data">
            <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Address</th>
                <th style="width: 100px; text-align: right;">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="meta">#{{ $user->user_id }}</td>
                    <td>
                        <strong>{{ $user->name }}</strong>
                    </td>
                    <td><a href="mailto:{{ $user->email }}" class="link-accent">{{ $user->email }}</a></td>
                    <td style="color:var(--ink-soft);">{{ $user->phone ?: '—' }}</td>
                    <td style="color:var(--ink-soft);">{{ $user->address ?: '—' }}</td>
                    <td style="text-align: right;">
                        <form method="POST" action="{{ route('admin.users.destroy', $user->user_id) }}" onsubmit="return confirm('Are you sure you want to delete this user? Their items and claims will be removed.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <h3>No users found</h3>
            <p>No one has registered on the platform yet.</p>
        </div>
    @endif
</div>
@endsection
