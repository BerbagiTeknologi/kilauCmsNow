<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KILAU</title>
    <link href="{{ asset('assets/img/LogoKilau2.png') }}" rel="icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

     <!-- Fonts and icons -->
     <script src="{{ asset('assets_admin/js/plugin/webfont/webfont.min.js') }}"></script>
     <script>
       WebFont.load({
         google: { families: ["Public Sans:300,400,500,600,700"] },
         custom: {
           families: [
             "Font Awesome 5 Solid",
             "Font Awesome 5 Regular",
             "Font Awesome 5 Brands",
             "simple-line-icons",
           ],
           urls: ["{{ asset('assets_admin/css/fonts.min.css') }}"],
         },
         active: function () {
           sessionStorage.fonts = true;
         },
       });
     </script>
 
     <!-- CSS Files -->
     <link rel="stylesheet" href="{{ asset('assets_admin/css/bootstrap.min.css') }}" />
     <link rel="stylesheet" href="{{ asset('assets_admin/css/plugins.min.css') }}" />
     <link rel="stylesheet" href="{{ asset('assets_admin/css/kaiadmin.min.css') }}" />
 
     <link rel="stylesheet" href="{{ asset('assets_admin/css/demo.css') }}" />

     @yield('style')

</head>
<body>
    <div class="wrapper">
        @include('AdminPage.App.sidebard')

        <div class="main-panel">
            @include('AdminPage.App.navbar')
    
            @yield('content')
    
            @include('AdminPage.App.footer')
        </div>
    </div>


</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!--   Core JS Files   -->
<script src="{{ asset('assets_admin/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>-->

<script src="{{ asset('assets_admin/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets_admin/js/core/bootstrap.min.js') }}"></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets_admin/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets_admin/js/plugin/chart.js/chart.min.js') }}"></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('assets_admin/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('assets_admin/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets_admin/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets_admin/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Maps -->
<script src="{{ asset('assets_admin/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets_admin/js/plugin/jsvectormap/world.js') }}"></script>

<!-- Sweet Alert -->
<script src="{{ asset('assets_admin/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('assets_admin/js/kaiadmin.min.js') }}"></script>

<!-- Kaiadmin DEMO methods, don't include it in your project! -->
<script src="{{ asset('assets_admin/js/setting-demo.js') }}"></script>
<script src="{{ asset('assets_admin/js/demo.js') }}"></script>

@if ($message = Session::get('success'))
    <script>
        Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: "{{ $message }}",
        });
    </script>
@endif

@yield('scripts')
</html>