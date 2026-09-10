<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->integer('Log_ID')->autoIncrement();
            $table->integer('User_ID')->nullable();
            $table->string('Action_Performed', 255);
            $table->dateTime('Log_Timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('User_ID')
                  ->references('User_ID')
                  ->on('system_user')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};