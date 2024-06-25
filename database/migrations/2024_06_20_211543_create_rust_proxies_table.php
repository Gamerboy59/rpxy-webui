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
        Schema::create('rust_proxies', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->unique();
            $table->string('server_name')->unique();
            $table->boolean('ssl_enabled')->default(false);
            $table->boolean('https_redirection')->default(true);
            $table->string('tls_cert_path')->nullable();
            $table->string('tls_cert_key_path')->nullable();
            $table->string('client_ca_cert_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rust_proxies');
    }
};
