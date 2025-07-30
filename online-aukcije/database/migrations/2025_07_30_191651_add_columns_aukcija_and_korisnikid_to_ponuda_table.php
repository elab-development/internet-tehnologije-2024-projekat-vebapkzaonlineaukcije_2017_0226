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
            
            $table->foreignId('aukcijaID')->constrained('aukcija')->onDelete('cascade');
            $table->foreignId('korisnikID')->constrained('korisnik')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('ponuda', function (Blueprint $table) {
            $table->dropForeign(['aukcijaID']);
            $table->dropForeign(['korisnikID']);
        });
    }
};
