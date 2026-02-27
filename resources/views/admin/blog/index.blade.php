@extends('layouts.admin')

@section('admin-content')
<div class="w-full">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-white">Blog posts</h1>
        <a href="{{ route('admin.blog.create') }}"
            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
            New post
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-black/20 bg-black/10">
        <table class="w-full text-sm text-white">
            <thead class="border-b border-white/10 text-white/60 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">English preview</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($posts as $post)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-4 py-3 font-medium">{{ \Carbon\Carbon::parse($post['date'])->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-white/70">
                        {{ \Illuminate\Support\Str::limit(str_replace("\n", ' ', (string) data_get($post, 'content.en', '')), 140) }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('admin.blog.edit', $post['id']) }}"
                                class="rounded-md bg-white/10 px-3 py-1 text-xs hover:bg-white/20">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.blog.destroy', $post['id']) }}"
                                onsubmit="return confirm('Delete this blog post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="rounded-md bg-red-500/20 px-3 py-1 text-xs text-red-200 hover:bg-red-500/30">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-white/40">No blog posts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
