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
        $table->string('hari_lama')->nullable();
        $table->string('tanggal_lama')->nullable();
        $table->string('jam_lama')->nullable();
        $table->string('ruangan_lama')->nullable();
    });
}

public function down(): void
{
    Schema::table('jadwal_pertemuans', function (Blueprint $table) {
        $table->dropColumn([
            'hari_lama',
            'tanggal_lama',
            'jam_lama',
            'ruangan_lama',
        ]);
    });
}
};
