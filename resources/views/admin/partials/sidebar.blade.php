<nav class="space-y-2">
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.dashboard') }}">Dashboard</a>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Books</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.books.index') }}">All books</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.books.create') }}">New book</a>
    <div class="h-px bg-white/15"></div>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Items</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.items.index') }}">All items</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.items.create') }}">New item</a>
    <div class="h-px bg-white/15"></div>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Banknotes</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.banknotes.index') }}">All banknotes</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.banknotes.create') }}">New banknote</a>
    <div class="h-px bg-white/15"></div>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Coins</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.coins.index') }}">All coins</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.coins.create') }}">New coin</a>
    <div class="h-px bg-white/15"></div>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Stamps</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.stamps.index') }}">All stamps</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.stamps.create') }}">New stamp</a>
    <div class="h-px bg-white/15"></div>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Postcards</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.postcards.index') }}">All postcards</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.postcards.create') }}">New postcard</a>
    <div class="h-px bg-white/15"></div>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Magazines</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.magazines.index') }}">All magazines</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.magazines.create') }}">New magazine</a>
    <div class="h-px bg-white/15"></div>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Newspapers</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.newspapers.index') }}">All newspapers</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.newspapers.create') }}">New newspaper</a>
    <div class="h-px bg-white/15"></div>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Account</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/profile') }}">Profiel</a>
</nav>
