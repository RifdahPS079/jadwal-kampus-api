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
            $table->timestamp('dibaca_admin_pada')->nullable()->after('ditolak_pada');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pertemuans', function (Blueprint $table) {
            $table->dropColumn('dibaca_admin_pada');
        });
    }
};
