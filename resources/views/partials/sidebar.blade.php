<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text fw-light">{{ config('app.name') }}</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                @foreach ($menuArbol as $item)
                    @include('partials.menu-item', ['item' => $item])
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
