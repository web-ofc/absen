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
        // Schema::create('attendances', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained();
        //     $table->foreignId('geozone_id')->nullable()->constrained();
        //     $table->date('attendance_date');
        //     $table->dateTime('check_in_time')->nullable();
        //     $table->dateTime('check_out_time')->nullable();
        //     $table->double('check_in_lat')->nullable();
        //     $table->double('check_in_lng')->nullable();
        //     $table->double('check_out_lat')->nullable();
        //     $table->double('check_out_lng')->nullable();
        //     $table->string('check_in_address')->nullable();
        //     $table->string('check_out_address')->nullable();
        //     $table->string('check_in_photo')->nullable();
        //     $table->string('check_out_photo')->nullable();
        //     $table->float('face_verification_score')->nullable();
        //     $table->boolean('is_face_verified')->default(false);
        //     $table->enum('status', ['present', 'absent', 'late', 'early_leave', 'incomplete'])->default('incomplete');
        //     $table->text('notes')->nullable();
        //     $table->timestamps();
            
        //     $table->unique(['user_id', 'attendance_date']);
        // });


        // Schema::create('attendances', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('user_id');   // relasi ke users
        //     $table->date('date');                    // tanggal absen
        //     $table->time('time_in')->nullable();     // jam masuk
        //     $table->time('time_out')->nullable();    // jam pulang
        //     $table->string('photo')->nullable();     // path foto absensi
        //     $table->unsignedBigInteger('geozone_id')->nullable(); // hapus ->after()
        //     $table->timestamps();

        //     $table->foreign('geozone_id')->references('id')->on('geozones')->onDelete('set null');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');   // relasi ke users
            $table->dateTime('time_in')->nullable();     // jam masuk
            $table->dateTime('time_out')->nullable();    // jam pulang
            $table->string('photo_in')->nullable();     // path foto absensi
            $table->string('photo_out')->nullable();     // path foto absensi
            $table->double('check_in_lat')->nullable();
            $table->double('check_in_lng')->nullable();
            $table->double('check_out_lat')->nullable();
            $table->double('check_out_lng')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
