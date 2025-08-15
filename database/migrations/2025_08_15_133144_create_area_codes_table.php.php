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
        Schema::create('area_codes', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('CountryID');
            $table->integer('AreaCodeTypeID');
            $table->string('Description')->nullable();
            $table->string('Code', 2);

            $table->foreign('CountryID')->references('ID')->on('countries');
            $table->foreign('AreaCodeTypeID')->references('ID')->on('area_code_types');
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
