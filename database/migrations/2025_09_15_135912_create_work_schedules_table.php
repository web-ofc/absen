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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Shift Malam", "Shift Pagi", dll.
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_shift')->default(false);
            $table->boolean('cross_midnight')->default(false); // TANDAKAN APAKAH SHIFT LINTAS HARI
            $table->integer('end_time_next_day')->default(0); // BERAPA HARI SETELAHNYA UNTUK END_TIME
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
