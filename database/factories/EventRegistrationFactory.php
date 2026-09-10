<?php

namespace Database\Factories;

use App\Models\EventRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventRegistration>
 */
class EventRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => 'EVT-'.fake()->unique()->bothify('########'),
            'resident_name' => fake()->name(),
            'contact_number' => fake()->numerify('09#########'),
            'purok' => 'Purok '.fake()->numberBetween(1, 5),
            'event_id' => 'barangay-assembly',
            'event_title' => 'Barangay Assembly',
            'event_date' => 'May 16, 2026',
            'event_time' => '2:00 PM',
            'submitted_at' => fake()->dateTimeBetween('-1 week'),
        ];
    }
}
