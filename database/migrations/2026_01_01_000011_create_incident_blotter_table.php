<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_blotter', function (Blueprint $table) {
            $table->integer('Incident_ID')->autoIncrement();
            $table->integer('Complainant_Id')->nullable();
            $table->integer('Respondent_Id');
            $table->integer('Guest_Id')->nullable();
            $table->integer('Category_Id');
            $table->text('Description');
            $table->text('Requested_Relief')->nullable();
            $table->dateTime('Date_Reported')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('Date_Filed')->nullable();
            $table->enum('Resolution_Status', ['Pending', 'Active', 'Resolved', 'Escalated'])->default('Pending');
            $table->decimal('Latitude', 10, 8)->nullable();
            $table->decimal('Longitude', 11, 8)->nullable();
            $table->integer('Handled_By')->nullable();

            $table->foreign('Complainant_Id')
                  ->references('Resident_ID')
                  ->on('resident')
                  ->onDelete('cascade');

            $table->foreign('Respondent_Id')
                  ->references('Resident_ID')
                  ->on('resident');

            $table->foreign('Guest_Id')
                  ->references('Guest_Id')
                  ->on('guest');

            $table->foreign('Category_Id')
                  ->references('Category_Id')
                  ->on('incident_types');

            $table->foreign('Handled_By')
                  ->references('User_ID')
                  ->on('system_user')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_blotter');
    }
};