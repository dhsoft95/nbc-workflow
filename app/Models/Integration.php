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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Store metadata about the integration
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function setMetaData($key, $value)
    {
        // Using the attachments table with a special 'metadata' type to store this information
        $metadata = $this->attachments()
            ->firstOrNew([
                'type' => 'metadata',
                'filename' => $key
            ]);

        $metadata->original_filename = $key;
        $metadata->mime_type = 'application/json';
        $metadata->size = 0;
        $metadata->path = null;
        $metadata->uploaded_by = auth()->id() ?? 1; // System user if no one is logged in
        $metadata->meta_value = $value; // Using meta_value field to store the actual value

        return $metadata->save();
    }

    /**
     * Get metadata about the integration
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getMetaData($key, $default = null)
    {
        $metadata = $this->attachments()
            ->where('type', 'metadata')
            ->where('filename', $key)
            ->first();

        return $metadata ? $metadata->meta_value : $default;
    }

    /**
     * Get time spent in current stage
     *
     * @return int Hours in current stage
     */
    public function getHoursInCurrentStage()
    {
        $currentStage = $this->getCurrentApprovalStage();
        if (!$currentStage) {
            return 0;
        }

        // Get last approval history for current stage
        $latestHistory = $this->approvalHistories()
            ->where('stage', $currentStage)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestHistory) {
            return 0;
        }

        // Calculate hours since that history record
        $enteredStageAt = $latestHistory->created_at;
        return now()->diffInHours($enteredStageAt);
    }

    /**
     * Check if current stage exceeds SLA warning threshold
     *
     * @return bool
     */
    public function exceedsSlaWarning()
    {
        $currentStage = $this->getCurrentApprovalStage();
        if (!$currentStage) {
            return false;
        }

        $hoursInStage = $this->getHoursInCurrentStage();

        // Get SLA configuration
        $slaConfig = \App\Models\SlaConfiguration::where('stage', $currentStage)->first();
        if (!$slaConfig) {
            return false;
        }

        return $hoursInStage >= $slaConfig->warning_hours;
    }

    /**
     * Check if current stage exceeds SLA critical threshold
     *
     * @return bool
     */
    public function exceedsSlaCritical()
    {
        $currentStage = $this->getCurrentApprovalStage();
        if (!$currentStage) {
            return false;
        }

        $hoursInStage = $this->getHoursInCurrentStage();

        // Get SLA configuration
        $slaConfig = \App\Models\SlaConfiguration::where('stage', $currentStage)->first();
        if (!$slaConfig) {
            return false;
        }

        return $hoursInStage >= $slaConfig->critical_hours;
    }
}
