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
        Schema::table('korisnik', function (Blueprint $table) {
            $table->string('uloga')->default('ulogovan korisnik')->after('lozinka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korisnik', function (Blueprint $table) {
            $table->dropColumn('uloga');
        });
    }
};
