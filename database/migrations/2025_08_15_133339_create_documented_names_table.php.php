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
        Schema::create('documented_names', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('FixedRef');
            $table->integer('DocNameStatusID')->nullable();
            $table->integer('AliasID')->nullable();

            $table->foreign('FixedRef')->references('FixedRef')->on('identities');
            // Foreign key for AliasID will be added after aliases table is created
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
