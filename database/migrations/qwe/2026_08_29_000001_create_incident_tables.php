<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_types', function (Blueprint $table) {
            $table->increments('Category_Id');
            $table->string('Category')->nullable();
        });

        Schema::create('incident_blotter', function (Blueprint $table) {
            $table->increments('Incident_ID');
            $table->unsignedInteger('Complainant_Id')->nullable();
            $table->unsignedInteger('Respondent_Id')->nullable();
            $table->unsignedInteger('Guest_Id')->nullable();
            $table->unsignedInteger('Category_Id');
            $table->text('Description');
            $table->text('Requested_Relief')->nullable();
            $table->dateTime('Date_Reported')->nullable()->useCurrent();
            $table->dateTime('Date_Filed')->nullable();
            $table->enum('Resolution_Status', ['Pending', 'Active', 'Resolved', 'Escalated'])->nullable()->default('Pending');
            $table->decimal('Latitude', 10, 8)->nullable();
            $table->decimal('Longitude', 11, 8)->nullable();
            $table->unsignedInteger('Handled_By')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_blotter');
        Schema::dropIfExists('incident_types');
    }
};
