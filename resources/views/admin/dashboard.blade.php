{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('admin-content')
@php
    $totalItems = collect($sections)->sum('total');
    $totalForSale = collect($sections)->sum('for_sale');
    $totalCreatedThisWeek = collect($sections)->sum('created_this_week');
    $sectionRoutes = [
        'Books'      => 'admin.books.index',
        'Items'      => 'admin.items.index',
        'Banknotes'  => 'admin.banknotes.index',
        'Coins'      => 'admin.coins.index',
        'Magazines'  => 'admin.magazines.index',
        'Newspapers' => 'admin.newspapers.index',
        'Postcards'  => 'admin.postcards.index',
        'Stamps'     => 'admin.stamps.index',
    ];
    $createRoutes = [
        'Books'      => 'admin.books.create',
        'Items'      => 'admin.items.create',
        'Banknotes'  => 'admin.banknotes.create',
        'Coins'      => 'admin.coins.create',
        'Magazines'  => 'admin.magazines.create',
        'Newspapers' => 'admin.newspapers.create',
        'Postcards'  => 'admin.postcards.create',
        'Stamps'     => 'admin.stamps.create',
    ];
    $sectionNamesDE = [
        'Books'      => 'Bücher',
        'Items'      => 'Gegenstände',
        'Banknotes'  => 'Banknoten',
        'Coins'      => 'Münzen',
        'Magazines'  => 'Zeitschriften',
        'Newspapers' => 'Zeitungen',
        'Postcards'  => 'Postkarten',
        'Stamps'     => 'Briefmarken',
    ];
@endphp

<div class="space-y-6">

    {{-- ── HEADER ─────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl bg-black/20 px-6 py-7 ring-1 ring-black/30 noise-texture">
        {{-- Balkenkreuz watermark --}}
        <div class="pointer-events-none select-none absolute inset-0 flex items-center justify-center overflow-hidden">
            <svg class="h-52 w-52 text-white/[0.025]" viewBox="0 0 40 40" fill="currentColor">
                <rect x="16" y="0" width="8" height="40"/>
                <rect x="0" y="16" width="40" height="8"/>
            </svg>
        </div>
        <p class="font-stencil text-xs tracking-[0.4em] text-khaki/60 uppercase mb-1">
            Tätigkeitsbericht &mdash; {{ $startOfWeek->format('d.m.Y') }}
        </p>
        <h1 class="font-stencil text-3xl font-black tracking-[0.2em] text-white uppercase">BEFEHLSZENTRALE</h1>
        <p class="font-mono text-[10px] tracking-[0.25em] text-white/35 mt-1 uppercase">
            Verwaltung &middot; WK II Sammlung &middot; Kollektionsübersicht
        </p>
    </div>

    {{-- ── SUMMARY STATS ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl bg-black/25 ring-1 ring-black/20 px-5 py-4 noise-texture">
            <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/60">Gesamtbestand</p>
            <p class="mt-2 font-stencil text-5xl font-black text-white">{{ number_format($totalItems) }}</p>
            <p class="font-mono text-[9px] tracking-[0.15em] text-white/30 mt-1.5 uppercase">Erfasste Objekte</p>
        </div>
        <div class="rounded-xl bg-black/25 ring-1 ring-black/20 px-5 py-4 noise-texture">
            <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/60">Zu Verkaufen</p>
            <p class="mt-2 font-stencil text-5xl font-black text-white">{{ number_format($totalForSale) }}</p>
            <p class="font-mono text-[9px] tracking-[0.15em] text-white/30 mt-1.5 uppercase">Verfügbare Stücke</p>
        </div>
        <div class="rounded-xl bg-black/25 ring-1 ring-black/20 px-5 py-4 noise-texture">
            <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/60">Diese Woche</p>
            <p class="mt-2 font-stencil text-5xl font-black text-white">{{ number_format($totalCreatedThisWeek) }}</p>
            <p class="font-mono text-[9px] tracking-[0.15em] text-white/30 mt-1.5 uppercase">Neu erfasst &middot; ab {{ $startOfWeek->format('d.m') }}</p>
        </div>
    </div>

    {{-- ── VALUE STATS ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl bg-black/25 ring-1 ring-black/20 px-5 py-4 noise-texture">
            <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/60">Investiert</p>
            <p class="mt-2 font-stencil text-2xl font-black text-white">€ {{ number_format($totalInvested, 0, ',', '.') }}</p>
            <p class="font-mono text-[9px] tracking-[0.15em] text-white/30 mt-1.5 uppercase">Total purchase cost</p>
        </div>
        <div class="rounded-xl bg-black/25 ring-1 ring-black/20 px-5 py-4 noise-texture">
            <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/60">Angebotswert</p>
            <p class="mt-2 font-stencil text-2xl font-black text-white">€ {{ number_format($totalForSaleValue, 0, ',', '.') }}</p>
            <p class="font-mono text-[9px] tracking-[0.15em] text-white/30 mt-1.5 uppercase">Active for-sale value</p>
        </div>
        <div class="rounded-xl bg-black/25 ring-1 ring-black/20 px-5 py-4 noise-texture">
            <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/60">Realisiert</p>
            <p class="mt-2 font-stencil text-2xl font-black text-white">€ {{ number_format($totalSoldValue, 0, ',', '.') }}</p>
            <p class="font-mono text-[9px] tracking-[0.15em] text-white/30 mt-1.5 uppercase">Revenue from sales</p>
        </div>
        <div class="rounded-xl bg-black/25 ring-1 ring-black/20 px-5 py-4 noise-texture">
            <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/60">Verkauft</p>
            <p class="mt-2 font-stencil text-5xl font-black text-white">{{ number_format($totalSoldCount) }}</p>
            <p class="font-mono text-[9px] tracking-[0.15em] text-white/30 mt-1.5 uppercase">Items sold</p>
        </div>
    </div>

    {{-- ── QUICK ACTIONS ───────────────────────────────────────── --}}
    <div class="rounded-xl ring-1 ring-black/25 bg-black/15 p-4">
        <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/55 mb-3">Schnellzugriff</p>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
            <a href="{{ route('admin.books.create') }}"
                class="group rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10 hover:border-khaki/30">
                <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-khaki/50 group-hover:text-khaki/70">Erfassen</p>
                <p class="mt-1 text-sm font-semibold text-white">Buch</p>
            </a>
            <a href="{{ route('admin.items.create') }}"
                class="group rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10 hover:border-khaki/30">
                <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-khaki/50 group-hover:text-khaki/70">Erfassen</p>
                <p class="mt-1 text-sm font-semibold text-white">Gegenstand</p>
            </a>
            <a href="{{ route('admin.lookups.index', ['type' => 'origins']) }}"
                class="group rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10 hover:border-khaki/30">
                <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-khaki/50 group-hover:text-khaki/70">Verwalten</p>
                <p class="mt-1 text-sm font-semibold text-white">Ursprünge</p>
            </a>
            <a href="{{ route('admin.blog.index') }}"
                class="group rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10 hover:border-khaki/30">
                <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-khaki/50 group-hover:text-khaki/70">Verwalten</p>
                <p class="mt-1 text-sm font-semibold text-white">Blog</p>
            </a>
        </div>
    </div>

    {{-- ── SECTION INVENTORY TABLE ─────────────────────────────── --}}
    <div class="rounded-xl ring-1 ring-black/30 bg-black/15 overflow-hidden">
        <div class="px-5 py-3 border-b border-black/30 flex items-center justify-between">
            <p class="font-stencil text-[10px] uppercase tracking-[0.25em] text-khaki/60">Kollektionsübersicht</p>
            <p class="font-mono text-[9px] text-white/25 uppercase tracking-[0.1em]">{{ count($sections) }} Abteilungen</p>
        </div>

        {{-- Column headers --}}
        <div class="grid grid-cols-[1fr_60px_60px_60px_88px] gap-3 px-5 py-2 border-b border-black/20">
            <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-white/30">Abteilung</p>
            <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-white/30 text-right">Gesamt</p>
            <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-white/30 text-right hidden sm:block">Verkauf</p>
            <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-white/30 text-right hidden md:block">Woche</p>
            <p class="font-stencil text-[9px] uppercase tracking-[0.2em] text-white/30 text-right"></p>
        </div>

        {{-- Rows --}}
        <div class="divide-y divide-black/15">
            @foreach($sections as $section)
            <div class="grid grid-cols-[1fr_60px_60px_60px_88px] gap-3 items-center px-5 py-3 hover:bg-white/[0.03] transition">
                <div>
                    <p class="text-sm font-semibold text-white">{{ $sectionNamesDE[$section['name']] ?? $section['name'] }}</p>
                    <p class="font-mono text-[9px] text-white/25 uppercase tracking-[0.08em]">{{ $section['name'] }}</p>
                </div>
                <p class="font-stencil text-base font-black text-white text-right">{{ $section['total'] }}</p>
                <p class="font-mono text-sm text-white/55 text-right hidden sm:block">{{ $section['for_sale'] }}</p>
                <p class="text-right hidden md:block">
                    @if($section['created_this_week'] > 0)
                        <span class="font-mono text-sm text-khaki">+{{ $section['created_this_week'] }}</span>
                    @else
                        <span class="font-mono text-sm text-white/25">—</span>
                    @endif
                </p>
                <div class="flex gap-1.5 justify-end">
                    @if(isset($sectionRoutes[$section['name']]))
                    <a href="{{ route($sectionRoutes[$section['name']]) }}"
                        class="rounded-md bg-white/10 px-2 py-1 font-stencil text-[9px] uppercase tracking-[0.1em] text-white/60 transition hover:bg-white/20 hover:text-white">
                        Ansicht
                    </a>
                    @endif
                    @if(isset($createRoutes[$section['name']]))
                    <a href="{{ route($createRoutes[$section['name']]) }}"
                        class="rounded-md bg-khaki/15 px-2 py-1 font-stencil text-[10px] text-khaki/70 transition hover:bg-khaki/25 hover:text-khaki">
                        +
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Footer totals --}}
        <div class="grid grid-cols-[1fr_60px_60px_60px_88px] gap-3 items-center px-5 py-3 bg-black/25 border-t border-black/30">
            <p class="font-stencil text-[10px] uppercase tracking-[0.2em] text-white/40">Gesamtsumme</p>
            <p class="font-stencil text-xl font-black text-khaki text-right">{{ $totalItems }}</p>
            <p class="font-mono text-sm text-white/40 text-right hidden sm:block">{{ $totalForSale }}</p>
            <p class="text-right hidden md:block">
                @if($totalCreatedThisWeek > 0)
                    <span class="font-mono text-sm text-khaki">+{{ $totalCreatedThisWeek }}</span>
                @else
                    <span class="font-mono text-sm text-white/25">—</span>
                @endif
            </p>
            <div></div>
        </div>
    </div>

</div>
@endsection
