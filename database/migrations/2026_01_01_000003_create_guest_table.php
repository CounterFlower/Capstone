<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest', function (Blueprint $table) {
            $table->integer('Guest_Id')->autoIncrement();
            $table->string('First_Name', 225)->nullable();
            $table->string('Middle_Name', 225)->nullable();
            $table->string('Last_Name', 225)->nullable();
            $table->string('Contact_Number', 100)->nullable();
            $table->text('Address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest');
    }
};