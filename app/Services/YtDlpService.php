<?php

namespace App\Services;

use App\Data\VideoMetadata;
use App\Exceptions\YtDlpException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class YtDlpService
{
    public function probe(string $url): VideoMetadata
    {
        $result = Process::timeout(config('vaultfetch.probe_timeout'))
            ->run($this->buildArgs([
                '--no-warnings',
                '--no-playlist',
                '-J',
                $url,
            ]));

        if (! $result->successful()) {
            Log::warning('yt-dlp probe failed', [
                'exit_code' => $result->exitCode(),
                'url' => $url,
            ]);

            throw new YtDlpException(
                'Unable to fetch video metadata.',
                $result->errorOutput(),
            );
        }

        /** @var array<string, mixed>|null $data */
        $data = json_decode($result->output(), true);

        if (! is_array($data) || ! isset($data['id'], $data['title'])) {
            throw new YtDlpException('Invalid metadata response from yt-dlp.');
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

        $result = Process::timeout(config('vaultfetch.download_timeout'))
            ->run($this->buildArgs([
                '--no-warnings',
                '--no-playlist',
                '-f', config('vaultfetch.format'),
                '--merge-output-format', config('vaultfetch.merge_output_format'),
                '-o', $outputTemplate,
                $url,
            ]));

        if (! $result->successful()) {
            Log::warning('yt-dlp download failed', [
                'exit_code' => $result->exitCode(),
                'external_id' => $externalId,
            ]);

            throw new YtDlpException(
                'Video download failed.',
                $result->errorOutput(),
            );
        }

        $files = glob(Storage::disk('local')->path($outputDir.'/'.$externalId.'.*'));

        if ($files === false || $files === []) {
            throw new YtDlpException('Download finished but no output file was found.');
        }

        $absolutePath = $files[0];

        return $outputDir.'/'.basename($absolutePath);
    }

    /**
     * @param  list<string>  $args
     * @return list<string>
     */
    private function buildArgs(array $args): array
    {
        $command = [config('vaultfetch.ytdlp_binary')];

        $cookies = config('vaultfetch.cookies_file');

        if (is_string($cookies) && $cookies !== '' && file_exists($cookies)) {
            $command[] = '--cookies';
            $command[] = $cookies;
        }

        return array_merge($command, $args);
    }
}
