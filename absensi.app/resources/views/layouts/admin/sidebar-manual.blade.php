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
        $authUser = Auth::user();
        $isSuperAdminMenu = $authUser->isSuperAdmin();
        $isStaffMenu = $authUser->isStaff();
    @endphp

    <ul class="menu-inner py-1">
        <!-- ================================================================ -->
        <!-- MENU UTAMA                                                       -->
        <!-- ================================================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" data-i18n="Menu Utama">Menu Utama</span>
        </li>

        <!-- Dashboards -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboards">Dashboard</div>
                @if ($isStaffMenu)
                    <div class="badge bg-label-primary rounded-pill ms-auto">Staff</div>
                @elseif ($isSuperAdminMenu)
                    <div class="badge bg-label-danger rounded-pill ms-auto">Superadmin</div>
                @else
                    <div class="badge bg-label-success rounded-pill ms-auto">Admin</div>
                @endif
            </a>
        </li>

        @if (! $isStaffMenu)
            <!-- Laporan Rekap (Khusus Admin & Superadmin) -->
            <li class="menu-item {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
                <a href="{{ route('admin.laporan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-file-analytics"></i>
                    <div data-i18n="Laporan">Laporan Rekap</div>
                </a>
            </li>
        @endif

        <!-- ================================================================ -->
        <!-- PRESENSI & KEHADIRAN                                             -->
        <!-- ================================================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" data-i18n="Presensi Civitas">Presensi & Kehadiran</span>
        </li>

        <!-- Absensi Departemen (Accordion Submenu) -->
        <li class="menu-item {{ request()->routeIs('admin.absensi*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-calendar-check"></i>
                <div data-i18n="Absensi Departemen">Absensi Departemen</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.absensi.index') && !request()->has('departemen') ? 'active' : '' }}">
                    <a href="{{ route('admin.absensi.index') }}" class="menu-link">
                        <div data-i18n="Semua Data">Semua Data</div>
                    </a>
                </li>
                @foreach (\Helper::getDepartemen() as $item)
                    @if (! $isStaffMenu || strtolower($item->nama) !== 'admin')
                        <li class="menu-item {{ request()->routeIs('admin.absensi.index') && request('departemen') == $item->id ? 'active' : '' }}">
                            <a href="{{ route('admin.absensi.index', ['departemen' => $item->id]) }}" class="menu-link">
                                <div data-i18n="{{ $item->nama }}">{{ $item->nama }}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>

        @if (! $isStaffMenu)
            <!-- ================================================================ -->
            <!-- DATA MASTER (Khusus Admin & Superadmin)                          -->
            <!-- ================================================================ -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text" data-i18n="Data Master">Data Master</span>
            </li>

            <!-- Role -->
            <li class="menu-item {{ request()->routeIs('admin.role*') ? 'active' : '' }}">
                <a href="{{ route('admin.role.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-shield-lock"></i>
                    <div data-i18n="Role">Role Akses</div>
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
                    <i class="menu-icon tf-icons ti ti-tags"></i>
                    <div data-i18n="Type User">Type User</div>
                </a>
            </li>
            <!-- Kategori -->
            <li class="menu-item {{ request()->routeIs('admin.kategori*') ? 'active' : '' }}">
                <a href="{{ route('admin.kategori.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-category"></i>
                    <div data-i18n="Kategori">Kategori Durasi</div>
                </a>
            </li>
            <!-- Galeri Foto -->
            <li class="menu-item {{ request()->routeIs('admin.gallery*') ? 'active' : '' }}">
                <a href="{{ route('admin.gallery.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-photo"></i>
                    <div data-i18n="Galeri Foto">Galeri Foto</div>
                </a>
            </li>
        @endif

        <!-- ================================================================ -->
        <!-- KELOLA CIVITAS / MANAJEMEN PENGGUNA                              -->
        <!-- ================================================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" data-i18n="Kelola Civitas">{{ $isStaffMenu ? 'Kelola Civitas' : 'Manajemen Pengguna' }}</span>
        </li>

        <!-- Users -->
        <li class="menu-item {{ request()->routeIs('admin.user*') ? 'active' : '' }}">
            <a href="{{ route('admin.user.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Users">{{ $isStaffMenu ? 'Data Civitas' : 'Data Pengguna' }}</div>
            </a>
        </li>

        @if ($isSuperAdminMenu)
            <!-- ================================================================ -->
            <!-- SISTEM & INTEGRASI (Khusus Superadmin)                           -->
            <!-- ================================================================ -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text" data-i18n="Sistem & Integrasi">Sistem & Integrasi</span>
            </li>

            <!-- Fingerspot Cloud -->
            <li class="menu-item {{ request()->routeIs('admin.fingerspot*') ? 'active' : '' }}">
                <a href="{{ route('admin.fingerspot.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-fingerprint"></i>
                    <div data-i18n="Fingerspot Cloud">Fingerspot Cloud</div>
                </a>
            </li>
            <!-- API Client -->
            <li class="menu-item {{ request()->routeIs('admin.api_client*') ? 'active' : '' }}">
                <a href="{{ route('admin.api_client.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-key"></i>
                    <div data-i18n="API Client">API Client</div>
                </a>
            </li>
        @endif

        <!-- ================================================================ -->
        <!-- PENGATURAN & AKUN                                                -->
        <!-- ================================================================ -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" data-i18n="Pengaturan & Akun">Pengaturan & Akun</span>
        </li>

        <!-- Profile -->
        <li class="menu-item {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
            <a href="{{ route('admin.profile.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user-circle"></i>
                <div data-i18n="Profile">Profil Saya</div>
            </a>
        </li>

        <!-- Logout -->
        <li class="menu-item">
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form-sidebar').submit();" class="menu-link">
                <i class="menu-icon tf-icons ti ti-logout text-danger"></i>
                <div data-i18n="Logout" class="text-danger">Logout</div>
            </a>
            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</aside>
