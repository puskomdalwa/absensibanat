<div class="row product-grid-4">
    @foreach ($data as $item)
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