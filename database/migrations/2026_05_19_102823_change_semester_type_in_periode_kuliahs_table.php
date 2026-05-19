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
    Schema::table('periode_kuliahs', function (Blueprint $table) {
        $table->string('semester')->change();
    });
}

public function down(): void
{
    Schema::table('periode_kuliahs', function (Blueprint $table) {
        $table->integer('semester')->change();
    });
}

};
