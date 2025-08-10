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
            $table->string('naziv')->after('id');
            $table->integer('maksimalna_cena')->nullable()->after('trenutna_cena');
            $table->dateTime('vreme_isteka')->nullable()->after('datum_pocetka'); 
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aukcija', function (Blueprint $table) {
            $table->dropColumn(['naziv', 'maksimalna_cena','vreme_isteka']);
        });
    }
};
