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
        Schema::table('proizvod', function (Blueprint $table) {
            
            $table->foreignId('aukcija_id')->constrained('aukcija')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proizvod', function (Blueprint $table) {
            $table->dropForeign(['aukcija_id']);
        });
    }
};
