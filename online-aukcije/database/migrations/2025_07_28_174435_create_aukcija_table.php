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
        Schema::create('aukcija', function (Blueprint $table) {
            $table->id();
            $table->integer('pocetna_cena');
            $table->integer('trenutna_cena');
            $table->dateTime('datum_pocetka');
            $table->string('status_aukcije');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aukcija');
    }
};
