{{-- resources/views/admin/postcards/create.blade.php --}}

<x-layout>
    <x-form-layout>
        @php
        $val = fn(string $key, $fallback = '') => old($key, $fallback);
        $forSaleJs = old('for_sale') ? 'true' : 'false';
        @endphp

        <form action="{{ route('admin.postcards.store') }}" method="POST" class="w-full mx-auto max-w-7xl">
            @csrf

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Create postcard</h1>
                    <p class="mt-1 text-sm text-white/60">Add a new postcard to the collection.</p>
                </div>
                <a href="{{ route('admin.postcards.index') }}" class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Back</a>
            </div>

            @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-100">
                <div class="font-semibold mb-2">Please fix the following:</div>
                <ul class="list-disc pl-5 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="rounded-xl border border-black/20 bg-black/10 p-6 space-y-8">

                <section class="rounded-xl border border-black/20 bg-black/10 p-6">
                    <h2 class="text-base font-semibold text-white mb-5">Public details</h2>
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
                            <label class="text-sm font-medium text-white/80">Year</label>
                            <input type="number" name="year" value="{{ $val('year') }}" min="1800" max="{{ date('Y') + 1 }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Postcard type</label>
                            <select name="postcard_type_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($postcardTypes as $t)
                                <option value="{{ $t->id }}" @selected($val('postcard_type_id') == $t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Occasion</label>
                            <input type="text" name="occasion" value="{{ $val('occasion') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2 lg:col-span-2">
                            <label class="text-sm font-medium text-white/80">Stamp status</label>
                            <div class="flex flex-wrap gap-6">
                                <label class="flex items-center gap-2 text-sm text-white/70">
                                    <input type="checkbox" name="unstamped" value="1" @checked($val('unstamped'))
                                        class="h-4 w-4 rounded border-white/20 bg-white/10">
                                    Unstamped
                                </label>
                                <label class="flex items-center gap-2 text-sm text-white/70">
                                    <input type="checkbox" name="stamped" value="1" @checked($val('stamped'))
                                        class="h-4 w-4 rounded border-white/20 bg-white/10">
                                    Stamped
                                </label>
                                <label class="flex items-center gap-2 text-sm text-white/70">
                                    <input type="checkbox" name="special_stamp" value="1" @checked($val('special_stamp'))
                                        class="h-4 w-4 rounded border-white/20 bg-white/10">
                                    Special stamp
                                </label>
                            </div>
                        </div>
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
                            <label class="text-sm font-medium text-white/80">Current value €</label>
                            <input type="number" step="0.01" name="current_value" value="{{ $val('current_value') }}"
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
                        <div x-data="{ forSale: {{ $forSaleJs }} }" class="space-y-2">
                            <label class="text-sm font-medium text-white/80">For sale</label>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="for_sale" value="0">
                                <input type="checkbox" name="for_sale" value="1" x-model="forSale" class="h-5 w-5 rounded border-white/20 bg-white/10">
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
                    <a href="{{ route('admin.postcards.index') }}" class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Cancel</a>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">Create postcard</button>
                </div>
            </div>
        </form>
    </x-form-layout>
</x-layout>
