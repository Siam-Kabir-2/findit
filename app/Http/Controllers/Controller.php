<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class Controller
{
    use AuthorizesRequests;

    protected function attemptFinditLogin(string $guard, Authenticatable $user, string $password): bool
    {
        $stored = (string) $user->getAuthPassword();
        $isHashed = str_starts_with($stored, '$2y$') || str_starts_with($stored, '$2a$') || str_starts_with($stored, '$2b$');

        if ($isHashed && Hash::check($password, $stored)) {
            Auth::guard($guard)->login($user);

            return true;
        }

        if (! $isHashed && hash_equals($stored, $password)) {
            $hashed = Hash::make($password);
            $table = $user->getTable();
            $key = $user->getAuthIdentifierName();
            $id = $user->getAuthIdentifier();

            DB::update("UPDATE {$table} SET password = :password WHERE {$key} = :id", [
                'password' => $hashed,
                'id' => $id,
            ]);

            $user->setRawAttributes(array_merge($user->getAttributes(), ['password' => $hashed]));
            Auth::guard($guard)->login($user);

            return true;
        }

        return false;
    }
}
