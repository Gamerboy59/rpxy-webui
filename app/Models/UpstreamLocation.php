<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpstreamLocation extends Model
{
    use HasFactory;

    protected $fillable = ['upstream_id', 'location', 'tls'];

    public function upstream(): BelongsTo
    {
        return $this->belongsTo(Upstream::class);
    }
}
