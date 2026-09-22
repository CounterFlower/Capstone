<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUser extends Model
{
    protected $table = 'system_user';
    protected $primaryKey = 'User_ID';
    public $timestamps = false;

    protected $fillable = [
        'Username',
        'Password_Hash',
        'Role',
        'Full_Name',
        'Is_Active',
    ];

    protected $hidden = [
        'Password_Hash',
    ];

    protected $casts = [
        'Is_Active' => 'boolean',
    ];

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'User_ID', 'User_ID');
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'Processed_By', 'User_ID');
    }
}
