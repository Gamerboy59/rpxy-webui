<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RustProxy extends Model
{
    use HasFactory;

    protected $fillable = ['app_name', 'server_name', 'ssl_enabled', 'https_redirection', 'letsencrypt_enabled', 'tls_cert_path', 'tls_cert_key_path', 'client_ca_cert_path'];

    public function upstreams(): HasMany
    {
        return $this->hasMany(Upstream::class);
    }

}
