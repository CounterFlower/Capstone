<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_user', function (Blueprint $table) {
            $table->integer('User_ID')->autoIncrement();
            $table->string('Username', 100)->unique();
            $table->string('Password_Hash', 255);
            $table->string('Role', 50);
            $table->string('Full_Name', 255)->nullable();
            $table->tinyInteger('Is_Active')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_user');
    }
};