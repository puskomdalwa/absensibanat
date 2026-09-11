<div class="row g-4">
    @foreach ($data as $item)
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
