<?php
// app/Http/Controllers/Public/SectionController.php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SectionController extends Controller
{
    public function index(string $section)
    {
        $enabled = Config::get("collector.enabled_sections.$section");

        if (!$enabled) {
            abort(404);
        }

        return view("sections.index", [
            'section' => $section,
            'title' => Str::title(str_replace('_', ' ', $section)),
        ]);
    }
}
