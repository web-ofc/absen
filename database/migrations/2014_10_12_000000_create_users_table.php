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
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->timestamp('email_verified_at')->nullable();
        //     $table->string('password');
        //     $table->rememberToken();
        //     $table->timestamps();
        // });
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('employee_id')->unique();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->timestamp('email_verified_at')->nullable();
        //     $table->string('password');
        //     $table->string('phone')->nullable();
        //     $table->foreignId('division_id')->constrained();
        //     $table->text('face_descriptor')->nullable(); // Face API JS descriptor
        //     $table->boolean('can_attend_anywhere')->default(false);
        //     $table->boolean('is_active')->default(true);
        //     $table->rememberToken();
        //     $table->timestamps();
        // });
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->text('face_descriptor')->nullable(); // Data wajah dalam JSON
        //     $table->string('photo')->nullable(); // Path foto
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        // });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->string('email')->unique();
            $table->text('face_descriptor')->nullable(); 
            $table->string('photo')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->boolean('can_attend_anywhere')->default(false); // 🔥 bisa absen di semua geozone
            $table->boolean('can_anytime')->default(false); // 🔥 bisa absen kapan saja
            $table->foreignId('work_schedule_id')->nullable()->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
