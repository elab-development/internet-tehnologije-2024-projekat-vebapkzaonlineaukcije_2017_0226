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
        Schema::table('ponuda', function (Blueprint $table) {
            
            $table->foreignId('aukcija_id')->constrained('aukcija')->onDelete('cascade');
            $table->foreignId('korisnik_id')->constrained('korisnik')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('ponuda', function (Blueprint $table) {
            $table->dropForeign(['aukcija_id']);
            $table->dropForeign(['korisnik_id']);
        });
    }
};
