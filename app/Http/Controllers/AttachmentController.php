<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function download($id)
    {
        // Find the attachment or throw a 404
        $attachment = Attachment::findOrFail($id);

        // Verify the file exists
        if (!Storage::disk('public')->exists($attachment->path)) {
            abort(404, 'File not found');
        }

        // Stream the file as a download with original filename
        return Storage::disk('public')->download(
            $attachment->path,
            $attachment->original_filename,
            [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $attachment->original_filename . '"'
            ]
        );
    }
}
