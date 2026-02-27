<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Location;
use App\Models\NominalValue;
use App\Models\Stamp;
use App\Models\StampType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StampController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Stamp::class);

        $query = Stamp::query()->with(['country', 'nominalValue', 'stampType']);

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->integer('country_id'));
        }
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->integer('type_id'));
        }
        if ($request->filled('for_sale')) {
            $query->where('for_sale', (bool) (int) $request->input('for_sale'));
        }

        $stamps = $query->orderByDesc('created_at')->paginate(50);
        $countries = Country::orderBy('name')->get();
        $stampTypes = StampType::orderBy('name')->get();

        return view('admin.stamps.index', compact('stamps', 'countries', 'stampTypes'));
    }

    public function create()
    {
        $this->authorize('create', Stamp::class);

        return view('admin.stamps.create', [
            'countries'    => Country::orderBy('name')->get(),
            'currencies'   => Currency::orderBy('name')->get(),
            'nominalValues' => NominalValue::orderBy('name')->get(),
            'stampTypes'   => StampType::orderBy('name')->get(),
            'locations'    => Location::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Stamp::class);

        $validated = $request->validate([
            'country_id'       => 'nullable|exists:countries,id',
            'currency_id'      => 'nullable|exists:currencies,id',
            'nominal_value_id' => 'nullable|exists:nominal_values,id',
            'type_id'          => 'nullable|exists:stamp_types,id',
            'year'             => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'michel_number'    => 'nullable|string|max:255',
            'for_sale'         => 'nullable|boolean',
            'selling_price'    => 'nullable|numeric|min:0',
            'purchase_date'    => 'nullable|date',
            'purchasing_price' => 'nullable|numeric|min:0',
            'current_value'    => 'nullable|numeric|min:0',
            'location_id'      => 'nullable|exists:locations,id',
            'personal_remarks' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');

        $stamp = Stamp::create($validated);

        return redirect()->route('admin.stamps.edit', $stamp)
            ->with('success', 'Stamp created. Upload images below.');
    }

    public function edit(Stamp $stamp)
    {
        $this->authorize('update', $stamp);

        $stamp->load(['images', 'files']);

        return view('admin.stamps.edit', [
            'stamp'        => $stamp,
            'countries'    => Country::orderBy('name')->get(),
            'currencies'   => Currency::orderBy('name')->get(),
            'nominalValues' => NominalValue::orderBy('name')->get(),
            'stampTypes'   => StampType::orderBy('name')->get(),
            'locations'    => Location::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Stamp $stamp)
    {
        $this->authorize('update', $stamp);

        $validated = $request->validate([
            'country_id'       => 'nullable|exists:countries,id',
            'currency_id'      => 'nullable|exists:currencies,id',
            'nominal_value_id' => 'nullable|exists:nominal_values,id',
            'type_id'          => 'nullable|exists:stamp_types,id',
            'year'             => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'michel_number'    => 'nullable|string|max:255',
            'for_sale'         => 'nullable|boolean',
            'selling_price'    => 'nullable|numeric|min:0',
            'purchase_date'    => 'nullable|date',
            'purchasing_price' => 'nullable|numeric|min:0',
            'current_value'    => 'nullable|numeric|min:0',
            'location_id'      => 'nullable|exists:locations,id',
            'personal_remarks' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');

        $stamp->update($validated);

        return redirect()->route('admin.stamps.edit', $stamp)
            ->with('success', 'Stamp updated!');
    }

    public function destroy(Stamp $stamp)
    {
        $this->authorize('delete', $stamp);

        $stamp->load(['images', 'files']);

        foreach ($stamp->media as $file) {
            if ($file->path) {
                Storage::disk($file->disk)->delete($file->path);
            }
        }

        Storage::disk('b2')->deleteDirectory('stamps/' . $stamp->id);

        $stamp->media()->delete();
        $stamp->delete();

        return redirect()->route('admin.stamps.index')
            ->with('success', 'Stamp deleted.');
    }
}
