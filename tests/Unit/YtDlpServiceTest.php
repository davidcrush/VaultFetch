<?php

namespace Tests\Unit;

use App\Data\VideoMetadata;
use App\Exceptions\YtDlpException;
use App\Services\YtDlpService;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Log;
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

    public function test_probe_passes_proxy_when_configured(): void
    {
        config(['vaultfetch.proxy' => 'socks5://127.0.0.1:1080']);

        Process::fake([
            '*' => Process::result(
                output: json_encode([
                    'id' => 'abc123xyz00',
                    'title' => 'Sample Video',
                    'duration' => 125,
                ]),
            ),
        ]);

        app(YtDlpService::class)->probe('https://www.youtube.com/watch?v=abc123xyz00');

        Process::assertRan(function ($process): bool {
            $command = $process->command;

            return in_array('--proxy', $command, true)
                && in_array('socks5://127.0.0.1:1080', $command, true);
        });
    }

    public function test_probe_logs_command_with_redacted_proxy_password(): void
    {
        config(['vaultfetch.proxy' => 'http://proxyuser:secretpass@proxy.example:8080']);

        $logged = [];
        Log::listen(function (MessageLogged $event) use (&$logged): void {
            $logged[] = [
                'level' => $event->level,
                'message' => $event->message,
                'context' => $event->context,
            ];
        });

        Process::fake([
            '*' => Process::result(
                output: json_encode([
                    'id' => 'abc123xyz00',
                    'title' => 'Sample Video',
                    'duration' => 125,
                ]),
            ),
        ]);

        app(YtDlpService::class)->probe('https://www.youtube.com/watch?v=abc123xyz00');

        $commandLog = collect($logged)->first(
            fn (array $entry): bool => $entry['message'] === 'yt-dlp command',
        );

        $this->assertNotNull($commandLog);
        $this->assertSame('info', $commandLog['level']);
        $this->assertStringContainsString('http://proxyuser:***@proxy.example:8080', $commandLog['context']['command']);
        $this->assertStringNotContainsString('secretpass', $commandLog['context']['command']);
    }

    public function test_probe_logs_process_output_on_failure(): void
    {
        $logged = [];
        Log::listen(function (MessageLogged $event) use (&$logged): void {
            $logged[] = [
                'level' => $event->level,
                'message' => $event->message,
                'context' => $event->context,
            ];
        });

        Process::fake([
            '*' => Process::result(
                output: 'partial stdout',
                errorOutput: 'ERROR: unable to download',
                exitCode: 1,
            ),
        ]);

        try {
            app(YtDlpService::class)->probe('https://www.youtube.com/watch?v=abc123xyz00');
        } catch (YtDlpException) {
        }

        $outputLog = collect($logged)->first(
            fn (array $entry): bool => $entry['message'] === 'yt-dlp output',
        );

        $this->assertNotNull($outputLog);
        $this->assertSame('warning', $outputLog['level']);
        $this->assertSame(1, $outputLog['context']['exit_code']);
        $this->assertSame('partial stdout', rtrim($outputLog['context']['stdout']));
        $this->assertSame('ERROR: unable to download', rtrim($outputLog['context']['stderr']));
        $this->assertSame('https://www.youtube.com/watch?v=abc123xyz00', $outputLog['context']['url']);
    }
}
