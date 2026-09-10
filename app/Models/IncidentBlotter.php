<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentBlotter extends Model
{
    use HasFactory;

    protected $table = 'incident_blotter';
    protected $primaryKey = 'Incident_ID';
    public $timestamps = false; // The schema handles Date_Reported with DEFAULT current_timestamp()

    protected $fillable = [
        'Complainant_Id',
        'Respondent_Id',
        'Guest_Id',
        'Category_Id',
        'Description',
        'Requested_Relief',
        'Date_Reported',
        'Date_Filed',
        'Resolution_Status',
        'Latitude',
        'Longitude',
        'Handled_By',
    ];

    protected $casts = [
        'Latitude' => 'float',
        'Longitude' => 'float',
        'Date_Reported' => 'datetime',
        'Date_Filed' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(IncidentType::class, 'Category_Id', 'Category_Id');
    }

    public function complainant()
    {
        return $this->belongsTo(Resident::class, 'Complainant_Id', 'Resident_ID');
    }

    public function respondent()
    {
        return $this->belongsTo(Resident::class, 'Respondent_Id', 'Resident_ID');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'Guest_Id', 'Guest_Id');
    }
}