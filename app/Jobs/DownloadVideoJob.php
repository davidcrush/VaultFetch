<?php

namespace App\Jobs;

use App\DownloadStatus;
use App\Models\Download;
use App\Services\YtDlpService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class DownloadVideoJob implements ShouldQueue
{
    use Queueable;

    public int $timeout;

    public function __construct(public Download $download)
    {
        $this->timeout = config('vaultfetch.download_timeout') + 60;
    }

    public function handle(YtDlpService $ytDlp): void
    {
        $this->download->update(['status' => DownloadStatus::Downloading]);

        $filePath = $ytDlp->download(
            $this->download->url,
            (string) $this->download->external_id,
        );

        $this->download->update([
            'status' => DownloadStatus::Completed,
            'file_path' => $filePath,
            'fetched_at' => now(),
            'error_message' => null,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $this->download->update([
            'status' => DownloadStatus::Failed,
            'error_message' => $exception?->getMessage() ?? __('vaultfetch.messages.job_failed'),
        ]);
    }
}
