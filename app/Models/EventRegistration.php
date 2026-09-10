<?php

namespace App\Models;

use Database\Factories\EventRegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $reference
 * @property string $resident_name
 * @property string $contact_number
 * @property string $purok
 * @property string $event_id
 * @property string $event_title
 * @property string $event_date
 * @property string $event_time
 * @property Carbon $submitted_at
 */
#[Fillable(['reference', 'resident_name', 'contact_number', 'purok', 'event_id', 'event_title', 'event_date', 'event_time', 'submitted_at'])]
class EventRegistration extends Model
{
    /** @use HasFactory<EventRegistrationFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }
}
