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
        Schema::table('aukcija', function (Blueprint $table) {
            $table->integer('trenutna_cena')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aukcija', function (Blueprint $table) {
            $table->integer('trenutna_cena')->change();
        });
    }
};
