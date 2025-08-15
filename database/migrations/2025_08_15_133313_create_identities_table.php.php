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
        Schema::create('identities', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('FixedRef');
            $table->boolean('Primary_')->nullable();
            $table->boolean('False_')->nullable();
            $table->integer('ProfileID');

            $table->foreign('FixedRef')->references('FixedRef')->on('distinct_parties');
            $table->foreign('ProfileID')->references('ID')->on('profiles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
