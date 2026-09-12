 <!-- Header -->
 <header class="header-area header-style-1 header-height-2">
     <div class="header-top header-top-ptb-1 d-none d-lg-block">
         <div class="container">
             <div class="row align-items-center">
                 <div class="col-xl-3 col-lg-4">
                     <div class="header-info">
                         <ul>
                             <li>
                                 <i class="fi-rs-building"></i>
                                 <a href="#">UII Dalwa</a>
                             </li>
                             <li>
                                 <i class="fi-rs-marker"></i><a href="page-contact.html">Bangil, Pasuruan</a>
                             </li>
                         </ul>
                     </div>
                 </div>
                 <div class="col-xl-6 col-lg-4">
                     <div class="text-center">
                         <div id="news-flash" class="d-inline-block">
                             <ul>
                                 <li>
                                     <a href="https://play.google.com/store/apps/details?id=com.uiidalwa.absensi">
                                         Download Aplikasi Absensi UII Dalwa >> Klik disini</a>
                                 </li>
                                 <li>
                                     Website Absensi UII Dalwa
                                     <a href="{{ route('root.index') }}">View details</a>
                                 </li>
                             </ul>
                         </div>
                     </div>
                 </div>
                 <div class="col-xl-3 col-lg-4">
                     <div class="header-info header-info-right">
                         <ul>
                             @if (\Auth::check())
                                 <li>
                                     <i class="fi-rs-sign-in"></i><a href="{{ route('login') }}">Dashboard</a>
                                 </li>
                                 <i class="fi-rs-sign-in"></i>
                                 <a href="{{ route('logout') }}"
                                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                     Log out
                                 </a>
                                 <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                     style="display: none;">
                                     @csrf
                                 </form>
                                 </li>
                             @else
                                 <li>
                                     <i class="fi-rs-sign-in"></i><a href="{{ route('login') }}">Log In</a>
                                 </li>
                             @endif
                         </ul>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
         <div class="container">
             <div class="header-wrap">
                 <div class="logo logo-width-1">
                     <a href="{{ route('root.index') }}"><img src="{{ asset('home/assets/imgs/theme/logo.png') }}"
                             alt="logo" /></a>
                 </div>
                 <div class="header-right">
                     <div class="search-style-2">
                         <form action="{{ route('absensi.index') }}">
                             <select class="select-active" name="departemen_id" id="desktop_departemen_id">
                                 <option value="*">Filter</option>
                                 @foreach ($departemen as $item)
                                     <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                 @endforeach
                                 <option value="*">Semua</option>
                             </select>
                             <input type="text" name="search" id="desktop_search"
                                 placeholder="Silahkan ketik nama..." />
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="header-bottom header-bottom-bg-color sticky-bar header-glass">
         <div class="container">
             <div class="header-wrap header-space-between position-relative">
                 <div class="logo logo-width-1 d-block d-lg-none">
                     <div class="header-action-icon-2 d-block d-lg-none">
                         <div class="burger-icon burger-icon-white">
                             <span class="burger-icon-top"></span>
                             <span class="burger-icon-mid"></span>
                             <span class="burger-icon-bottom"></span>
                         </div>
                     </div>
                 </div>
                 <div class="header-nav d-none d-lg-flex">
                     <div class="main-categori-wrap d-none d-lg-block">
                         <a class="categori-button-active" href="#">
                             <span class="fi-rs-apps"></span> Departemen
                         </a>
                         <div class="categori-dropdown-wrap categori-dropdown-active-large">
                             <ul>
                                 @foreach ($departemen as $item)
                                     <li>
                                         <a href="{{ route('absensi.index', ['departemen_id' => $item->id]) }}"><i
                                                 class="fi-rs-user"></i>{{ $item->nama }}</a>
                                     </li>
                                 @endforeach
                             </ul>
                         </div>
                     </div>
                     <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block">
                         <nav>
                             <ul>
                                 <li>
                                     <a class="{{ request()->routeIs('root.index*') ? 'active' : '' }}"
                                         href="{{ route('root.index') }}">Home</a>
                                 </li>
                                 <li>
                                     <a class="{{ request()->routeIs('absensi.index*') ? 'active' : '' }}"
                                         href="{{ route('absensi.index') }}">Absensi</a>
                                 </li>
                                 <li>
                                     <a class="{{ request()->routeIs('dashboard.index*') ? 'active' : '' }}"
                                         href="{{ route('dashboard.index') }}">Dashboard</a>
                                 </li>
                                 <li>
                                     <a class="{{ request()->routeIs('laporan*') ? 'active' : '' }}"
                                         href="{{ route('laporan.index') }}">Laporan</a>
                                 </li>
                                 <li>
                                     <a class="{{ request()->routeIs('realtime.index*') ? 'active' : '' }}"
                                         href="{{ route('realtime.index') }}">Absensi Realtime</a>
                                 </li>
                                 <li>
                                     <a href="https://play.google.com/store/apps/details?id=com.uiidalwa.absensi">Download
                                         Aplikasi</a>
                                 </li>
                             </ul>
                         </nav>
                     </div>
                 </div>
                 <div class="hotline d-none d-lg-block">
                     <p>
                         <i class="fi-rs-laptop"></i><span>Absensi</span>
                     </p>
                 </div>
                 <div class="nav-search d-block d-md-none">
                     <div class="search-style-3">
                         <form action="{{ route('absensi.index') }}" id="form-mobile-filter">
                             <input type="text" class="input-search" name="search" id="mobile_search"
                                 placeholder="Search for items…" />
                             <input type="hidden" name="departemen_id" id="mobile_departemen_id">
                             <div class="search-icon">
                                 <button type="submit" class="item">
                                     <i class="fi-rs-search"></i>
                                 </button>
                                 <button type="button" class="item btn-filter" id="btn-filter">
                                     <i class="fi-rs-filter"></i>
                                 </button>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </header>
 <div class="mobile-header-active mobile-header-wrapper-style" style="left: 0">
     <div class="mobile-header-wrapper-inner">
         <div class="mobile-header-top">
             <div class="mobile-header-logo">
                 <a href="{{ route('root.index') }}"><img src="{{ asset('home/assets/imgs/theme/logo.png') }}"
                         alt="logo" /></a>
             </div>
             <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                 <button class="close-style search-close">
                     <i class="icon-top"></i>
                     <i class="icon-bottom"></i>
                 </button>
             </div>
         </div>
         <div class="mobile-header-content-area">
             <div class="mobile-search search-style-3 mobile-header-border">
                 <form action="#">
                     <input type="text" placeholder="Search for items…" />
                     <button type="submit">
                         <i class="fi-rs-search"></i>
                     </button>
                 </form>
             </div>
             <div class="mobile-menu-wrap mobile-header-border">
                 <div class="main-categori-wrap mobile-header-border mt-3 mb-3">
                     <a class="categori-button-active-2" href="#">
                         <span class="fi-rs-apps"></span> Departemen
                     </a>
                     <div class="categori-dropdown-wrap categori-dropdown-active-small">
                         <ul>
                             @foreach ($departemen as $item)
                                 <li>
                                     <a
                                         href="{{ route('absensi.index', ['departemen_id' => $item->id]) }}">{{ $item->nama }}</a>
                                 </li>
                             @endforeach
                         </ul>
                     </div>
                 </div>
                 <!-- mobile menu start -->
                 <nav>
                     <ul class="mobile-menu">
                         <li class="menu-item-has-children">
                             <span class="menu-expand"></span><a href="{{ route('root.index') }}">Home</a>
                         </li>
                         <li class="menu-item-has-children">
                             <span class="menu-expand"></span><a href="{{ route('absensi.index') }}">Absensi</a>
                         </li>
                         <li class="menu-item-has-children">
                             <span class="menu-expand"></span><a href="{{ route('laporan.index') }}">Laporan</a>
                         </li>
                         <li class="menu-item-has-children">
                             <span class="menu-expand"></span><a href="{{ route('realtime.index') }}">Absensi Realtime</a>
                         </li>
                         <li class="menu-item-has-children">
                             <span class="menu-expand"></span><a
                                 href="https://play.google.com/store/apps/details?id=com.uiidalwa.absensi">Download
                                 Aplikasi</a>
                         </li>
                         @if (\Auth::check())
                             <li class="menu-item-has-children">
                                 <span class="menu-expand"></span><a href="{{ route('dashboard.index') }}">Dashboard</a>
                             </li>
                             <li class="menu-item-has-children">
                                 <span class="menu-expand"></span><a href="{{ route('logout') }}"
                                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                 <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                     @csrf
                                 </form>
                             </li>
                         @else
                             <li class="menu-item-has-children">
                                 <span class="menu-expand"></span><a href="{{ route('login') }}">Login</a>
                             </li>
                         @endif
                     </ul>
                 </nav>
                 <!-- mobile menu end -->
             </div>
         </div>
     </div>
 </div>

 <div class="mobile-filter-active mobile-header-wrapper-style">
     <div class="mobile-header-wrapper-inner">
         <div class="mobile-header-top">
             <div class="mobile-header-logo">
                 <a href="{{ route('root.index') }}"><img src="{{ asset('home/assets/imgs/theme/logo.png') }}"
                         alt="logo" /></a>
             </div>
             <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                 <button class="close-style search-close" type="button">
                     <i class="icon-top"></i>
                     <i class="icon-bottom"></i>
                 </button>
             </div>
         </div>
         <div class="mobile-header-content-area">
             <div class="mobile-filter-header-badge mb-4">
                 <i class="fa-solid fa-filter me-2"></i> Pilih Departemen
             </div>

             <div class="banat-filter-group mb-4">
                 @foreach ($departemen as $item)
                     <label class="banat-filter-option" for="dep-{{ $item->id }}">
                         <input name="mobile_departemen_id" id="dep-{{ $item->id }}" type="radio"
                             value="{{ $item->id }}" />
                         <span class="custom-radio-indicator"></span>
                         <span class="filter-option-label">{{ $item->nama }}</span>
                     </label>
                 @endforeach
             </div>

             <div class="d-flex flex-column gap-2 mt-4">
                 <button class="btn-banat-primary w-100 py-3" id="btn-submit-filter" type="button">
                     <i class="fa-solid fa-check me-2"></i> Terapkan Filter
                 </button>
                 <button class="btn-banat-outline w-100 py-2" id="btn-delete-filter" type="button">
                     <i class="fa-solid fa-rotate-left me-2"></i> Hapus Filter
                 </button>
             </div>
         </div>
     </div>
 </div>
 <!-- End Header -->
