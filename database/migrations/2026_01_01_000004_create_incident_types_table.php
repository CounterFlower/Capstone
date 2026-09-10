<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_types', function (Blueprint $table) {
            $table->integer('Category_Id')->autoIncrement();
            $table->string('Category', 225)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_types');
    }
};