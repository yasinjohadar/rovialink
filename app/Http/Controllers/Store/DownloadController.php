<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\OrderDownload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download(string $token)
    {
        $download = OrderDownload::with(['order.status', 'file'])->where('download_token', $token)->firstOrFail();

        if (!$download->order || $download->order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$download->canDownload()) {
            abort(403);
        }

        if (!is_null($download->remaining_downloads)) {
            $download->decrement('remaining_downloads');
        }
        $download->update(['downloaded_at' => now()]);

        return Storage::disk('public')->download($download->file->path, $download->file->title);
    }
}

