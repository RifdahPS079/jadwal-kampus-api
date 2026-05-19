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
        Schema::table('pengampu_mata_kuliahs', function (Blueprint $table) {

            $table->foreignId('dosen2_id')
                ->nullable()
                ->after('dosen_id')
                ->constrained('dosens')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengampu_mata_kuliahs', function (Blueprint $table) {
            //
        });
    }
};
