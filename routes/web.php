<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'recentDownloads' => [
            ['title' => 'Amazing Nature Documentary', 'duration' => '45:12', 'fetchedAt' => '2024-05-20 14:30'],
            ['title' => 'Introduction to Laravel Queues', 'duration' => '18:44', 'fetchedAt' => '2024-05-19 09:15'],
            ['title' => 'City Timelapse — Tokyo at Night', 'duration' => '12:03', 'fetchedAt' => '2024-05-18 21:42'],
            ['title' => 'Home Studio Tour 2024', 'duration' => '32:17', 'fetchedAt' => '2024-05-17 16:08'],
            ['title' => 'Classic Jazz Performance (Live)', 'duration' => '58:29', 'fetchedAt' => '2024-05-16 11:55'],
            ['title' => 'Building a Homelab from Scratch', 'duration' => '24:51', 'fetchedAt' => '2024-05-15 08:20'],
            ['title' => 'Ocean Waves — 4K Relaxation', 'duration' => '1:02:10', 'fetchedAt' => '2024-05-14 19:33'],
            ['title' => 'Retro Game Speedrun Highlights', 'duration' => '15:38', 'fetchedAt' => '2024-05-13 13:47'],
            ['title' => 'Cooking: Perfect Sourdough Bread', 'duration' => '21:06', 'fetchedAt' => '2024-05-12 07:12'],
            ['title' => 'Space Documentary — Mars Missions', 'duration' => '47:55', 'fetchedAt' => '2024-05-11 22:01'],
        ],
    ]);
});
