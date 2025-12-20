<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{
    public function index()
    {
        $language = session('language', 'en'); // Default to English

        // Lees het JSON-bestand
        $jsonPath = storage_path('app/public/blog.json');
        $blogs = json_decode(File::get($jsonPath), true);

        // Sorteer blogs op datum (nieuwste eerst)
        usort($blogs, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Haal de content op in de geselecteerde taal voor elke blogpost
        foreach ($blogs as &$blog) {
            $blog['content'] = $blog['content'][$language] ?? $blog['content']['en']; // Standaard naar Engels
        }

        // Haal de laatste blogpost (meest recente)
        $latestBlog = $blogs[0] ?? null;
        $season = $this->currentSeason();

        return view('home', compact('blogs', 'latestBlog', 'language', 'season'));
    }

    public function showAllPosts()
    {
        $language = session('language', 'en'); // Default to English

        // Lees de JSON-bestanden in
        $jsonPath = storage_path('app/public/blog.json');
        $blogs = json_decode(File::get($jsonPath), true);

        return view('blog', compact('blogs', 'language'));
    }

    private function currentSeason(): string
    {
        $month = now()->month;

        return match (true) {
            $month >= 6 && $month <= 8 => 'summer',
            default => 'winter',
        };
    }
}
