<div class="banat-dashboard-card mb-4 p-3 px-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background: rgba(224, 82, 117, 0.12); color: var(--banat-primary); border: 1px solid var(--banat-border); border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 0.85rem;">
                <i class="fa-solid fa-users me-1"></i> Ditemukan <strong>{{ $isPaginated ? $users->total() : $users->count() }}</strong> Civitas
            </span>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <select class="sort-by-product-wrap" onchange="loadData(1)" id="data-sort" aria-label="Urutkan">
                <option value="name" selected>Urutkan: Nama (A-Z)</option>
                <option value="id">Urutkan: ID Civitas</option>
            </select>

            <select class="sort-by-product-wrap" onchange="loadData(1)" id="data-show" aria-label="Tampilkan">
                <option value="12" selected>Tampilkan: 12</option>
                <option value="24">Tampilkan: 24</option>
                <option value="48">Tampilkan: 48</option>
                <option value="*">Tampilkan Semua</option>
            </select>
        </div>
    </div>
</div>

@if ($users->count() <= 0)
    <div class="alert banat-alert-info p-4 text-center" role="alert">
        <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
        <span>Tidak ada civitas yang sesuai dengan kriteria pencarian Anda.</span>
    </div>
@endif

<div class="row g-4 mb-4">
    @foreach ($users as $item)
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="banat-dashboard-card h-100 p-3 position-relative d-flex flex-column justify-content-between">
            <div>
                <!-- Top Badge & ID -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge-dept" style="font-size: 0.74rem;">
                        <i class="fa-solid fa-building-user me-1"></i> {{ $item->departemen->nama }}
                    </span>
                    <span class="small fw-700" style="color: var(--banat-primary);">#{{ $item->id }}</span>
                </div>

                <div class="text-center my-3">
                    <div class="dashboard-avatar-wrapper mb-2">
                        <div class="avatar-ring-wrapper" style="padding: 3px;">
                            <img src="{{ $item->photo ? asset('photo') . '/'.$item->photo : asset('home/assets/imgs/theme/user.png') }}" 
                                 alt="{{ $item->name }}" 
                                 class="rounded-circle shadow-sm" 
                                 style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #ffffff; display: block;" />
                        </div>
                    </div>
                    <h5 class="fw-700 mt-2 mb-1" style="font-size: 1.02rem;">
                        <a href="{{ route('absensi.show', ['user' => $item->id]) }}" class="text-decoration-none hover-primary" style="color: var(--banat-text-dark);">
                            {{ $item->name }}
                        </a>
                    </h5>
                    <p class="small text-muted mb-0">Civitas Banat UII Dalwa</p>
                </div>
            </div>

            <div class="pt-3 border-top text-center" style="border-color: var(--banat-border) !important;">
                <a href="{{ route('absensi.show', ['user' => $item->id]) }}" class="btn-banat-outline btn-sm w-100 text-decoration-none py-2">
                    <i class="fa-solid fa-calendar-check me-1"></i> Lihat Absensi
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
