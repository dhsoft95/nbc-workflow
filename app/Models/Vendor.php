<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_email',
        'contact_phone',
        'address',
        'website',
        'description',
    ];

    public function externalIntegrations(): HasMany
    {
        return $this->hasMany(ExternalIntegration::class);
    }
}
