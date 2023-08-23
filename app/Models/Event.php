<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable =
    [
        'name',
        'engine_status',
        'panic_button',
        'main_battery',
        'ext_battery',
        'gps_antenna',
        'engine_cutoff',
        'jamming',
        'status_events'
    ];

    protected $hidden =
    [
        'id',
        'created_at',
        'updated_at',
    ];
}
