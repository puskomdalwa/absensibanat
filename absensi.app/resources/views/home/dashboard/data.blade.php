<div class="shop-product-fillter">
    <div class="totall-product">
        <p>
            We found <strong class="text-brand">{{ $isPaginated ? $users->total() : $users->count() }}</strong> items for
            you!
        </p>
    </div>
    <div class="sort-by-product-area">
        <div class="sort-by-cover">
            <select class="sort-by-product-wrap" onchange="loadData(1)" id="data-sort" aria-label="Default select example">
                <option value="name" selected>Urutkan: Nama</option>
                <option value="id">Urutkan: ID</option>
            </select>
        </div>

        <div class="sort-by-cover">
            <select class="sort-by-product-wrap" onchange="loadData(1)" id="data-show" aria-label="Default select example">
                <option value="10" selected>Tampilkan: 10</option>
                <option value="20">Tampilkan 20</option>
                <option value="30">Tampilkan 30</option>
                <option value="*">Tampilkan Semua</option>
            </select>
        </div>
    </div>
</div>

@if ($users->count() <= 0) <div class="alert alert-warning" role="alert">Tidak ada data yang ditemukan.</div>
    @endif

    <div class="row g-4">
        @foreach ($users as $item)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="glass-card h-100 p-3 position-relative d-flex flex-column justify-content-between">
                <div>
                    <!-- Top Badge & Avatar -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge-banat-tag">
                            <i class="fa-solid fa-building-user me-1"></i> {{ $item->departemen->nama }}
                        </span>
                        <span class="small text-muted fw-600">#{{ $item->id }}</span>
                    </div>

                    <div class="text-center my-3">
                        <div class="position-relative d-inline-block">
                            <img src="{{ $item->photo ? asset('photo') . '/'.$item->photo : asset('home/assets/imgs/theme/user.png') }}" 
                                 alt="{{ $item->name }}" 
                                 class="rounded-circle shadow-sm" 
                                 style="width: 85px; height: 85px; object-fit: cover; border: 3px solid var(--banat-primary-light);" />
                        </div>
                        <h5 class="fw-700 mt-3 mb-1" style="color: var(--banat-text-dark); font-size: 1.05rem;">
                            <a href="{{ route('absensi.show', ['user' => $item->id]) }}" class="text-decoration-none text-dark hover-primary">
                                {{ $item->name }}
                            </a>
                        </h5>
                        <p class="small text-muted mb-0">Civitas Banat UII Dalwa</p>
                    </div>
                </div>

                <div class="pt-3 border-top text-center" style="border-color: var(--banat-border) !important;">
                    <a href="{{ route('absensi.show', ['user' => $item->id]) }}" class="btn-banat-outline btn-sm w-100 text-decoration-none">
                        <i class="fa-solid fa-address-card me-1"></i> Lihat Absensi
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @if ($isPaginated)
    <!--pagination-->
    <div class="pagination-area mt-15 mb-sm-5 mb-lg-0">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-start">
                {{-- Tombol Previous --}}
                @if ($users->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link"><i class="fi-rs-angle-double-small-left"></i></span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link" href="{{ $users->previousPageUrl() }}">
                        <i class="fi-rs-angle-double-small-left"></i>
                    </a>
                </li>
                @endif

                @php
                $start = max($users->currentPage() - 2, 1);
                $end = min($users->currentPage() + 2, $users->lastPage());
                @endphp

                {{-- Tampilkan halaman pertama jika tidak dalam rentang --}}
                @if ($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $users->url(1) }}">01</a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                @endif

                {{-- Loop untuk halaman dalam rentang --}}
                @for ($page = $start; $page <= $end; $page++) <li class="page-item {{ $users->currentPage() == $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ $users->url($page) }}">{{ str_pad($page, 2, '0', STR_PAD_LEFT) }}</a>
                    </li>
                    @endfor

                    {{-- Tampilkan halaman terakhir jika tidak dalam rentang --}}
                    @if ($end < $users->lastPage())
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="{{ $users->url($users->lastPage()) }}">{{ str_pad($users->lastPage(), 2, '0', STR_PAD_LEFT) }}</a>
                        </li>
                        @endif

                        {{-- Tombol Next --}}
                        @if ($users->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $users->nextPageUrl() }}">
                                <i class="fi-rs-angle-double-small-right"></i>
                            </a>
                        </li>
                        @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fi-rs-angle-double-small-right"></i></span>
                        </li>
                        @endif
            </ul>
        </nav>
    </div>
    @endif
