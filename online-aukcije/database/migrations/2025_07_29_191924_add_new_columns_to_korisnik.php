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
            $table->string('prezime')->after('ime');
            $table->integer('brojTelefona')->after('remember_token');
            $table->string('adresa')->after('brojTelefona');
            $table->integer('stanjeNaRacunu')->after('adresa');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korisnik', function (Blueprint $table) {
        $table->dropColumn('prezime');
        $table->dropColumn('brojTelefona');
        $table->dropColumn('adresa');
        $table->dropColumn('stanjeNaRacunu');
        });
    }
};
