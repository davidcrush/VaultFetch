<?php

return [

    'ytdlp_binary' => env('YT_DLP_BINARY', '/usr/local/bin/yt-dlp'),

    'output_dir' => env('YT_DLP_OUTPUT_DIR', 'downloads'),

    'format' => env('YT_DLP_FORMAT', 'bv*+ba/b'),

    'merge_output_format' => env('YT_DLP_MERGE_OUTPUT_FORMAT', 'mp4'),

    'probe_timeout' => (int) env('YT_DLP_PROBE_TIMEOUT', 30),

    'download_timeout' => (int) env('YT_DLP_DOWNLOAD_TIMEOUT', 600),

    'cookies_file' => env('YT_DLP_COOKIES_FILE'),

    'proxy' => env('YT_DLP_PROXY'),

    'allowed_hosts' => [
        'youtube.com',
        'www.youtube.com',
        'youtu.be',
        'm.youtube.com',
    ],

    'retention_days' => (int) env('YT_DLP_RETENTION_DAYS', 7),

    'recent_limit' => (int) env('VAULTFETCH_RECENT_LIMIT', 25),

    'admin_password' => env('VAULTFETCH_ADMIN_PASSWORD'),

];
