<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class SlaConfiguration extends Model
{
    use HasFactory ,HasRoles;

    protected $fillable = [
        'stage',
        'warning_hours',
        'critical_hours',
        'include_weekends',
    ];

    protected $casts = [
        'include_weekends' => 'boolean',
    ];
}
