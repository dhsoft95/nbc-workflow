<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class Attachment extends Model
{
    use HasFactory,HasRoles;

    protected $fillable = [
        'integration_id',
        'type',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'path',
        'uploaded_by',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
