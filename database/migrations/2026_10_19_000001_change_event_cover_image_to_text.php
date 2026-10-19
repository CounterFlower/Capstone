<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->text('Cover_Image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->string('Cover_Image', 255)->nullable()->change();
        });
    }
};
