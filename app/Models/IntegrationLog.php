<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntegrationLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service',
        'method',
        'status_code',
        'duration_ms',
        ''
    ];
}
