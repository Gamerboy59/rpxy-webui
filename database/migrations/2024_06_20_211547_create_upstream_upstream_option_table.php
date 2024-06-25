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
        Schema::create('upstream_upstream_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upstream_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('upstream_option_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upstream_upstream_option');
    }
};
