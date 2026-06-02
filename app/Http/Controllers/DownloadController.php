<?php

namespace App\Http\Controllers;

use App\DownloadStatus;
use App\Jobs\DownloadVideoJob;
use App\Models\Download;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function show(Download $download): View|RedirectResponse
    {
        if (! $download->isFileAvailable()) {
            return redirect()
                ->route('home')
                ->withErrors(['download' => __('vaultfetch.messages.file_not_available')]);
        }

        return view('downloads.show', [
            'download' => $download,
        ]);
    }

    public function stream(Download $download): StreamedResponse|RedirectResponse
    {
        if (! $download->isFileAvailable()) {
            return redirect()
                ->route('home')
                ->withErrors(['download' => __('vaultfetch.messages.file_not_available')]);
        }

        return Storage::disk('local')->response(
            $download->file_path,
            basename($download->file_path),
            [
                'Content-Type' => 'video/mp4',
                'Content-Disposition' => 'inline',
            ],
        );
    }

    public function file(Download $download): StreamedResponse|RedirectResponse
    {
        if (! $download->isFileAvailable()) {
            return redirect()
                ->route('home')
                ->withErrors(['download' => __('vaultfetch.messages.file_not_available')]);
        }

        return Storage::disk('local')->download(
            $download->file_path,
            basename($download->file_path),
        );
    }

    public function refetch(Download $download): RedirectResponse
    {
        if (! $download->canRefetch()) {
            return redirect()
                ->route('home')
                ->withErrors(['download' => __('vaultfetch.messages.cannot_refetch')]);
        }

        $download->update([
            'status' => DownloadStatus::Pending,
            'file_path' => null,
            'error_message' => null,
        ]);

        DownloadVideoJob::dispatch($download);

        return redirect()
            ->route('home')
            ->with('status', __('vaultfetch.messages.refetch_queued', ['title' => $download->title]));
    }
}
