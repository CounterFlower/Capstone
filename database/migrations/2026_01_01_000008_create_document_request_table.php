<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_request', function (Blueprint $table) {
            $table->integer('Request_ID')->autoIncrement();
            $table->integer('Resident_ID');
            $table->dateTime('Date_Requested')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('Years_Stayed')->nullable();
            $table->string('Document_Type', 100);
            $table->string('Purpose', 255)->nullable();
            $table->string('Status', 50)->default('Pending');
            $table->dateTime('Pickup_Schedule')->nullable();
            $table->string('QR_Hash', 255)->nullable();
            $table->integer('Processed_By')->nullable();

            $table->foreign('Resident_ID')
                  ->references('Resident_ID')
                  ->on('resident')
                  ->onDelete('cascade');

            $table->foreign('Processed_By')
                  ->references('User_ID')
                  ->on('system_user')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_request');
    }
};