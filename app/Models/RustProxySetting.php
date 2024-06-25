<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RustProxySetting extends Model
{
    use HasFactory;

    protected $fillable = ['section', 'key', 'value', 'type'];
}
