@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <div class="flex gap-6">
            <aside class="w-64 shrink-0">
                <div class="rounded border bg-white p-3">
                    @include('admin.partials.sidebar')
                </div>
            </aside>

            <main class="flex-1">
                <div class="rounded border bg-white p-4">
                    @yield('admin-content')
                </div>
            </main>
        </div>
    </div>
</div>
@endsection