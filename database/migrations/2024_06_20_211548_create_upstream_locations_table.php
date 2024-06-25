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
        Schema::create('upstream_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upstream_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('location');
            $table->boolean('tls')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upstream_locations');
    }
};
