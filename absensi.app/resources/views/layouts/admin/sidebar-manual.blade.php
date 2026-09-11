<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('root.index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('admin_assets/img/favicon-admin.png') }}"
                    style="width: 100%;height: 100%;object-fit: contain;" alt="logo">
            </span>
            <span class="app-brand-text demo menu-text fw-bold">{{ config('app.name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $isStaffMenu = Auth::user()->isStaff();
    @endphp

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboards">Dashboards</div>
            </a>
        </li>

        @if ($isStaffMenu)
            <li class="menu-item {{ request()->routeIs('admin.absensi*') ? 'active' : '' }}">
                <a href="{{ route('admin.absensi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-checklist"></i>
                    <div data-i18n="Absensi">Absensi</div>
                </a>
            </li>
        @endif

        @if (! $isStaffMenu)
            <li class="menu-item {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
                <a href="{{ route('admin.laporan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-report"></i>
                    <div data-i18n="Laporan">Laporan</div>
                </a>
            </li>

            <!-- DATA-->
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="News">DATA</span>
            </li>

            <!-- Role -->
            <li class="menu-item {{ request()->routeIs('admin.role*') ? 'active' : '' }}">
                <a href="{{ route('admin.role.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-key"></i>
                    <div data-i18n="Role">Role</div>
                </a>
            </li>
            <!-- Departemen -->
            <li class="menu-item {{ request()->routeIs('admin.departemen*') ? 'active' : '' }}">
                <a href="{{ route('admin.departemen.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-building"></i>
                    <div data-i18n="Departemen">Departemen</div>
                </a>
            </li>
            <!-- Type User -->
            <li class="menu-item {{ request()->routeIs('admin.type*') ? 'active' : '' }}">
                <a href="{{ route('admin.type.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-tag"></i>
                    <div data-i18n="Type User">Type User</div>
                </a>
            </li>

            <!-- ABSENSI-->
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Absensi">Absensi</span>
            </li>

            <!-- Semua -->
            <li
                class="menu-item {{ request()->routeIs('admin.absensi.index') && !request()->has('departemen') ? 'active' : '' }}">
                <a href="{{ route('admin.absensi.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-checklist"></i>
                    <div data-i18n="Semua">Semua</div>
                </a>
            </li>
            @php
                $tags = ['building', 'user-cog', 'compass', 'tags', 'folder'];

            @endphp
            @foreach (\Helper::getDepartemen() as $item)
                <!-- {{ $item->nama }} -->
                <li
                    class="menu-item {{ request()->routeIs('admin.absensi.index') && request('departemen') == $item->id ? 'active' : '' }}">
                    <a href="{{ route('admin.absensi.index', ['departemen' => $item->id]) }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-{{ $tags[rand(0, 4)] }}"></i>
                        <div data-i18n="{{ $item->nama }}">{{ $item->nama }}</div>
                    </a>
                </li>
            @endforeach

            <!-- ADMINISTRATOR-->
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Administrator">ADMINISTRATOR</span>
            </li>
        @endif

        <!-- Users -->
        <li class="menu-item {{ request()->routeIs('admin.user*') ? 'active' : '' }}">
            <a href="{{ route('admin.user.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div data-i18n="Users">Users</div>
            </a>
        </li>
        @if (! $isStaffMenu)
            <!-- Kategori -->
            <li class="menu-item {{ request()->routeIs('admin.kategori*') ? 'active' : '' }}">
                <a href="{{ route('admin.kategori.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-category"></i>
                    <div data-i18n="Kategori">Kategori</div>
                </a>
            </li>
            {{-- <!-- API Client -->
            <li class="menu-item {{ request()->routeIs('admin.api_client*') ? 'active' : '' }}">
                <a href="{{ route('admin.api_client.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-key"></i>
                    <div data-i18n="API Client">API Client</div>
                </a>
            </li> --}}
        @endif
        <!-- Profile -->
        <li class="menu-item {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
            <a href="{{ route('admin.profile.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div data-i18n="Profile">Profile</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form-sidebar').submit();" class="menu-link">
                <i class="menu-icon tf-icons ti ti-logout"></i>
                <div data-i18n="Logout">Logout</div>
            </a>
            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</aside>
