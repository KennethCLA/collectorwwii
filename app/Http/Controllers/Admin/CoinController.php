<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coin;
use App\Models\CoinMaterial;
use App\Models\CoinOccasion;
use App\Models\CoinShape;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Location;
use App\Models\NominalValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Coin::class);

        $query = Coin::query()->with(['country', 'nominalValue', 'material']);

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->integer('country_id'));
        }
        if ($request->filled('material_id')) {
            $query->where('material_id', $request->integer('material_id'));
        }
        if ($request->filled('for_sale')) {
            $query->where('for_sale', (bool) (int) $request->input('for_sale'));
        }

        $coins = $query->orderByDesc('created_at')->paginate(50);
        $countries = Country::orderBy('name')->get();
        $materials = CoinMaterial::orderBy('name')->get();

        return view('admin.coins.index', compact('coins', 'countries', 'materials'));
    }

    public function create()
    {
        $this->authorize('create', Coin::class);

        return view('admin.coins.create', [
            'countries' => Country::orderBy('name')->get(),
            'currencies' => Currency::orderBy('name')->get(),
            'nominalValues' => NominalValue::orderBy('name')->get(),
            'shapes' => CoinShape::orderBy('name')->get(),
            'materials' => CoinMaterial::orderBy('name')->get(),
            'occasions' => CoinOccasion::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Coin::class);

        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'nominal_value_id' => 'nullable|exists:nominal_values,id',
            'shape_id' => 'nullable|exists:coin_shapes,id',
            'material_id' => 'nullable|exists:coin_materials,id',
            'year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'occasion_id' => 'nullable|exists:coin_occasions,id',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'purchasing_price' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'personal_remarks' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');

        $coin = Coin::create($validated);

        return redirect()->route('admin.coins.edit', $coin)
            ->with('success', 'Coin created. Upload images below.');
    }

    public function edit(Coin $coin)
    {
        $this->authorize('update', $coin);

        $coin->load(['images', 'files']);

        return view('admin.coins.edit', [
            'coin' => $coin,
            'countries' => Country::orderBy('name')->get(),
            'currencies' => Currency::orderBy('name')->get(),
            'nominalValues' => NominalValue::orderBy('name')->get(),
            'shapes' => CoinShape::orderBy('name')->get(),
            'materials' => CoinMaterial::orderBy('name')->get(),
            'occasions' => CoinOccasion::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Coin $coin)
    {
        $this->authorize('update', $coin);

        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'nominal_value_id' => 'nullable|exists:nominal_values,id',
            'shape_id' => 'nullable|exists:coin_shapes,id',
            'material_id' => 'nullable|exists:coin_materials,id',
            'year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'occasion_id' => 'nullable|exists:coin_occasions,id',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'purchasing_price' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'personal_remarks' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');

        $coin->update($validated);

        return redirect()->route('admin.coins.edit', $coin)
            ->with('success', 'Coin updated!');
    }

    public function destroy(Coin $coin)
    {
        $this->authorize('delete', $coin);

        $coin->load(['images', 'files']);

        foreach ($coin->media as $file) {
            if ($file->path) {
                Storage::disk($file->disk)->delete($file->path);
            }
        }

        Storage::disk('b2')->deleteDirectory('coins/'.$coin->id);

        $coin->media()->delete();
        $coin->delete();

        return redirect()->route('admin.coins.index')
            ->with('success', 'Coin deleted.');
    }
}
