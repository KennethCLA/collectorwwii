<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banknote;
use App\Models\BanknoteSeries;
use App\Models\BanknoteTimePeriod;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Location;
use App\Models\NominalValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BanknoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Banknote::class);

        $query = Banknote::query()->with(['country', 'currency', 'nominalValue']);

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->integer('country_id'));
        }
        if ($request->filled('currency_id')) {
            $query->where('currency_id', $request->integer('currency_id'));
        }
        if ($request->filled('for_sale')) {
            $query->where('for_sale', (bool) (int) $request->input('for_sale'));
        }

        $banknotes = $query->orderByDesc('created_at')->paginate(50);
        $countries = Country::orderBy('name')->get();
        $currencies = Currency::orderBy('name')->get();

        return view('admin.banknotes.index', compact('banknotes', 'countries', 'currencies'));
    }

    public function create()
    {
        $this->authorize('create', Banknote::class);

        return view('admin.banknotes.create', [
            'countries' => Country::orderBy('name')->get(),
            'currencies' => Currency::orderBy('name')->get(),
            'nominalValues' => NominalValue::orderBy('name')->get(),
            'seriesList' => BanknoteSeries::orderBy('name')->get(),
            'timePeriods' => BanknoteTimePeriod::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Banknote::class);

        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'nominal_value_id' => 'nullable|exists:nominal_values,id',
            'series_id' => 'nullable|exists:banknote_series,id',
            'time_period_id' => 'nullable|exists:banknote_time_periods,id',
            'year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'variation' => 'nullable|string|max:255',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'purchasing_price' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'personal_remarks' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');

        $banknote = Banknote::create($validated);

        return redirect()->route('admin.banknotes.edit', $banknote)
            ->with('success', 'Banknote created. Upload images below.');
    }

    public function edit(Banknote $banknote)
    {
        $this->authorize('update', $banknote);

        $banknote->load(['images', 'files']);

        return view('admin.banknotes.edit', [
            'banknote' => $banknote,
            'countries' => Country::orderBy('name')->get(),
            'currencies' => Currency::orderBy('name')->get(),
            'nominalValues' => NominalValue::orderBy('name')->get(),
            'seriesList' => BanknoteSeries::orderBy('name')->get(),
            'timePeriods' => BanknoteTimePeriod::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Banknote $banknote)
    {
        $this->authorize('update', $banknote);

        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'nominal_value_id' => 'nullable|exists:nominal_values,id',
            'series_id' => 'nullable|exists:banknote_series,id',
            'time_period_id' => 'nullable|exists:banknote_time_periods,id',
            'year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'variation' => 'nullable|string|max:255',
            'for_sale' => 'nullable|boolean',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'purchasing_price' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:locations,id',
            'personal_remarks' => 'nullable|string',
        ]);

        $validated['for_sale'] = $request->boolean('for_sale');

        $banknote->update($validated);

        return redirect()->route('admin.banknotes.edit', $banknote)
            ->with('success', 'Banknote updated!');
    }

    public function destroy(Banknote $banknote)
    {
        $this->authorize('delete', $banknote);

        $banknote->load(['images', 'files']);

        foreach ($banknote->media as $file) {
            if ($file->path) {
                Storage::disk($file->disk)->delete($file->path);
            }
        }

        Storage::disk('b2')->deleteDirectory('banknotes/'.$banknote->id);

        $banknote->media()->delete();
        $banknote->delete();

        return redirect()->route('admin.banknotes.index')
            ->with('success', 'Banknote deleted.');
    }
}
