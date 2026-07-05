<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $table = 'locations';

    protected $primaryKey = 'location_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'location_name',
        'description',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'location_id', 'location_id');
    }
}
