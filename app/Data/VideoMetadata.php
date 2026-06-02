<?php

namespace App\Data;

readonly class VideoMetadata
{
    public function __construct(
        public string $id,
        public string $title,
        public int $durationSeconds,
        public string $url,
        public ?string $thumbnail = null,
        public ?string $ext = null,
    ) {}
}
