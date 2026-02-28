<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Location;
use App\Models\NominalValue;
use App\Models\Postcard;
use App\Models\PostcardType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostcardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Postcard::class);

        $query = Postcard::query()->with(['country', 'postcardType']);

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->integer('country_id'));
        }
        if ($request->filled('postcard_type_id')) {
            $query->where('postcard_type_id', $request->integer('postcard_type_id'));
        }
        if ($request->filled('for_sale')) {
            $query->where('for_sale', (bool) (int) $request->input('for_sale'));
        }

        $postcards = $query->orderByDesc('created_at')->paginate(50);
        $countries = Country::orderBy('name')->get();
        $postcardTypes = PostcardType::orderBy('name')->get();

        return view('admin.postcards.index', compact('postcards', 'countries', 'postcardTypes'));
    }

    public function create()
    {
        $this->authorize('create', Postcard::class);

        return view('admin.postcards.create', [
            'countries' => Country::orderBy('name')->get(),
            'postcardTypes' => PostcardType::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'currencies' => Currency::orderBy('name')->get(),
            'nominalValues' => NominalValue::orderBy('name')->get(),
            'valuationImages' => DB::table('postcard_valuation_images')->orderBy('name')->get(),
            'colours' => DB::table('colours')->orderBy('name')->get(),
            'printTypes' => DB::table('print_types')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Postcard::class);

        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'postcard_type_id' => 'nullable|exists:postcard_types,id',
            'nominal_value_id' => 'nullable|exists:nominal_values,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'valuation_image_id' => 'nullable|exists:postcard_valuation_images,id',
            'colour_id' => 'nullable|exists:colours,id',
            'print_type_id' => 'nullable|exists:print_types,id',
            'occasion' => 'nullable|string|max:255',
            'michel_number' => 'nullable|string|max:255',
            'date_of_issue' => 'nullable|string|max:255',
            'front_image' => 'nullable|string',
            'special_features' => 'nullable|string',
            'stamp_text' => 'nullable|string|max:255',
            'stamp_date' => 'nullable|string|max:255',
            'stamp_location' => 'nullable|string|max:255',
            'unstamped' => 'nullable|boolean',
            'stamped' => 'nullable|boolean',
            'special_stamp' => 'nullable|boolean',
            'perforation' => 'nullable|boolean',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'purchasing_price' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'print_run' => 'nullable|integer|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'location_detail' => 'nullable|string|max:255',
            'personal_remarks' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');
        $validated['unstamped'] = $request->boolean('unstamped');
        $validated['stamped'] = $request->boolean('stamped');
        $validated['special_stamp'] = $request->boolean('special_stamp');
        $validated['perforation'] = $request->boolean('perforation');

        $postcard = Postcard::create($validated);

        return redirect()->route('admin.postcards.edit', $postcard)
            ->with('success', 'Postcard created. Upload images below.');
    }

    public function edit(Postcard $postcard)
    {
        $this->authorize('update', $postcard);

        $postcard->load(['images', 'files']);

        return view('admin.postcards.edit', [
            'postcard' => $postcard,
            'countries' => Country::orderBy('name')->get(),
            'postcardTypes' => PostcardType::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'currencies' => Currency::orderBy('name')->get(),
            'nominalValues' => NominalValue::orderBy('name')->get(),
            'valuationImages' => DB::table('postcard_valuation_images')->orderBy('name')->get(),
            'colours' => DB::table('colours')->orderBy('name')->get(),
            'printTypes' => DB::table('print_types')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Postcard $postcard)
    {
        $this->authorize('update', $postcard);

        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'postcard_type_id' => 'nullable|exists:postcard_types,id',
            'nominal_value_id' => 'nullable|exists:nominal_values,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'valuation_image_id' => 'nullable|exists:postcard_valuation_images,id',
            'colour_id' => 'nullable|exists:colours,id',
            'print_type_id' => 'nullable|exists:print_types,id',
            'occasion' => 'nullable|string|max:255',
            'michel_number' => 'nullable|string|max:255',
            'date_of_issue' => 'nullable|string|max:255',
            'front_image' => 'nullable|string',
            'special_features' => 'nullable|string',
            'stamp_text' => 'nullable|string|max:255',
            'stamp_date' => 'nullable|string|max:255',
            'stamp_location' => 'nullable|string|max:255',
            'unstamped' => 'nullable|boolean',
            'stamped' => 'nullable|boolean',
            'special_stamp' => 'nullable|boolean',
            'perforation' => 'nullable|boolean',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'purchasing_price' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'print_run' => 'nullable|integer|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'location_detail' => 'nullable|string|max:255',
            'personal_remarks' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');
        $validated['unstamped'] = $request->boolean('unstamped');
        $validated['stamped'] = $request->boolean('stamped');
        $validated['special_stamp'] = $request->boolean('special_stamp');
        $validated['perforation'] = $request->boolean('perforation');

        $postcard->update($validated);

        return redirect()->route('admin.postcards.edit', $postcard)
            ->with('success', 'Postcard updated!');
    }

    public function destroy(Postcard $postcard)
    {
        $this->authorize('delete', $postcard);

        $postcard->load(['images', 'files']);

        foreach ($postcard->media as $file) {
            if ($file->path) {
                Storage::disk($file->disk)->delete($file->path);
            }
        }

        Storage::disk('b2')->deleteDirectory('postcards/'.$postcard->id);

        $postcard->media()->delete();
        $postcard->delete();

        return redirect()->route('admin.postcards.index')
            ->with('success', 'Postcard deleted.');
    }
}
