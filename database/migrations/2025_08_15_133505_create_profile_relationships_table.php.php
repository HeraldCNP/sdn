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
        Schema::create('profile_relationships', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('FromProfileID');
            $table->integer('ToProfileID');
            $table->integer('RelationTypeID')->nullable();
            $table->integer('RelationQualityID')->nullable();
            $table->boolean('Former')->nullable();
            $table->integer('SanctionsEntryID')->nullable();
            $table->text('Comment')->nullable();

            $table->foreign('FromProfileID')->references('ID')->on('profiles');
            $table->foreign('ToProfileID')->references('ID')->on('profiles');
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
