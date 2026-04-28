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
        Schema::create('jadwals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pengampu_id')->constrained('pengampu_mata_kuliahs')->cascadeOnDelete();
        $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
        $table->foreignId('waktu_id')->constrained('waktus')->cascadeOnDelete();

        $table->string('program_studi');
        $table->string('kelas');
        $table->timestamps();

        $table->unique(['ruangan_id','waktu_id'], 'ruangan_waktu_unique');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
