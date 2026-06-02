<?php

namespace Tests\Feature;

use App\DownloadStatus;
use App\Jobs\DownloadVideoJob;
use App\Models\Download;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_lists_only_current_user_downloads(): void
    {
        $userA = $this->authenticate();
        $userB = User::factory()->create();

        Download::factory()->for($userA)->create(['title' => 'My Video']);
        Download::factory()->for($userB)->create(['title' => 'Other User Video']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('My Video');
        $response->assertDontSee('Other User Video');
    }

    public function test_user_cannot_view_another_users_download(): void
    {
        $userA = User::factory()->create();
        $userB = $this->authenticate();

        $download = Download::factory()->for($userA)->create([
            'status' => DownloadStatus::Completed,
            'file_path' => 'downloads/other.mp4',
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('downloads/other.mp4', 'video');

        $this->get(route('downloads.show', $download))->assertNotFound();
    }

    public function test_user_cannot_stream_another_users_download(): void
    {
        $userA = User::factory()->create();
        $this->authenticate();

        $download = Download::factory()->for($userA)->create([
            'status' => DownloadStatus::Completed,
            'file_path' => 'downloads/other.mp4',
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('downloads/other.mp4', 'video');

        $this->get(route('downloads.stream', $download))->assertNotFound();
    }

    public function test_user_cannot_download_file_for_another_users_download(): void
    {
        $userA = User::factory()->create();
        $this->authenticate();

        $download = Download::factory()->for($userA)->create([
            'status' => DownloadStatus::Completed,
            'file_path' => 'downloads/other.mp4',
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('downloads/other.mp4', 'video');

        $this->get(route('downloads.file', $download))->assertNotFound();
    }

    public function test_user_cannot_refetch_another_users_download(): void
    {
        Queue::fake();
        Carbon::setTestNow('2026-06-10 12:00:00');

        $userA = User::factory()->create();
        $this->authenticate();

        $download = Download::factory()->for($userA)->create([
            'status' => DownloadStatus::Completed,
            'file_path' => null,
            'fetched_at' => now()->subDays(10),
        ]);

        $this->post(route('downloads.refetch', $download))->assertNotFound();

        Queue::assertNothingPushed();
    }

    public function test_fetch_assigns_download_to_authenticated_user(): void
    {
        Queue::fake();

        $user = $this->authenticate();

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

        $this->post(route('fetch.store'), [
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $this->assertDatabaseHas('downloads', [
            'external_id' => 'dQw4w9WgXcQ',
            'user_id' => $user->id,
        ]);

        Queue::assertPushed(DownloadVideoJob::class);
    }
}
