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
        Schema::create('upstreams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rust_proxy_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('tls')->default(false);
            $table->foreignId('loadbalance_type_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('path')->nullable();
            $table->string('replace_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upstreams');
    }
};
