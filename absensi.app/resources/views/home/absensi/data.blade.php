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

    <div class="row product-grid-3">
        @foreach ($users as $item)
        <div class="col-6 col-lg-3 col-md-4">
            <div class="product-cart-wrap mb-30">
                <div class="product-img-action-wrap">
                    <div class="product-img product-img-zoom">
                        <a href="{{ route('absensi.show', ['user' => $item->id]) }}">
                            <img class="default-img" src="{{ $item->photo ? asset('photo') . '/'.$item->photo : asset('home/assets/imgs/theme/user.png') }}" alt="" />
                            <img class="hover-img" src="{{ asset('home/assets/imgs/theme/user-hover.jpg') }}" alt="" />
                        </a>
                    </div>
                    <div class="product-action-1">
                        <a aria-label="Detail" class="action-btn hover-up" href="{{ route('absensi.show', ['user' => $item->id]) }}">
                            <i class="fi-rs-search"></i></a>
                    </div>
                    <div class="product-badges product-badges-position product-badges-mrg">
                        <span class="bg-primary">{{ $item->departemen->nama }}</span>
                    </div>
                </div>
                <div class="product-content-wrap">
                    <div class="product-category">
                        <a href="{{ route('absensi.show', ['user' => $item->id]) }}">{{ $item->departemen->nama }}
                            ({{ $item->id }})
                        </a>
                    </div>
                    <h2>
                        <a href="{{ route('absensi.show', ['user' => $item->id]) }}">{{ $item->name }}</a>
                    </h2>
                    <div class="product-action-1 show">
                        <a aria-label="Detail" class="action-btn hover-up" href="{{ route('absensi.show', ['user' => $item->id]) }}"><i class="fi-rs-search"></i></a>
                    </div>
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
