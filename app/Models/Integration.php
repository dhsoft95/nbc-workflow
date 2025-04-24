<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Integration extends Model
{
    use HasFactory, SoftDeletes ,hasRoles;

    protected $fillable = [
        'name',
        'purpose',
        'department',
        'type',
        'status',
        'priority',
        'priority_justification',
        'resource_requirements',
        'estimated_timeline',
        'created_by',
    ];

    protected $casts = [
        'estimated_timeline' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function internalIntegration(): HasOne
    {
        return $this->hasOne(InternalIntegration::class);
    }

    public function externalIntegration(): HasOne
    {
        return $this->hasOne(ExternalIntegration::class);
    }

    public function approvalHistories(): HasMany
    {
        return $this->hasMany(ApprovalHistory::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function getCurrentApprovalStage()
    {
        // Helper method to get current approval stage based on status
        $statusToStageMap = [
            'app_owner_approval' => 'app_owner',
            'idi_approval' => 'idi',
            'security_approval' => 'security',
            'infrastructure_approval' => 'infrastructure',
        ];

        return $statusToStageMap[$this->status] ?? null;
    }

    public function isInternal()
    {
        return $this->type === 'internal';
    }

    public function isExternal()
    {
        return $this->type === 'external';
    }
}
