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
        Schema::create('sanctions_entries', function (Blueprint $table) {
            $table->id();
            $table->string('sanctions_entry_id')->unique();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->string('entry_event_type_id')->nullable();
            $table->string('legal_basis_id')->nullable();
            $table->string('sanctions_type_id')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanctions_entries');
    }
};
