@extends('layouts.admin')

@section('admin-content')
<form action="{{ route('admin.blog.update', $post['id']) }}" method="POST" class="w-full">
    @csrf
    @method('PUT')

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Edit blog post</h1>
            <p class="mt-1 text-sm text-white/60">Update post text in all languages as needed.</p>
        </div>
        <a href="{{ route('admin.blog.index') }}"
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
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-xl border border-black/20 bg-black/10 p-6 space-y-6">
        <div class="space-y-2">
            <label class="text-sm font-medium text-white/80">Date *</label>
            <input type="date" name="date" value="{{ old('date', data_get($post, 'date')) }}" required
                class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-white/20">
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-medium text-white/80">English content *</label>
                <textarea name="content_en" rows="8" required
                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('content_en', data_get($post, 'content.en')) }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-white/80">Dutch content</label>
                <textarea name="content_nl" rows="8"
                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('content_nl', data_get($post, 'content.nl')) }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-white/80">German content</label>
                <textarea name="content_de" rows="8"
                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('content_de', data_get($post, 'content.de')) }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-white/80">French content</label>
                <textarea name="content_fr" rows="8"
                    class="w-full rounded-md border border-black/30 bg-white/10 px-3 py-2 text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('content_fr', data_get($post, 'content.fr')) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.blog.index') }}"
                class="rounded-md bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Cancel</a>
            <button type="submit"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">Save changes</button>
        </div>
    </div>
</form>
@endsection
