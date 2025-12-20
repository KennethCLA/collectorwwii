{{-- resources/views/layouts/admin.blade.php --}}
@extends('layouts.app', ['useAdminHeader' => true, 'bodyClass' => 'bg-[#565e55]'])

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex gap-6">
        <aside class="w-64 shrink-0">
            <div class="rounded-xl bg-black/15 ring-1 ring-black/30 p-3 text-white">
                @include('admin.partials.sidebar')
            </div>
        </aside>

        <main class="flex-1">
            <div class="rounded-xl bg-black/15 ring-1 ring-black/30 p-4 text-white">
                @yield('admin-content')
            </div>
        </main>
    </div>
</div>
@endsection