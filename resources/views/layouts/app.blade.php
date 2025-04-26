<!doctype html>
<html lang="en">
<head>
    <title>@yield('title', ':: Iconic :: Home')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Iconic Laravel Admin Template">
    <meta name="author" content="WrapTheme, design by: ThemeMakker.com">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/charts-c3/plugin.css') }}"/>

    <!-- MAIN Project CSS file -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <!-- Livewire Styles -->
    @livewireStyles

    @yield('page_styles')
</head>
<body data-theme="light" class="font-nunito">
<div id="wrapper" class="theme-cyan">
    <!-- Page Loader -->
    @include('partials.page_loader')

    <!-- Top navbar div start -->
    @include('partials.top_navbar')

    <!-- main left menu -->
    @include('partials.left_sidebar')

    <!-- main page content body part -->
    <div id="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
</div>

<!-- Livewire Scripts - MOVED ABOVE other scripts -->
@livewireScripts

<!-- Javascript -->
<script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
<script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script>
<!-- page vendor js files -->
<script src="{{ asset('assets/vendor/toastr/toastr.js') }}"></script>
<script src="{{ asset('assets/bundles/c3.bundle.js') }}"></script>
<!-- page js file -->
<script src="{{ asset('assets/bundles/mainscripts.bundle.js') }}"></script>
<script src="{{ asset('js/index.js') }}"></script>

<!-- Livewire-jQuery Compatibility Script -->
<script>
    // Ensure Livewire and jQuery play nicely together
    document.addEventListener('livewire:load', function () {
        // Handle jQuery-based events to Livewire components
        $(document).on('click', '[wire\\:click]', function(e) {
            // Prevent default only if the element has wire:click
            if ($(this).attr('wire:click')) {
                // Don't prevent default - let Livewire handle it
            }
        });

        // Fix for Next button specifically
        $(document).on('click', '#next-step-button', function(e) {
            console.log('Next button clicked via jQuery handler');
            // Let the event continue to Livewire
        });

        // Debug Livewire status
        console.log('Livewire loaded and jQuery compatibility script initialized');
    });
</script>

@yield('page_scripts')
</body>
</html>
