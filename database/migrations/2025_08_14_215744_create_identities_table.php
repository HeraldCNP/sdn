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
            $table->id();
            $table->string('identity_id')->unique();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_false')->default(false)->nullable();
            $table->string('fixed_ref')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identities');
    }
};
