<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class ExternalIntegration extends Model
{
    use HasFactory ,HasRoles;

    protected $fillable = [
        'integration_id',
        'is_new_vendor',
        'vendor_id',
        'connection_method',
        'network_requirements',
        'authentication_method',
        'data_encryption_requirements',
        'api_documentation_url',
        'rate_limiting',
        'data_formats',
        'contract_expiration',
        'sla_terms',
        'legal_approval',
        'compliance_approval',
        'sit_outcome',
        'test_plan',
        'issue_log',
        'business_impact',
    ];

    protected $casts = [
        'is_new_vendor' => 'boolean',
        'data_formats' => 'array',
        'legal_approval' => 'boolean',
        'compliance_approval' => 'boolean',
        'contract_expiration' => 'date',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
