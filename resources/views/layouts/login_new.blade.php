<!DOCTYPE html>

<html
    lang="en"
    class="light-style layout-wide customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{asset('')}}"
    data-template="vertical-menu-template">
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>@yield('title', config('app.name'))</title>

    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{asset('hakim.jpeg')}}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet"/>

    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('main/fonts/remixicons/remixicon.min.css')}}"/>

    <!-- Menu waves for no-customizer fix -->

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('main/libs/perfect-scrollbar/perfect-scrollbar.css')}}"/>
    <link rel="stylesheet" href="{{asset('main/libs/sweetalert2/sweetalert2.css')}}"/>

    <!-- Page CSS -->
    @yield('style')

    <style>
        .transparent-swal2 .swal2-popup {
            background-color: transparent !important; /* Make dialog background transparent */
            box-shadow: none !important; /* Remove box-shadow */
        }

        .swal2-container.transparent-swal2 {
            background-color: rgba(0, 0, 0, 0.6); /* Adjust backdrop color and transparency */
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
    </style>
    <!-- Core CSS -->
    {{--    <link rel="stylesheet" href="{{asset('css/demo.css')}}"/>--}}

    <link rel="stylesheet" href="{{asset('main/css/core.css')}}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{asset('main/css/theme-default.css')}}" class="template-customizer-theme-css"/>

    <!-- Helpers -->
    <script src="{{asset('main/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{asset('main/js/template-customizer.min.js')}}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset('js/config.js')}}"></script>


</head>

<body>
@yield('content')

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{asset('main/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('main/libs/popper/popper.js')}}"></script>
<script src="{{asset('main/js/bootstrap.js')}}"></script>
<script src="{{asset('main/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{asset('main/libs/hammer/hammer.js')}}"></script>
<script src="{{asset('main/js/menu.js')}}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{asset('main/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('main/libs/sweetalert2/sweetalert2.js')}}"></script>

<!-- Main JS -->
<script src="{{asset('js/main.js')}}"></script>

<script src="{{asset('js/alerts.js')}}"></script>

<!-- Page JS -->
@yield('script')

<script>
    @if(session()->has('alert'))
        {!! session('alert') !!}
    @endif

</script>
<script>
    function updateClock() {
        let now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();

        // document.getElementById('clock').innerHTML = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
    }

    // updateClock();
    // setInterval(updateClock, 1000);
</script>

</body>
</html>
