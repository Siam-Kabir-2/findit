<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $primaryKey = 'audit_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'table_name',
        'record_id',
        'action_type',
        'old_status',
        'new_status',
        'action_date',
        'action_by',
    ];
}
