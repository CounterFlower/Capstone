<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement', function (Blueprint $table) {
            $table->integer('Announcement_ID')->autoIncrement();
            $table->string('Title', 255);
            $table->text('Content');
            $table->dateTime('Date_Posted')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('Posted_By')->nullable();

            $table->foreign('Posted_By')
                  ->references('User_ID')
                  ->on('system_user')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement');
    }
};