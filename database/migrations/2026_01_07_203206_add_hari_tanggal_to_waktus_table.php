<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('waktus', function (Blueprint $table) {
            // kalau kolom sudah ada, skip (tapi umumnya belum)
            if (!Schema::hasColumn('waktus', 'hari')) {
                $table->string('hari', 15)->after('id'); // Senin, Selasa, dst
            }

            if (!Schema::hasColumn('waktus', 'tanggal')) {
                $table->date('tanggal')->nullable()->after('hari'); // opsional
            }

            // pastikan jam_mulai & jam_selesai ada (kalau belum ada)
            if (!Schema::hasColumn('waktus', 'jam_mulai')) {
                $table->time('jam_mulai')->after('tanggal');
            }
            if (!Schema::hasColumn('waktus', 'jam_selesai')) {
                $table->time('jam_selesai')->after('jam_mulai');
            }

            // unique agar tidak dobel slot waktu per hari
            $table->unique(['hari', 'jam_mulai', 'jam_selesai'], 'waktus_unique_hari_jam');
        });
    }

    public function down(): void
    {
        Schema::table('waktus', function (Blueprint $table) {
            // drop unique kalau ada
            try { $table->dropUnique('waktus_unique_hari_jam'); } catch (\Throwable $e) {}

            if (Schema::hasColumn('waktus', 'tanggal')) {
                $table->dropColumn('tanggal');
            }
            if (Schema::hasColumn('waktus', 'hari')) {
                $table->dropColumn('hari');
            }
        });
    }
};
