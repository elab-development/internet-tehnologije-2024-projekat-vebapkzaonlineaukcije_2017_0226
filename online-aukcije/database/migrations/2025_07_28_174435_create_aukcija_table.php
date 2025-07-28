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
            $table->integer('pocetnaCena');
            $table->integer('trenutnaCena');
            $table->timestamp('datumPocetka');
            $table->string('statusAukcije');
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
