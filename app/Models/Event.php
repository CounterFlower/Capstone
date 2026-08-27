<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';

    public $timestamps = false;

    protected $fillable = [
        'Event_Name',
        'Event_Date',
        'Location',
        'Available_Slots',
        'Summary',
        'Created_By',
    ];

    protected $casts = [
        'Event_Date' => 'datetime',
        'Available_Slots' => 'integer',
    ];

    public function getTitleAttribute(): ?string
    {
        return $this->attributes['Event_Name'] ?? null;
    }

    public function setTitleAttribute($value): void
    {
        $this->attributes['Event_Name'] = $value;
    }

    public function getDateAttribute(): ?string
    {
        return $this->attributes['Event_Date'] ?? null;
    }

    public function setDateAttribute($value): void
    {
        $this->attributes['Event_Date'] = $value;
    }

    public function getTimeAttribute(): ?string
    {
        if (! isset($this->attributes['Event_Date'])) {
            return null;
        }

        return Carbon::parse($this->attributes['Event_Date'])->format('H:i');
    }

    public function setTimeAttribute($value): void
    {
        if (isset($this->attributes['Event_Date'])) {
            $this->attributes['Event_Date'] = Carbon::parse($this->attributes['Event_Date'])->setTimeFromTimeString($value)->format('Y-m-d H:i:s');
            return;
        }

        $this->attributes['Event_Date'] = Carbon::parse('2000-01-01 '.$value)->format('Y-m-d H:i:s');
    }

    public function getVenueAttribute(): ?string
    {
        return $this->attributes['Location'] ?? null;
    }

    public function setVenueAttribute($value): void
    {
        $this->attributes['Location'] = $value;
    }

    public function getSummaryAttribute(): ?string
    {
        return $this->attributes['Summary'] ?? null;
    }

    public function setSummaryAttribute($value): void
    {
        $this->attributes['Summary'] = $value;
    }
}
