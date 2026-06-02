<?php

namespace App\Http\Controllers;

use App\DownloadStatus;
use App\Exceptions\YtDlpException;
use App\Http\Requests\FetchVideoRequest;
use App\Jobs\DownloadVideoJob;
use App\Services\YtDlpService;
use Illuminate\Http\RedirectResponse;

class FetchController extends Controller
{
    public function store(FetchVideoRequest $request, YtDlpService $ytDlp): RedirectResponse
    {
        $url = $request->string('url')->toString();

        try {
            $metadata = $ytDlp->probe($url);
        } catch (YtDlpException $exception) {
            return back()
                ->withInput()
                ->withErrors(['url' => $exception->getMessage()]);
        }

        $download = $request->user()->downloads()->create([
            'url' => $metadata->url,
            'external_id' => $metadata->id,
            'title' => $metadata->title,
            'duration_seconds' => $metadata->durationSeconds,
            'status' => DownloadStatus::Pending,
        ]);

        DownloadVideoJob::dispatch($download);

        return redirect()
            ->route('home')
            ->with('status', 'Download queued for "'.$metadata->title.'".');
    }
}
