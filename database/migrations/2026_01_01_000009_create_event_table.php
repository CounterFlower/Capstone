<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event', function (Blueprint $table) {
            $table->integer('Event_ID')->autoIncrement();
            $table->string('Event_Name', 255);
            $table->date('Event_Date');
            $table->time('Start_Time')->nullable();
            $table->date('End_Date')->nullable();
            $table->time('End_Time')->nullable();
            $table->string('Location', 255)->nullable();
            $table->integer('Available_Slots')->nullable();
            $table->integer('Created_By')->nullable();
            $table->text('Summary')->nullable();
            $table->string('Cover_Image', 255)->nullable();

            $table->foreign('Created_By')
                  ->references('User_ID')
                  ->on('system_user')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};