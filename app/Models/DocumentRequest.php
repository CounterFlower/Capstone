<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
	protected $table = 'document_request';
	protected $primaryKey = 'Request_ID';
	public $timestamps = false;

	protected $fillable = [
		'Resident_ID',
		'Date_Requested',
		'Years_Stayed',
		'Document_Type',
		'Purpose',
		'Status',
		'Pickup_Schedule',
		'QR_Hash',
		'Processed_By',
	];

	protected $casts = [
		'Date_Requested' => 'datetime',
		'Years_Stayed' => 'integer',
		'Pickup_Schedule' => 'datetime',
	];

	public function resident()
	{
		return $this->belongsTo(Resident::class, 'Resident_ID', 'Resident_ID');
	}

	public function processedBy()
	{
		return $this->belongsTo(SystemUser::class, 'Processed_By', 'User_ID');
	}
}
