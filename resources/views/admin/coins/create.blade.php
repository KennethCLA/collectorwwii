{{-- resources/views/admin/coins/create.blade.php --}}

@extends('layouts.admin')

@section('admin-content')
        @php
        $val = fn(string $key, $fallback = '') => old($key, $fallback);
        $forSaleJs = old('for_sale') ? 'true' : 'false';
        @endphp

        <form action="{{ route('admin.coins.store') }}" method="POST"
            class="w-full mx-auto max-w-7xl">
            @csrf

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Create coin</h1>
                    <p class="mt-1 text-sm text-white/60">Add a new coin to the collection.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                    class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Back</a>
            </div>

            @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">
                <div class="font-semibold mb-2">Please fix the following:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="rounded-xl border border-black/20 bg-black/10 p-6 space-y-8">

                <section class="rounded-xl border border-black/20 bg-black/10 p-6">
                    <div class="flex items-center justify-between gap-4 mb-5">
                        <h2 class="text-base font-semibold text-white">Public details</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Country</label>
                            <select name="country_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($countries as $c)
                                <option value="{{ $c->id }}" @selected($val('country_id') == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Currency</label>
                            <select name="currency_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($currencies as $c)
                                <option value="{{ $c->id }}" @selected($val('currency_id') == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Nominal value</label>
                            <select name="nominal_value_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($nominalValues as $nv)
                                <option value="{{ $nv->id }}" @selected($val('nominal_value_id') == $nv->id)>{{ $nv->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Shape</label>
                            <select name="shape_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($shapes as $s)
                                <option value="{{ $s->id }}" @selected($val('shape_id') == $s->id)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Material</label>
                            <select name="material_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($materials as $m)
                                <option value="{{ $m->id }}" @selected($val('material_id') == $m->id)>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Year</label>
                            <input type="number" name="year" value="{{ $val('year') }}"
                                min="1800" max="{{ date('Y') + 1 }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Occasion</label>
                            <select name="occasion_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($occasions as $o)
                                <option value="{{ $o->id }}" @selected($val('occasion_id') == $o->id)>{{ $o->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Head of state</label>
                            <select name="head_of_state_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($headsOfState as $item)
                                <option value="{{ $item->id }}" @selected($val('head_of_state_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Strike mark</label>
                            <select name="strike_mark_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($strikeMarks as $item)
                                <option value="{{ $item->id }}" @selected($val('strike_mark_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Designer</label>
                            <select name="designer_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($designers as $item)
                                <option value="{{ $item->id }}" @selected($val('designer_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Front image</label>
                            <select name="front_image_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($frontImages as $item)
                                <option value="{{ $item->id }}" @selected($val('front_image_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Front text</label>
                            <select name="front_text_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($frontTexts as $item)
                                <option value="{{ $item->id }}" @selected($val('front_text_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Reverse image</label>
                            <select name="reverse_image_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($reverseImages as $item)
                                <option value="{{ $item->id }}" @selected($val('reverse_image_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Reverse text</label>
                            <select name="reverse_text_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($reverseTexts as $item)
                                <option value="{{ $item->id }}" @selected($val('reverse_text_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Rim</label>
                            <select name="rim_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($rims as $item)
                                <option value="{{ $item->id }}" @selected($val('rim_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Rim text</label>
                            <select name="rim_text_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($rimTexts as $item)
                                <option value="{{ $item->id }}" @selected($val('rim_text_id') == $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Time period</label>
                            <input type="text" name="time_period" value="{{ $val('time_period') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Number Jaeger</label>
                            <input type="text" name="number_jaeger" value="{{ $val('number_jaeger') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Date of issue</label>
                            <input type="date" name="date_of_issue" value="{{ $val('date_of_issue') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Gold/silver content</label>
                            <input type="number" step="0.01" name="gold_silver_content" value="{{ $val('gold_silver_content') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Weight (g)</label>
                            <input type="number" step="0.01" name="weight" value="{{ $val('weight') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Diameter (mm)</label>
                            <input type="number" step="0.01" name="diameter" value="{{ $val('diameter') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Thickness (mm)</label>
                            <input type="number" step="0.01" name="thickness" value="{{ $val('thickness') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Run / Mintage</label>
                            <input type="number" name="run" value="{{ $val('run') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <label class="text-sm font-medium text-white/80">Special features</label>
                        <textarea name="special_features" rows="3"
                            class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('special_features') }}</textarea>
                    </div>
                </section>

                <section class="rounded-xl border border-white/10 bg-black/20 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <h2 class="text-base font-semibold text-white">Admin-only</h2>
                        <span class="inline-flex items-center rounded-full bg-white/10 px-2 py-0.5 text-xs text-white/70">Not visible publicly</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Purchase date</label>
                            <input type="date" name="purchase_date" value="{{ $val('purchase_date') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Purchasing price €</label>
                            <input type="number" step="0.01" name="purchasing_price" value="{{ $val('purchasing_price') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Location</label>
                            <select name="location_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" @selected($val('location_id') == $loc->id)>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Current value &euro;</label>
                            <input type="number" step="0.01" name="current_value" value="{{ $val('current_value') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Location detail</label>
                            <input type="text" name="location_detail" value="{{ $val('location_detail') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div x-data="{ forSale: {{ $forSaleJs }} }" class="space-y-2">
                            <label class="text-sm font-medium text-white/80">For sale</label>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="for_sale" value="0">
                                <input type="checkbox" name="for_sale" value="1" x-model="forSale"
                                    class="h-5 w-5 rounded border-white/20 bg-white/10">
                                <span class="text-sm text-white/70">Mark as for sale</span>
                            </div>
                            <div x-show="forSale" x-cloak class="pt-2">
                                <label class="text-sm font-medium text-white/80">Selling price €</label>
                                <input type="number" step="0.01" name="selling_price" value="{{ $val('selling_price') }}"
                                    class="mt-2 w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <label class="text-sm font-medium text-white/80">Personal remarks</label>
                        <textarea name="personal_remarks" rows="4"
                            class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('personal_remarks') }}</textarea>
                    </div>
                </section>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.dashboard') }}"
                        class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Cancel</a>
                    <button type="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">Create coin</button>
                </div>
            </div>
        </form>
@endsection
