<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public $timestamps = false;

    protected $fillable = ['url', 'ip_address', 'country', 'device', 'browser', 'referer', 'visited_at'];

    protected $casts = [
        'visited_at' => 'datetime',
    ];
}
