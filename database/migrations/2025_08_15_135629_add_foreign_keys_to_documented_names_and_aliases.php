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
        // Add foreign key constraint to documented_names table
        Schema::table('documented_names', function (Blueprint $table) {
            $table->foreign('AliasID')->references('ID')->on('aliases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint from documented_names table
        Schema::table('documented_names', function (Blueprint $table) {
            $table->dropForeign(['AliasID']);
        });
    }
};
