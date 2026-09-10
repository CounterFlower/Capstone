<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident', function (Blueprint $table) {
            $table->integer('Resident_ID')->autoIncrement();
            $table->integer('Household_Index')->nullable();
            $table->string('First_Name', 255);
            $table->string('Middle_Name', 255)->nullable();
            $table->string('Last_Name', 255);
            $table->date('Date_of_Birth')->nullable();
            $table->string('Gender', 20)->nullable();
            $table->string('Contact_Number', 50)->nullable();
            $table->tinyInteger('Is_Verified')->default(0);
            $table->string('Place_of_Birth', 255)->nullable();
            $table->enum('Civil_Status', ['Single', 'Married', 'Widowed'])->nullable();

            $table->foreign('Household_Index')
                  ->references('Household_Index')
                  ->on('household')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident');
    }
};