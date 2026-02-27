{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('admin-content')
@php
    $totalItems = collect($sections)->sum('total');
    $totalForSale = collect($sections)->sum('for_sale');
    $totalCreatedThisWeek = collect($sections)->sum('created_this_week');
    $sectionRoutes = [
        'Books' => 'admin.books.index',
        'Items' => 'admin.items.index',
        'Banknotes' => 'admin.banknotes.index',
        'Coins' => 'admin.coins.index',
        'Magazines' => 'admin.magazines.index',
        'Newspapers' => 'admin.newspapers.index',
        'Postcards' => 'admin.postcards.index',
        'Stamps' => 'admin.stamps.index',
    ];
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('admin.books.create') }}"
            class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
            <p class="text-xs uppercase tracking-[0.15em] text-white/55">Quick Action</p>
            <p class="mt-1 text-sm font-semibold text-white">Add new book</p>
        </a>
        <a href="{{ route('admin.items.create') }}"
            class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
            <p class="text-xs uppercase tracking-[0.15em] text-white/55">Quick Action</p>
            <p class="mt-1 text-sm font-semibold text-white">Add new item</p>
        </a>
        <a href="{{ route('admin.lookups.index', ['type' => 'origins']) }}"
            class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
            <p class="text-xs uppercase tracking-[0.15em] text-white/55">Quick Action</p>
            <p class="mt-1 text-sm font-semibold text-white">Manage origins</p>
        </a>
        <a href="{{ route('admin.blog.index') }}"
            class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
            <p class="text-xs uppercase tracking-[0.15em] text-white/55">Quick Action</p>
            <p class="mt-1 text-sm font-semibold text-white">Manage blog posts</p>
        </a>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
        <h1 class="text-2xl font-semibold text-white">Admin Dashboard</h1>
        <p class="mt-2 text-white/70">
            Overview of all collection sections.
            <span class="text-white/85">Created this week since {{ $startOfWeek->format('Y-m-d') }}.</span>
        </p>

        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-xl bg-black/25 p-3">
                <p class="text-xs uppercase tracking-[0.15em] text-white/55">Total records</p>
                <p class="mt-1 text-2xl font-semibold text-white">{{ $totalItems }}</p>
            </div>
            <div class="rounded-xl bg-black/25 p-3">
                <p class="text-xs uppercase tracking-[0.15em] text-white/55">For sale</p>
                <p class="mt-1 text-2xl font-semibold text-white">{{ $totalForSale }}</p>
            </div>
            <div class="rounded-xl bg-black/25 p-3">
                <p class="text-xs uppercase tracking-[0.15em] text-white/55">Added this week</p>
                <p class="mt-1 text-2xl font-semibold text-white">{{ $totalCreatedThisWeek }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($sections as $section)
            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-lg font-semibold text-white">{{ $section['name'] }}</h2>
                    @if(isset($sectionRoutes[$section['name']]))
                        <a href="{{ route($sectionRoutes[$section['name']]) }}"
                            class="rounded-md bg-white/10 px-2 py-1 text-xs text-white/80 transition hover:bg-white/20 hover:text-white">
                            Manage
                        </a>
                    @endif
                </div>
                <dl class="mt-3 space-y-2 text-sm text-white/80">
                    <div class="flex items-center justify-between">
                        <dt>Total</dt>
                        <dd class="font-semibold text-white">{{ $section['total'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>For sale</dt>
                        <dd class="font-semibold text-white">{{ $section['for_sale'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Created this week</dt>
                        <dd class="font-semibold text-white">{{ $section['created_this_week'] }}</dd>
                    </div>
                </dl>
            </div>
        @endforeach
    </div>
</div>
@endsection
