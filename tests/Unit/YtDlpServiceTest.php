<?php

namespace Tests\Unit;

use App\Data\VideoMetadata;
use App\Exceptions\YtDlpException;
use App\Services\YtDlpService;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class YtDlpServiceTest extends TestCase
{
    public function test_probe_parses_json_metadata(): void
    {
        Process::fake([
            '*' => Process::result(
                output: json_encode([
                    'id' => 'abc123xyz00',
                    'title' => 'Sample Video',
                    'duration' => 125,
                    'ext' => 'webm',
                    'thumbnail' => 'https://example.com/thumb.jpg',
                ]),
            ),
        ]);

        $metadata = app(YtDlpService::class)->probe('https://www.youtube.com/watch?v=abc123xyz00');

        $this->assertInstanceOf(VideoMetadata::class, $metadata);
        $this->assertSame('abc123xyz00', $metadata->id);
        $this->assertSame('Sample Video', $metadata->title);
        $this->assertSame(125, $metadata->durationSeconds);
        $this->assertSame('webm', $metadata->ext);

        Process::assertRan(function ($process) {
            $command = implode(' ', $process->command);

            return str_contains($command, '-J')
                && str_contains($command, 'https://www.youtube.com/watch?v=abc123xyz00');
        });
    }

    public function test_probe_throws_when_process_fails(): void
    {
        Process::fake([
            '*' => Process::result(
                output: '',
                errorOutput: 'failed',
                exitCode: 1,
            ),
        ]);

        $this->expectException(YtDlpException::class);

        app(YtDlpService::class)->probe('https://www.youtube.com/watch?v=abc123xyz00');
    }

    public function test_download_returns_relative_storage_path(): void
    {
        Storage::fake('local');

        Process::fake([
            '*' => Process::result(output: ''),
        ]);

        $outputDir = config('vaultfetch.output_dir');
        $absoluteDir = Storage::disk('local')->path($outputDir);
        mkdir($absoluteDir, 0777, true);
        file_put_contents($absoluteDir.'/video99.mp4', 'fake video content');

        $path = app(YtDlpService::class)->download(
            'https://www.youtube.com/watch?v=video99',
            'video99',
        );

        $this->assertSame('downloads/video99.mp4', $path);
    }
}
