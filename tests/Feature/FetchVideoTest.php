<?php

namespace Tests\Feature;

use App\DownloadStatus;
use App\Jobs\DownloadVideoJob;
use App\Models\Download;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FetchVideoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_home_page_displays_recent_downloads(): void
    {
        $user = auth()->user();

        $download = Download::factory()->for($user)->create([
            'title' => 'Test Documentary',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Test Documentary');
        $response->assertSee('Recent Downloads');
    }

    public function test_fetch_probes_video_and_queues_download_job(): void
    {
        Queue::fake();

        Process::fake([
            '*' => Process::result(
                output: json_encode([
                    'id' => 'dQw4w9WgXcQ',
                    'title' => 'Never Gonna Give You Up',
                    'duration' => 213,
                    'ext' => 'mp4',
                ]),
            ),
        ]);

        $response = $this->post(route('fetch.store'), [
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('downloads', [
            'external_id' => 'dQw4w9WgXcQ',
            'title' => 'Never Gonna Give You Up',
            'duration_seconds' => 213,
            'status' => DownloadStatus::Pending->value,
            'user_id' => auth()->id(),
        ]);

        Queue::assertPushed(DownloadVideoJob::class);
    }

    public function test_fetch_rejects_unsupported_hosts(): void
    {
        Queue::fake();
        Process::fake();

        $response = $this->from(route('home'))
            ->post(route('fetch.store'), [
                'url' => 'https://example.com/video/123',
            ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('url');

        $this->assertDatabaseCount('downloads', 0);
        Queue::assertNothingPushed();
        Process::assertNothingRan();
    }

    public function test_fetch_shows_error_when_probe_fails(): void
    {
        Queue::fake();

        Process::fake([
            '*' => Process::result(
                output: '',
                errorOutput: 'ERROR: Video unavailable',
                exitCode: 1,
            ),
        ]);

        $response = $this->from(route('home'))
            ->post(route('fetch.store'), [
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('url');

        $this->assertDatabaseCount('downloads', 0);
        Queue::assertNothingPushed();
    }
}
