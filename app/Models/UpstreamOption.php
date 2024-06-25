<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UpstreamOption extends Model
{
    use HasFactory;

    protected $fillable = ['upstream_id', 'option'];

    public function upstreams(): BelongsToMany
    {
        return $this->belongsToMany(Upstream::class);
    }
}
