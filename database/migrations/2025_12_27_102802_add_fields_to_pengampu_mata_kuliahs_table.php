<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengampu_mata_kuliahs', function (Blueprint $table) {
            $table->foreignId('dosen_id')
                ->after('id')
                ->constrained('dosens')
                ->cascadeOnDelete();

            $table->foreignId('mata_kuliah_id')
                ->after('dosen_id')
                ->constrained('mata_kuliahs')
                ->cascadeOnDelete();

            $table->string('semester')->after('mata_kuliah_id');
            $table->string('tahun_ajaran')->after('semester');

            // biar tidak dobel pengampu yang sama
            $table->unique(
                ['dosen_id', 'mata_kuliah_id', 'semester', 'tahun_ajaran'],
                'pengampu_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('pengampu_mata_kuliahs', function (Blueprint $table) {
            $table->dropUnique('pengampu_unique');

            $table->dropForeign(['dosen_id']);
            $table->dropForeign(['mata_kuliah_id']);

            $table->dropColumn(['dosen_id', 'mata_kuliah_id', 'semester', 'tahun_ajaran']);
        });
    }
};
