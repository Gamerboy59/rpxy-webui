<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upstream extends Model
{
    use HasFactory;

    protected $fillable = ['rust_proxy_id', 'tls', 'loadbalance_type_id', 'path', 'replace_path'];

    public function rustProxy(): BelongsTo
    {
        return $this->belongsTo(RustProxy::class);
    }

    public function upstreamOptions(): BelongsToMany
    {
        return $this->belongsToMany(UpstreamOption::class);
    }

    public function loadbalanceType(): BelongsTo
    {
        return $this->belongsTo(LoadbalanceType::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(UpstreamLocation::class);
    }
}
