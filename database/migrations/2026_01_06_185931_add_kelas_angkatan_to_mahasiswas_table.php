<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            // kalau kamu mau urutan setelah NIM/Program Studi, silakan sesuaikan after()
            $table->string('kelas', 50)->nullable()->after('nim');
            $table->string('angkatan', 10)->nullable()->after('kelas');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'angkatan']);
        });
    }
};
