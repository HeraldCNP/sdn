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
        Schema::table('name_part_groups', function (Blueprint $table) {
            $table->foreign('NamePartTypeID')->references('ID')->on('name_part_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('name_part_groups', function (Blueprint $table) {
            $table->dropForeign(['NamePartTypeID']);
        });
    }
};
