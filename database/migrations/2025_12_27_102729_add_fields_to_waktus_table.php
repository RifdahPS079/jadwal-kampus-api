<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('waktus', function (Blueprint $table) {
            $table->string('hari')->after('id');
            $table->time('jam_mulai')->after('hari');
            $table->time('jam_selesai')->after('jam_mulai');
            $table->date('tanggal')->nullable()->after('jam_selesai'); // nullable kalau jadwal mingguan
        });
    }

    public function down(): void
    {
        Schema::table('waktus', function (Blueprint $table) {
            $table->dropColumn(['hari','jam_mulai','jam_selesai','tanggal']);
        });
    }
};
