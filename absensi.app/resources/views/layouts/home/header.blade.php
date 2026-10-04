<meta charset="utf-8" />
@if (!in_array(request()->getHost(), ['localhost', '127.0.0.1', '::1']) && !str_ends_with(request()->getHost(), '.test') && !str_ends_with(request()->getHost(), '.local'))
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
@endif
<title>@yield('title')</title>
<meta http-equiv="x-ua-compatible" content="ie=edge" />
<meta name="description" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta property="og:title" content="" />
<meta property="og:type" content="" />
<meta property="og:url" content="" />
<meta property="og:image" content="" />
<!-- Favicon -->
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('home/assets/imgs/theme/logo.ico') }}" />
<!-- Google Fonts: Montez & Plus Jakarta Sans -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montez&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<!-- Template CSS -->
<link rel="stylesheet" href="{{ asset('home/assets/css/plugins/datatables.css') }}" />
<link rel="stylesheet" href="{{ asset('home/assets/css/main.css?v=3.4') }}" />
<link rel="stylesheet" href="{{ asset('home/assets/css/loader.css') }}" />
<link rel="stylesheet" href="{{ asset('home/assets/css/plugins/toastr.min.css') }}" />
<!-- Banat Feminine Luxury Theme -->
<link rel="stylesheet" href="{{ asset('home/assets/css/banat-theme.css?v=6.6') }}" />

<!-- Instant Theme Init Script (Zero-Flicker) -->
<script>
    (function() {
        try {
            var savedTheme = localStorage.getItem('banat_theme');
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        } catch(e) {}
    })();
</script>
