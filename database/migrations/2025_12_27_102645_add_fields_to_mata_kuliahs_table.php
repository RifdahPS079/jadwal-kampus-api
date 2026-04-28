<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->string('kode_mk')->unique()->after('id');
            $table->string('nama_mk')->after('kode_mk');
            $table->unsignedTinyInteger('sks')->after('nama_mk');
            $table->string('program_studi')->after('sks');
            $table->string('semester')->after('program_studi');
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->dropUnique('mata_kuliahs_kode_mk_unique');
            $table->dropColumn(['kode_mk','nama_mk','sks','program_studi','semester']);
        });
    }
};
