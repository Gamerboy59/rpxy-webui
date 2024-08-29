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
        Schema::table('rust_proxies', function (Blueprint $table) {
            $table->boolean('letsencrypt_enabled')->default(false)->after('https_redirection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rust_proxies', function (Blueprint $table) {
            $table->dropColumn('letsencrypt_enabled');
        });
    }
};
