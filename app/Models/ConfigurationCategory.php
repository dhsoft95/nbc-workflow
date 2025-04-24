<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class ConfigurationCategory extends Model
{
    use HasFactory ,HasRoles;

    protected $fillable = [
        'name',
        'key',
        'description',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ConfigurationItem::class, 'category_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ConfigurationHistory::class, 'category_id');
    }
}
