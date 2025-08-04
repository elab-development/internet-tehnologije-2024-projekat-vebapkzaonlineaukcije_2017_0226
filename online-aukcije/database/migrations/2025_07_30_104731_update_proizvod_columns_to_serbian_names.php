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
            $table->renameColumn('name', 'naziv');
            $table->renameColumn('description', 'opis');
            $table->renameColumn('category', 'kategorija');
            $table->renameColumn('state', 'stanje');
            $table->renameColumn('pictureURL', 'slika_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proizvod', function (Blueprint $table) {
            $table->renameColumn('naziv', 'name');
            $table->renameColumn('opis', 'description');
            $table->renameColumn('kategorija', 'category');
            $table->renameColumn('stanje', 'state');
            $table->renameColumn('slika_url', 'pictureURL');
        });
    }
};
