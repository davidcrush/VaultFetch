<?php

namespace Tests\Feature;

use App\DownloadStatus;
use App\Jobs\DownloadVideoJob;
use App\Models\Download;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RefetchDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_refetch_queues_download_for_expired_record(): void
    {
        Queue::fake();
        Carbon::setTestNow('2026-06-10 12:00:00');

        $download = Download::factory()->for(auth()->user())->create([
            'status' => DownloadStatus::Completed,
            'file_path' => null,
            'fetched_at' => now()->subDays(10),
        ]);

        $response = $this->post(route('downloads.refetch', $download));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('status');

        $download->refresh();

        $this->assertSame(DownloadStatus::Pending, $download->status);
        $this->assertNull($download->file_path);

        Queue::assertPushed(DownloadVideoJob::class);
    }

    public function test_refetch_rejects_active_downloads(): void
    {
        Queue::fake();
        Carbon::setTestNow('2026-06-02 12:00:00');

        $download = Download::factory()->for(auth()->user())->create([
            'status' => DownloadStatus::Completed,
            'file_path' => 'downloads/active.mp4',
            'fetched_at' => Carbon::parse('2026-06-01 12:00:00'),
        ]);

        $this->assertFalse($download->isExpired());

        $response = $this->from(route('home'))
            ->post(route('downloads.refetch', $download));

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('download');

        Queue::assertNothingPushed();
    }
}
