<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return 'user_id';
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'user_id', 'user_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'user_id', 'user_id');
    }
}
