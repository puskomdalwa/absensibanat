<!DOCTYPE html>
<html class="no-js')}}" lang="en">

<head>
    @include('layouts.home.header')

    @stack('css')
</head>

<body>
    @include('layouts.home.navbar')

    <main class="main">
        @yield('content')
    </main>

    @include('layouts.home.footer')

    @include('layouts.home.preloader')

    @include('layouts.home.scripts')

    @stack('scripts')

</body>

</html>
