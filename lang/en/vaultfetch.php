<?php

return [

    'app' => [
        'name' => 'VaultFetch',
        'tagline' => 'Local Media Downloader',
        'logo_alt' => 'VaultFetch — Local Media Downloader',
    ],

    'auth' => [
        'sign_in' => 'Sign in',
        'sign_out' => 'Sign out',
        'email' => 'Email',
        'password' => 'Password',
        'remember_me' => 'Remember me',
    ],

    'home' => [
        'enter_video_url' => 'Enter Video URL',
        'url_placeholder' => 'https://www.youtube.com/watch?v=...',
        'fetch' => 'Fetch',
        'fetching' => 'Fetching…',
        'recent_downloads' => 'Recent Downloads',
        'recent_downloads_subtitle' => 'Last :count videos fetched',
        'no_downloads' => 'No downloads yet. Paste a URL above to fetch your first video.',
    ],

    'downloads' => [
        'back' => 'Back',
        'view' => 'View',
        'download' => 'Download',
        'refetch' => 'Refetch',
        'processing' => 'Processing…',
        'unsupported_browser' => 'Your browser does not support embedded video playback.',
    ],

    'metadata' => [
        'duration' => 'Duration',
        'added' => 'Added',
        'expires' => 'Expires',
        'expired' => 'Expired',
    ],

    'status' => [
        'pending' => 'Pending',
        'downloading' => 'Downloading',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ],

    'duration' => [
        'empty' => '—',
        'hour' => '{1} :count hour|[2,*] :count hours',
        'minute' => '{1} :count minute|[2,*] :count minutes',
        'second' => '{1} :count second|[2,*] :count seconds',
    ],

    'messages' => [
        'download_queued' => 'Download queued for ":title".',
        'refetch_queued' => 'Re-download queued for ":title".',
        'file_not_available' => 'This file is not available yet.',
        'cannot_refetch' => 'This download cannot be refetched.',
        'metadata_unavailable' => 'Unable to fetch video metadata.',
        'invalid_metadata' => 'Invalid metadata response from yt-dlp.',
        'download_failed' => 'Video download failed.',
        'output_not_found' => 'Download finished but no output file was found.',
        'job_failed' => 'Download failed.',
    ],

    'validation' => [
        'unsupported_host' => 'Only supported video hosts are allowed.',
    ],

];
