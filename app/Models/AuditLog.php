<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';
    protected $primaryKey = 'Log_ID';
    public $timestamps = false;

    protected $fillable = [
        'User_ID',
        'Action_Performed',
        'Log_Timestamp',
    ];

    protected $casts = [
        'Log_Timestamp' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(SystemUser::class, 'User_ID', 'User_ID');
    }
}
