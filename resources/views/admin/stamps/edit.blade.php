{{-- resources/views/admin/stamps/edit.blade.php --}}

@extends('layouts.admin')

@section('admin-content')
        @php
        $val = fn(string $key, $fallback = '') => old($key, data_get($stamp, $key, $fallback));
        $forSaleOld = old('for_sale', $stamp->for_sale ?? false);
        $forSaleJs = filter_var($forSaleOld, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        @endphp

        <form id="stamp-form" action="{{ route('admin.stamps.update', $stamp) }}" method="POST"
            class="w-full mx-auto max-w-7xl">
            @csrf
            @method('PUT')

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Edit stamp</h1>
                    <p class="mt-1 text-sm text-white/60">Update the stamp details.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                    class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Back</a>
            </div>

            @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
            @endif

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
                    <h2 class="text-base font-semibold text-white mb-5">Public details</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Country</label>
                            <select name="country_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($countries as $c)
                                <option value="{{ $c->id }}" @selected((string)$val('country_id') === (string)$c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Currency</label>
                            <select name="currency_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($currencies as $c)
                                <option value="{{ $c->id }}" @selected((string)$val('currency_id') === (string)$c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Nominal value</label>
                            <select name="nominal_value_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($nominalValues as $nv)
                                <option value="{{ $nv->id }}" @selected((string)$val('nominal_value_id') === (string)$nv->id)>{{ $nv->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Type</label>
                            <select name="type_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($stampTypes as $t)
                                <option value="{{ $t->id }}" @selected((string)$val('type_id') === (string)$t->id)>{{ $t->name }}</option>
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
                            <label class="text-sm font-medium text-white/80">Michel number</label>
                            <input type="text" name="michel_number" value="{{ $val('michel_number') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Yvert & Tellier number</label>
                            <input type="text" name="yvert_tellier_number" value="{{ $val('yvert_tellier_number') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Date of issue</label>
                            <input type="text" name="date_of_issue" value="{{ $val('date_of_issue') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Occasion</label>
                            <input type="text" name="occasion" value="{{ $val('occasion') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Designer</label>
                            <select name="designer_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($designers as $d)
                                <option value="{{ $d->id }}" @selected((string)$val('designer_id') === (string)$d->id)>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Colour</label>
                            <select name="colour_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($colours as $c)
                                <option value="{{ $c->id }}" @selected((string)$val('colour_id') === (string)$c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Print type</label>
                            <select name="print_type_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($printTypes as $pt)
                                <option value="{{ $pt->id }}" @selected((string)$val('print_type_id') === (string)$pt->id)>{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Watermark</label>
                            <select name="watermark_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($watermarks as $w)
                                <option value="{{ $w->id }}" @selected((string)$val('watermark_id') === (string)$w->id)>{{ $w->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Gum</label>
                            <select name="gum_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($gums as $g)
                                <option value="{{ $g->id }}" @selected((string)$val('gum_id') === (string)$g->id)>{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Perforation type</label>
                            <select name="perforation_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($perforations as $p)
                                <option value="{{ $p->id }}" @selected((string)$val('perforation_id') === (string)$p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Printing house</label>
                            <select name="printing_house_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">—</option>
                                @foreach($printingHouses as $ph)
                                <option value="{{ $ph->id }}" @selected((string)$val('printing_house_id') === (string)$ph->id)>{{ $ph->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Width (mm)</label>
                            <input type="number" step="0.01" name="width" value="{{ $val('width') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Height (mm)</label>
                            <input type="number" step="0.01" name="height" value="{{ $val('height') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Print run</label>
                            <input type="number" name="print_run" value="{{ $val('print_run') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2 lg:col-span-2">
                            <label class="text-sm font-medium text-white/80">Illustration</label>
                            <textarea name="illustration" rows="3"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('illustration', $stamp->illustration) }}</textarea>
                        </div>

                        <div class="space-y-2 lg:col-span-2">
                            <label class="text-sm font-medium text-white/80">Special features</label>
                            <textarea name="special_features" rows="3"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('special_features', $stamp->special_features) }}</textarea>
                        </div>

                        <div class="space-y-2 lg:col-span-2">
                            <label class="text-sm font-medium text-white/80">Condition</label>
                            <div class="flex flex-wrap gap-6">
                                <label class="flex items-center gap-2 text-sm text-white/70">
                                    <input type="hidden" name="mnh" value="0">
                                    <input type="checkbox" name="mnh" value="1" @checked($val('mnh'))
                                        class="h-4 w-4 rounded border-white/20 bg-white/10">
                                    MNH
                                </label>
                                <label class="flex items-center gap-2 text-sm text-white/70">
                                    <input type="hidden" name="hinged" value="0">
                                    <input type="checkbox" name="hinged" value="1" @checked($val('hinged'))
                                        class="h-4 w-4 rounded border-white/20 bg-white/10">
                                    Hinged
                                </label>
                                <label class="flex items-center gap-2 text-sm text-white/70">
                                    <input type="hidden" name="postmarked" value="0">
                                    <input type="checkbox" name="postmarked" value="1" @checked($val('postmarked'))
                                        class="h-4 w-4 rounded border-white/20 bg-white/10">
                                    Postmarked
                                </label>
                                <label class="flex items-center gap-2 text-sm text-white/70">
                                    <input type="hidden" name="special_postmark" value="0">
                                    <input type="checkbox" name="special_postmark" value="1" @checked($val('special_postmark'))
                                        class="h-4 w-4 rounded border-white/20 bg-white/10">
                                    Special postmark
                                </label>
                                <label class="flex items-center gap-2 text-sm text-white/70">
                                    <input type="hidden" name="perforation" value="0">
                                    <input type="checkbox" name="perforation" value="1" @checked($val('perforation'))
                                        class="h-4 w-4 rounded border-white/20 bg-white/10">
                                    Perforation
                                </label>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Postmark date</label>
                            <input type="text" name="postmark_date" value="{{ $val('postmark_date') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Postmark location</label>
                            <input type="text" name="postmark_location" value="{{ $val('postmark_location') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2 lg:col-span-2">
                            <label class="text-sm font-medium text-white/80">Postmark text</label>
                            <input type="text" name="postmark_text" value="{{ $val('postmark_text') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Condition</label>
                            <select name="condition" class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                <option value="">— Not graded —</option>
                                @foreach(['Mint','Extremely Fine','Very Fine','Fine','Very Good','Good','Poor'] as $grade)
                                <option value="{{ $grade }}" @selected(old('condition', $stamp->condition ?? '') === $grade)>{{ $grade }}</option>
                                @endforeach
                            </select>
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
                            <input type="date" name="purchase_date"
                                value="{{ old('purchase_date', $stamp->purchase_date?->format('Y-m-d') ?? '') }}"
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
                            <div class="flex items-center gap-2">
                                <select id="location_id" name="location_id" class="js-select w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                    <option value="">—</option>
                                    @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" @selected((string)$val('location_id') === (string)$loc->id)>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button"
                                    class="h-10 w-10 shrink-0 rounded-md border border-white/10 bg-white/10 text-white hover:bg-white/15"
                                    data-lookup-add data-type="location" data-select="#location_id"
                                    title="Add location">+</button>
                            </div>
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

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Location detail</label>
                            <input type="text" name="location_detail" value="{{ $val('location_detail') }}"
                                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                        </div>

                        {{-- Sold --}}
                        <div x-data="{ sold: {{ old('sold_at', $stamp->sold_at ?? null) ? 'true' : 'false' }} }" class="space-y-2">
                            <label class="text-sm font-medium text-white/80">Sold</label>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="sold" value="0">
                                <input type="checkbox" value="1" x-model="sold"
                                    class="h-5 w-5 rounded border-white/20 bg-white/10">
                                <span class="text-sm text-white/70">Mark as sold</span>
                            </div>
                            <div x-show="sold" x-cloak class="grid grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="text-sm font-medium text-white/80">Sold on</label>
                                    <input type="date" name="sold_at"
                                        value="{{ old('sold_at', $stamp->sold_at?->format('Y-m-d') ?? '') }}"
                                        class="mt-1 w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-white/80">Sold price €</label>
                                    <input type="number" step="0.01" name="sold_price"
                                        value="{{ old('sold_price', $stamp->sold_price ?? '') }}"
                                        class="mt-1 w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <label class="text-sm font-medium text-white/80">Personal remarks</label>
                        <textarea name="personal_remarks" rows="4"
                            class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('personal_remarks', $stamp->personal_remarks) }}</textarea>
                    </div>
                </section>

        </form>

                {{-- MEDIA --}}
                <section class="rounded-xl border border-black/20 bg-black/10 p-6 space-y-6">
                    <h2 class="text-base font-semibold text-white">Media</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-md bg-sage-900 p-4 border border-white/10">
                            <div class="text-white font-semibold mb-2">Upload images</div>
                            <form action="{{ route('admin.media.store', ['type' => 'stamps', 'id' => $stamp->id]) }}"
                                method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                                @csrf
                                <input type="hidden" name="collection" value="images">
                                <input type="file" name="files[]" multiple accept="image/*"
                                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-white hover:file:bg-white/15">
                                <button type="submit" class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Upload images</button>
                            </form>
                        </div>
                        <div class="rounded-md bg-sage-900 p-4 border border-white/10">
                            <div class="text-white font-semibold mb-2">Upload PDFs</div>
                            <form action="{{ route('admin.media.store', ['type' => 'stamps', 'id' => $stamp->id]) }}"
                                method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                                @csrf
                                <input type="hidden" name="collection" value="files">
                                <input type="file" name="files[]" multiple accept="application/pdf"
                                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-white hover:file:bg-white/15">
                                <button type="submit" class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Upload PDFs</button>
                            </form>
                        </div>
                    </div>

                    <p class="text-xs text-white/50">Max 50MB per file.</p>

                    @php
                    $mediaImages = $stamp->images;
                    $mediaPdfs = $stamp->files->filter(fn($f) => $f->isPdf())->values();
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-sage rounded-md p-4 border border-black/20">
                            <h3 class="text-lg font-semibold text-white mb-3">Images ({{ $mediaImages->count() }})</h3>
                            @if($mediaImages->isEmpty())
                            <p class="text-white/80 text-sm">No images uploaded yet.</p>
                            @else
                            <div class="flex flex-wrap gap-2 items-start"
                                data-reorder-container
                                data-reorder-url="{{ route('admin.media.reorder', ['type' => 'stamps', 'id' => $stamp->id]) }}">
                                @foreach($mediaImages as $img)
                                @include('admin.books._image-card', ['img' => $img, 'type' => 'stamps'])
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="bg-sage rounded-md p-4 border border-black/20">
                            <h3 class="text-lg font-semibold text-white mb-3">PDFs ({{ $mediaPdfs->count() }})</h3>
                            @if($mediaPdfs->isEmpty())
                            <p class="text-white/80 text-sm">No PDFs uploaded yet.</p>
                            @else
                            <div class="space-y-4">
                                @foreach($mediaPdfs as $pdf)
                                @include('admin.books._pdf-card', ['pdf' => $pdf, 'type' => 'stamps'])
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </section>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.dashboard') }}"
                        class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Cancel</a>
                    <button type="submit" form="stamp-form"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">Save changes</button>
                </div>
            </div>

    @include('admin.partials.lookup-modal')
@endsection
