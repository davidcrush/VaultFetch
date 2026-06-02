<?php

namespace App\Services;

use App\Data\VideoMetadata;
use App\Exceptions\YtDlpException;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class YtDlpService
{
    public function probe(string $url): VideoMetadata
    {
        $result = $this->execute([
            // '--no-warnings',
            // '--no-playlist',
            '-J',
            $url,
        ], config('vaultfetch.probe_timeout'), ['url' => $url]);

        if (! $result->successful()) {
            throw new YtDlpException(
                __('vaultfetch.messages.metadata_unavailable'),
                $result->errorOutput(),
            );
        }

        /** @var array<string, mixed>|null $data */
        $data = json_decode($result->output(), true);

        if (! is_array($data) || ! isset($data['id'], $data['title'])) {
            Log::warning('yt-dlp probe returned invalid metadata', [
                'url' => $url,
                'stdout' => $result->output(),
            ]);

            throw new YtDlpException(__('vaultfetch.messages.invalid_metadata'));
        }

        return new VideoMetadata(
            id: (string) $data['id'],
            title: (string) $data['title'],
            durationSeconds: (int) ($data['duration'] ?? 0),
            url: $url,
            thumbnail: isset($data['thumbnail']) ? (string) $data['thumbnail'] : null,
            ext: isset($data['ext']) ? (string) $data['ext'] : null,
        );
    }

    public function download(string $url, string $externalId): string
    {
        $outputDir = config('vaultfetch.output_dir');
        Storage::disk('local')->makeDirectory($outputDir);

        $outputTemplate = Storage::disk('local')->path(
            $outputDir.'/'.$externalId.'.%(ext)s',
        );

        $result = $this->execute([
            '--no-warnings',
            '--no-playlist',
            '-f', config('vaultfetch.format'),
            '--merge-output-format', config('vaultfetch.merge_output_format'),
            '-o', $outputTemplate,
            $url,
        ], config('vaultfetch.download_timeout'), [
            'url' => $url,
            'external_id' => $externalId,
        ]);

        if (! $result->successful()) {
            throw new YtDlpException(
                __('vaultfetch.messages.download_failed'),
                $result->errorOutput(),
            );
        }

        $files = glob(Storage::disk('local')->path($outputDir.'/'.$externalId.'.*'));

        if ($files === false || $files === []) {
            throw new YtDlpException(__('vaultfetch.messages.output_not_found'));
        }

        $absolutePath = $files[0];

        return $outputDir.'/'.basename($absolutePath);
    }

    /**
     * @param  list<string>  $args
     * @param  array<string, mixed>  $logContext
     */
    private function execute(array $args, int $timeout, array $logContext = []): ProcessResult
    {
        $command = $this->buildArgs($args);

        Log::info('yt-dlp command', [
            'command' => $this->formatCommandForLog($command),
        ]);

        $result = Process::timeout($timeout)->run($command);

        Log::log(
            $result->successful() ? 'info' : 'warning',
            'yt-dlp output',
            array_merge($logContext, [
                'exit_code' => $result->exitCode(),
                'stdout' => $result->output(),
                'stderr' => $result->errorOutput(),
            ]),
        );

        return $result;
    }

    /**
     * @param  list<string>  $command
     */
    private function formatCommandForLog(array $command): string
    {
        $redacted = [];

        for ($i = 0; $i < count($command); $i++) {
            if ($command[$i] === '--proxy' && isset($command[$i + 1])) {
                $redacted[] = $command[$i];
                $redacted[] = $this->redactProxyUrl($command[$i + 1]);
                $i++;

                continue;
            }

            $redacted[] = $command[$i];
        }

        return implode(' ', array_map(escapeshellarg(...), $redacted));
    }

    private function redactProxyUrl(string $proxy): string
    {
        $parts = parse_url($proxy);

        if ($parts === false || ! isset($parts['pass'])) {
            return $proxy;
        }

        $redacted = ($parts['scheme'] ?? 'http').'://';

        if (isset($parts['user'])) {
            $redacted .= $parts['user'].':***@';
        }

        $redacted .= $parts['host'] ?? '';

        if (isset($parts['port'])) {
            $redacted .= ':'.$parts['port'];
        }

        if (isset($parts['path']) && $parts['path'] !== '') {
            $redacted .= $parts['path'];
        }

        return $redacted;
    }

    /**
     * @param  list<string>  $args
     * @return list<string>
     */
    private function buildArgs(array $args): array
    {
        $command = [config('vaultfetch.ytdlp_binary')];

        $cookies = config('vaultfetch.cookies_file');

        if (is_string($cookies) && $cookies !== '') {
            if (file_exists($cookies)) {
                $command[] = '--cookies';
                $command[] = $cookies;
            } else {
                Log::warning('yt-dlp cookies file configured but not found', [
                    'path' => $cookies,
                ]);
            }
        }

        $proxy = config('vaultfetch.proxy');

        if (is_string($proxy) && $proxy !== '') {
            $command[] = '--proxy';
            $command[] = $proxy;
        }

        return array_merge($command, $args);
    }
}
