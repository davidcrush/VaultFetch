<?php

namespace App\Console\Commands;

use App\DownloadStatus;
use App\Models\Download;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredDownloadsCommand extends Command
{
    protected $signature = 'downloads:purge-expired';

    protected $description = 'Delete video files older than the retention period while keeping download history';

    public function handle(): int
    {
        $retentionDays = config('vaultfetch.retention_days');
        $cutoff = now()->subDays($retentionDays);

        $downloads = Download::query()
            ->where('status', DownloadStatus::Completed)
            ->whereNotNull('file_path')
            ->where(function ($query) use ($cutoff): void {
                $query->where('fetched_at', '<', $cutoff)
                    ->orWhere(function ($query) use ($cutoff): void {
                        $query->whereNull('fetched_at')
                            ->where('created_at', '<', $cutoff);
                    });
            })
            ->get();

        $purged = 0;

        foreach ($downloads as $download) {
            if (Storage::disk('local')->exists($download->file_path)) {
                Storage::disk('local')->delete($download->file_path);
            }

            $download->update(['file_path' => null]);
            $purged++;
        }

        $this->info("Purged {$purged} expired download file(s) older than {$retentionDays} day(s).");

        return self::SUCCESS;
    }
}
