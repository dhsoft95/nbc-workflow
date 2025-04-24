<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class ApprovalHistory extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'integration_id',
        'stage',
        'action',
        'user_id',
        'comments',
        'return_to_stage',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
