<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('household', function (Blueprint $table) {
            $table->increments('Household_Index');
            $table->unsignedInteger('Household_Id')->nullable();
            $table->string('House_Number')->nullable();
            $table->string('Zone_Purok')->nullable();
        });

        Schema::create('resident', function (Blueprint $table) {
            $table->increments('Resident_ID');
            $table->unsignedInteger('Household_Index')->nullable();
            $table->string('First_Name');
            $table->string('Middle_Name')->nullable();
            $table->string('Last_Name');
            $table->date('Date_of_Birth')->nullable();
            $table->string('Gender')->nullable();
            $table->string('Contact_Number')->nullable();
            $table->boolean('Is_Verified')->default(false);
            $table->string('Place_of_Birth')->nullable();
            $table->enum('Civil_Status', ['Single', 'Married', 'Widowed'])->nullable();
        });

        Schema::create('event_rsvp', function (Blueprint $table) {
            $table->increments('RSVP_ID');
            $table->unsignedInteger('Event_ID');
            $table->unsignedInteger('Resident_ID');
            $table->dateTime('Date_Registered')->nullable()->useCurrent();
            $table->string('Attendance_Status')->default('Confirmed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_rsvp');
        Schema::dropIfExists('resident');
        Schema::dropIfExists('household');
    }
};
