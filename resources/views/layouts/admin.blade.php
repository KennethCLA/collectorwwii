{{-- resources/views/layouts/admin.blade.php --}}
@extends('layouts.app', [
'useAdminHeader' => true,
'bodyClass' => 'bg-[#565e55]',
'mainClass' => 'w-full' // <- belangrijk: geen centering classes
    ])

    @section('content')
    <div class="mx-auto w-full max-w-none px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <aside class="w-full lg:w-56 lg:shrink-0 order-2 lg:order-1">
            <div class="rounded-xl bg-black/15 ring-1 ring-black/30 p-3 text-white">
                @include('admin.partials.sidebar')
            </div>
        </aside>

        <main class="flex-1 min-w-0 order-1 lg:order-2">
            <div class="rounded-xl bg-black/15 ring-1 ring-black/30 p-4 text-white">
                @yield('admin-content')
            </div>
        </main>
    </div>
    </div>
    @endsection