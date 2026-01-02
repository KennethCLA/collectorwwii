<nav class="space-y-2">
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.dashboard') }}">Dashboard</a>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Beheer</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/books') }}">Boeken</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.books.create') }}">
        New book
    </a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/items') }}">Items</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/banknotes') }}">Banknotes</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/coins') }}">Coins</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/stamps') }}">Stamps</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/postcards') }}">Postcards</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/magazines') }}">Magazines</a>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/newspapers') }}">Newspapers</a>

    <div class="pt-2 text-xs font-semibold text-gray-500 uppercase">Account</div>
    <a class="block px-3 py-2 rounded hover:bg-gray-100" href="{{ url('/admin/profile') }}">Profiel</a>
</nav>