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
            $table->id();
            $table->string('profile_relationship_id')->unique();
            $table->string('from_profile_id');
            $table->string('to_profile_id');
            $table->string('relation_type_id')->nullable();
            $table->string('relation_quality_id')->nullable();
            $table->boolean('former')->default(false);
            $table->string('sanctions_entry_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_relationships');
    }
};
