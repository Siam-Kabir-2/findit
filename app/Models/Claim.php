<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    protected $table = 'claims';

    protected $primaryKey = 'claim_id';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'user_id',
        'claim_message',
        'proof_description',
        'claim_status',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
