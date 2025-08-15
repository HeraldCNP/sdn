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
        Schema::create('name_part_groups', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('NamePartTypeID')->nullable();
            $table->integer('IdentityID');

            $table->foreign('IdentityID')->references('ID')->on('identities');
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
