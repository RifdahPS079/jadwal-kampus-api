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
        Schema::table('jadwal_pertemuans', function (Blueprint $table) {
            $table->text('alasan_tolak')->nullable()->after('alasan_batal');
            $table->timestamp('disetujui_pada')->nullable()->after('alasan_tolak');
            $table->timestamp('ditolak_pada')->nullable()->after('disetujui_pada');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pertemuans', function (Blueprint $table) {
            $table->dropColumn([
                'alasan_tolak',
                'disetujui_pada',
                'ditolak_pada',
            ]);
        });
    }
};
