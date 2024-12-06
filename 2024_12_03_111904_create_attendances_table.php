<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Relasi ke user
            $table->date('date'); // Tanggal presensi
            $table->time('time'); // Waktu presensi
            $table->enum('status', ['hadir', 'tidak hadir', 'izin', 'sakit']); // Status presensi
            $table->timestamps(); // created_at dan updated_at

            // Tambahkan foreign key jika ada relasi ke tabel users
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
}
