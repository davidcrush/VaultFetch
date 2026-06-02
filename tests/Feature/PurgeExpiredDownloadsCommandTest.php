<?php

namespace Tests\Feature;

use App\DownloadStatus;
use App\Models\Download;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurgeExpiredDownloadsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_purges_files_older_than_retention_period(): void
    {
        Storage::fake('local');
        Carbon::setTestNow('2026-06-10 12:00:00');

        $expired = Download::factory()->create([
            'status' => DownloadStatus::Completed,
            'file_path' => 'downloads/expired.mp4',
            'fetched_at' => now()->subDays(8),
        ]);

        Storage::disk('local')->put('downloads/expired.mp4', 'video content');

        $recent = Download::factory()->create([
            'status' => DownloadStatus::Completed,
            'file_path' => 'downloads/recent.mp4',
            'fetched_at' => now()->subDays(2),
        ]);

        Storage::disk('local')->put('downloads/recent.mp4', 'video content');

        $this->artisan('downloads:purge-expired')
            ->assertSuccessful();

        $expired->refresh();
        $recent->refresh();

        $this->assertNull($expired->file_path);
        $this->assertFalse($expired->isFileAvailable());
        $this->assertTrue($expired->hasExpiredFile());
        Storage::disk('local')->assertMissing('downloads/expired.mp4');

        $this->assertSame('downloads/recent.mp4', $recent->file_path);
        $this->assertTrue($recent->isFileAvailable());
        Storage::disk('local')->assertExists('downloads/recent.mp4');

        $this->assertDatabaseCount('downloads', 2);
    }

    public function test_purges_using_created_at_when_fetched_at_is_null(): void
    {
        Storage::fake('local');
        Carbon::setTestNow('2026-06-10 12:00:00');

        $download = Download::factory()->create([
            'status' => DownloadStatus::Completed,
            'file_path' => 'downloads/old.mp4',
            'fetched_at' => null,
            'created_at' => now()->subDays(10),
        ]);

        Storage::disk('local')->put('downloads/old.mp4', 'video content');

        $this->artisan('downloads:purge-expired')
            ->assertSuccessful();

        $download->refresh();

        $this->assertNull($download->file_path);
        Storage::disk('local')->assertMissing('downloads/old.mp4');
    }

    public function test_does_not_purge_non_completed_downloads(): void
    {
        Storage::fake('local');
        Carbon::setTestNow('2026-06-10 12:00:00');

        $pending = Download::factory()->pending()->create([
            'file_path' => 'downloads/pending.mp4',
            'created_at' => now()->subDays(30),
        ]);

        Storage::disk('local')->put('downloads/pending.mp4', 'video content');

        $this->artisan('downloads:purge-expired')
            ->assertSuccessful();

        $pending->refresh();

        $this->assertSame('downloads/pending.mp4', $pending->file_path);
        Storage::disk('local')->assertExists('downloads/pending.mp4');
    }
}
