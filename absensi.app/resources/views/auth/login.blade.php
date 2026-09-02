@extends('layouts.home.template')
@section('title', 'Login | Absensi UII Dalwa')
@section('content')
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ route('root.index') }}" rel="nofollow">Home</a>
                <span></span> Pages <span></span> Login / Register
            </div>
        </div>
    </div>

    <section class="pt-50 pb-50">
        <div class="container">
            <div class="login_wrap widget-taber-content p-30 background-white border-radius-10 mb-md-5 mb-lg-0 mb-sm-5">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $item)
                            {{ $item }}
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="padding_eight_all bg-white">
                    <div class="heading_s1">
                        <h3 class="mb-30">Login</h3>
                    </div>
                    <form method="post" action="{{ route('login') }}" id="form-login">
                        @csrf
                        <div class="form-group">
                            <input type="text" required="" name="username" placeholder="Your Username" />
                        </div>
                        <div class="form-group">
                            <input required="" type="password" name="password" placeholder="Password"
                                autocomplete="off" />
                        </div>
                        <div class="login_footer form-group">
                            <div class="chek-form">
                                <div class="custome-checkbox">
                                    <input class="form-check-input" type="checkbox" name="checkbox" id="remember-me"
                                        value="" />
                                    <label class="form-check-label" for="remember-me"><span>Remember me</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-fill-out btn-block hover-up" name="login">
                                Log in
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('#form-login [name="username"]').val(Cookies.get('username'));
                $('#form-login [name="password"]').val(Cookies.get('password'));
    
                if (Cookies.get('username') && Cookies.get('password')) {
                    $('#remember-me').prop('checked', true);
                }
            }, 1000);
        });

        $('#form-login').submit(function(e) {
            if ($('#remember-me').is(":checked")) {
                Cookies.set('username', $('#form-login [name="username"]').val());
                Cookies.set('password', $('#form-login [name="password"]').val());
            }
        });
    </script>
@endpush
