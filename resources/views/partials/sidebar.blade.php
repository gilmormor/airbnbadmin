<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text fw-light">{{ config('app.name') }}</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @can('reservas.ver')
                    @if (Route::has('reservas.index'))
                        <li class="nav-item">
                            <a href="{{ route('reservas.index') }}" class="nav-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-calendar-check"></i>
                                <p>Reservas</p>
                            </a>
                        </li>
                    @endif
                @endcan

                @can('reportes.ver')
                    @if (Route::has('reportes.index'))
                        <li class="nav-item">
                            <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-bar-chart-line"></i>
                                <p>Reportes</p>
                            </a>
                        </li>
                    @endif
                @endcan

                @role('Administrador')
                    <li class="nav-item mt-2">
                        <span class="nav-link text-uppercase small text-body-secondary px-3">Administración</span>
                    </li>

                    @if (Route::has('edificios.index'))
                        <li class="nav-item">
                            <a href="{{ route('edificios.index') }}" class="nav-link {{ request()->routeIs('edificios.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-building"></i>
                                <p>Edificios</p>
                            </a>
                        </li>
                    @endif

                    @if (Route::has('propietarios.index'))
                        <li class="nav-item">
                            <a href="{{ route('propietarios.index') }}" class="nav-link {{ request()->routeIs('propietarios.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-person-badge"></i>
                                <p>Propietarios</p>
                            </a>
                        </li>
                    @endif

                    @if (Route::has('departamentos.index'))
                        <li class="nav-item">
                            <a href="{{ route('departamentos.index') }}" class="nav-link {{ request()->routeIs('departamentos.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-door-closed"></i>
                                <p>Departamentos</p>
                            </a>
                        </li>
                    @endif

                    @if (Route::has('importaciones.index'))
                        <li class="nav-item">
                            <a href="{{ route('importaciones.index') }}" class="nav-link {{ request()->routeIs('importaciones.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-cloud-upload"></i>
                                <p>Importar reservas</p>
                            </a>
                        </li>
                    @endif

                    @if (Route::has('beds24.propiedades'))
                        <li class="nav-item">
                            <a href="{{ route('beds24.propiedades') }}" class="nav-link {{ request()->routeIs('beds24.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-diagram-3"></i>
                                <p>Propiedades Beds24</p>
                            </a>
                        </li>
                    @endif

                    @if (Route::has('usuarios.index'))
                        <li class="nav-item">
                            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-people"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>
                    @endif
                @endrole
            </ul>
        </nav>
    </div>
</aside>
