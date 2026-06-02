<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $recentDownloads = $request->user()
            ->downloads()
            ->latest()
            ->limit(config('vaultfetch.recent_limit'))
            ->get();

        return view('home', [
            'recentDownloads' => $recentDownloads,
        ]);
    }
}
