<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periode_kuliahs', function (Blueprint $table) {
            $table->integer('jumlah_pertemuan')->default(16)->after('tanggal_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('periode_kuliahs', function (Blueprint $table) {
            $table->dropColumn('jumlah_pertemuan');
        });
    }
};