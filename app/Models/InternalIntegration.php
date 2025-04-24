<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class InternalIntegration extends Model
{
    use HasFactory ,HasRoles;

    protected $fillable = [
        'integration_id',
        'middleware_connection',
        'cms_binding',
        'cms_binding_details',
        'api_specifications',
        'security_classification',
        'responsible_team',
        'features_supported',
        'system_dependencies',
    ];

    protected $casts = [
        'cms_binding' => 'boolean',
        'features_supported' => 'array',
        'system_dependencies' => 'array',
    ];

    public function integration(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
