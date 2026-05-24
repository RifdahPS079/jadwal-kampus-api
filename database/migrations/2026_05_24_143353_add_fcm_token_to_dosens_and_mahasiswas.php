<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('dosens', 'fcm_token')) {
            Schema::table('dosens', function (Blueprint $table) {
                $table->text('fcm_token')->nullable();
            });
        }

        if (!Schema::hasColumn('mahasiswas', 'fcm_token')) {
            Schema::table('mahasiswas', function (Blueprint $table) {
                $table->text('fcm_token')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dosens', 'fcm_token')) {
            Schema::table('dosens', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }

        if (Schema::hasColumn('mahasiswas', 'fcm_token')) {
            Schema::table('mahasiswas', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }
    }
};