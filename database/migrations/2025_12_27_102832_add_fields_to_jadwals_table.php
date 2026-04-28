<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->foreignId('pengampu_id')
                ->after('id')
                ->constrained('pengampu_mata_kuliahs')
                ->cascadeOnDelete();

            $table->foreignId('ruangan_id')
                ->after('pengampu_id')
                ->constrained('ruangans')
                ->cascadeOnDelete();

            $table->foreignId('waktu_id')
                ->after('ruangan_id')
                ->constrained('waktus')
                ->cascadeOnDelete();

            $table->string('program_studi')->after('waktu_id');
            $table->string('kelas')->after('program_studi');

            // minimal mencegah 1 ruangan dipakai di waktu yang sama
            $table->unique(['ruangan_id', 'waktu_id'], 'ruangan_waktu_unique');
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropUnique('ruangan_waktu_unique');

            $table->dropForeign(['pengampu_id']);
            $table->dropForeign(['ruangan_id']);
            $table->dropForeign(['waktu_id']);

            $table->dropColumn(['pengampu_id', 'ruangan_id', 'waktu_id', 'program_studi', 'kelas']);
        });
    }
};
