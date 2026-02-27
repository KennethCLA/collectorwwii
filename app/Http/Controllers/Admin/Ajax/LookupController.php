<?php

// app/Http/Controllers/Admin/Ajax/LookupController.php

namespace App\Http\Controllers\Admin\Ajax;

use App\Http\Controllers\Controller;
use App\Models\BookCover;
use App\Models\BookSeries;
use App\Models\BookTopic;
use App\Models\Location;
use App\Models\Origin;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function store(Request $request, string $type)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $name = trim($data['name']);

        $map = [
            'topic' => BookTopic::class,
            'series' => BookSeries::class,
            'cover' => BookCover::class,
            'location' => Location::class,
            'origin' => Origin::class,
        ];

        abort_unless(isset($map[$type]), 404);

        $modelClass = $map[$type];

        try {
            $row = $modelClass::firstOrCreate(['name' => $name]);
        } catch (UniqueConstraintViolationException) {
            $row = $modelClass::where('name', $name)->firstOrFail();
        }

        return response()->json([
            'id' => $row->id,
            'name' => $row->name,
        ]);
    }
}
