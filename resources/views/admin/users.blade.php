@extends('layouts.admin')

@section('title', 'Users')
@section('heading', 'Manage Users')

@section('content')
<div class="panel table-wrap">
    <table class="data">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->user_id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->address }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.users.destroy', $user->user_id) }}" onsubmit="return confirm('Delete this user?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">No users found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
