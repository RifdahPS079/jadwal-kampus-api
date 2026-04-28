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
        Schema::create('pengampu_mata_kuliahs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dosen_id')->constrained('dosens')->cascadeOnDelete();
        $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
        $table->string('semester');
        $table->string('tahun_ajaran');
        $table->timestamps();

        $table->unique(['dosen_id', 'mata_kuliah_id', 'semester', 'tahun_ajaran'], 'pengampu_unique');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengampu_mata_kuliahs');
    }
};
