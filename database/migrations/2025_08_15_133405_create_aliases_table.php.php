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
        Schema::create('aliases', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('FixedRef');
            $table->integer('AliasTypeID');
            $table->boolean('Primary_')->nullable();
            $table->boolean('LowQuality')->nullable();
            $table->integer('DocumentedNameID')->nullable();

            $table->foreign('FixedRef')->references('FixedRef')->on('identities');
            $table->foreign('AliasTypeID')->references('ID')->on('alias_types');
            $table->foreign('DocumentedNameID')->references('ID')->on('documented_names'); // La tabla 'documented_names' debe existir
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
