<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class ConfigurationHistory extends Model
{
    use HasFactory ,HasRoles;

    protected $table = 'configuration_histories';

    protected $fillable = [
        'category_id',
        'item_id',
        'action',
        'old_value',
        'new_value',
        'user_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ConfigurationCategory::class, 'category_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ConfigurationItem::class, 'item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
