<?php

namespace Tests\Unit;

use App\DownloadStatus;
use App\Models\Download;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DownloadModelTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_expires_at_is_retention_days_after_fetched_at(): void
    {
        Carbon::setTestNow('2026-06-01 12:00:00');

        $download = Download::factory()->create([
            'status' => DownloadStatus::Completed,
            'fetched_at' => Carbon::parse('2026-06-01 12:00:00'),
            'file_path' => 'downloads/test.mp4',
        ]);

        $this->assertTrue(
            $download->expiresAt()->equalTo(Carbon::parse('2026-06-08 12:00:00')),
        );
    }

    public function test_is_expired_when_file_was_purged(): void
    {
        $download = Download::factory()->create([
            'status' => DownloadStatus::Completed,
            'file_path' => null,
            'fetched_at' => now()->subDays(10),
        ]);

        $this->assertTrue($download->isExpired());
        $this->assertTrue($download->canRefetch());
    }

    public function test_human_duration_formats_length_in_words(): void
    {
        $download = Download::factory()->make([
            'duration_seconds' => 3727,
        ]);

        $this->assertSame('1 hour 2 minutes', $download->humanDuration());
    }

    public function test_is_expired_when_past_expiry_even_if_file_still_on_disk(): void
    {
        Carbon::setTestNow('2026-06-10 12:00:00');

        $download = Download::factory()->create([
            'status' => DownloadStatus::Completed,
            'file_path' => 'downloads/test.mp4',
            'fetched_at' => Carbon::parse('2026-06-01 12:00:00'),
        ]);

        $this->assertTrue($download->isExpired());
    }
}
