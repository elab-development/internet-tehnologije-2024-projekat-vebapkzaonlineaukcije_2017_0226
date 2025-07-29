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
            $table->dropColumn('email_verified_at');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('remember_token');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korisnik', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->rememberToken();
    });

    }

};
