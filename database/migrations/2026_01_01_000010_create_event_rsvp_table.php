<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_rsvp', function (Blueprint $table) {
            $table->integer('RSVP_ID')->autoIncrement();
            $table->integer('Event_ID');
            $table->integer('Resident_ID');
            $table->dateTime('Date_Registered')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('Attendance_Status', 50)->default('Confirmed');

            $table->foreign('Event_ID')
                  ->references('Event_ID')
                  ->on('event')
                  ->onDelete('cascade');

            $table->foreign('Resident_ID')
                  ->references('Resident_ID')
                  ->on('resident')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_rsvp');
    }
};