<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pertemuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();

            $table->integer('pertemuan_ke');

            $table->foreignId('waktu_id')->nullable()->constrained('waktus')->nullOnDelete();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();

            $table->string('status')->default('aktif'); 
            // aktif, batal, pindah

            $table->text('alasan_batal')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_id', 'pertemuan_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pertemuans');
    }
};