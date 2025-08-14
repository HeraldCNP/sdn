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
            $table->id();
            $table->foreignId('identity_id')->constrained('identities')->onDelete('cascade');
            $table->string('alias_id')->nullable();
            $table->string('name_part_value');
            $table->string('alias_type_id')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('low_quality')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aliases');
    }
};
