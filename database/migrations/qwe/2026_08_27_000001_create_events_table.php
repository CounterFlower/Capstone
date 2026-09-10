<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event', function (Blueprint $table) {
            $table->increments('Event_ID');
            $table->string('Event_Name');
            $table->dateTime('Event_Date');
            $table->string('Location')->nullable();
            $table->integer('Available_Slots')->nullable();
            $table->text('Summary')->nullable();
            $table->unsignedInteger('Created_By')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dateTime('End_Date')->nullable()->after('Event_Date');
            $table->string('Cover_Image')->nullable()->after('Summary');
        });
    }

    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropColumn(['End_Date', 'Cover_Image']);
        });
    }
};