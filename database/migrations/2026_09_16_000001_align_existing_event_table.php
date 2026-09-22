<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('event')) {
            return;
        }

        Schema::table('event', function (Blueprint $table): void {
            if (! Schema::hasColumn('event', 'Start_Time')) {
                $table->time('Start_Time')->nullable();
            }

            if (! Schema::hasColumn('event', 'End_Date')) {
                $table->date('End_Date')->nullable();
            }

            if (! Schema::hasColumn('event', 'End_Time')) {
                $table->time('End_Time')->nullable();
            }

            if (! Schema::hasColumn('event', 'Cover_Image')) {
                $table->string('Cover_Image', 255)->nullable();
            }
        });

        if (Schema::hasColumn('event', 'Event_Date')) {
            Schema::table('event', function (Blueprint $table): void {
                $table->date('Event_Date')->change();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('event')) {
            return;
        }

        Schema::table('event', function (Blueprint $table): void {
            foreach (['Start_Time', 'End_Date', 'End_Time', 'Cover_Image'] as $column) {
                if (Schema::hasColumn('event', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};