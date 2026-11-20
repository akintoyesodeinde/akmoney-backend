<!DOCTYPE html>
<html lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif >

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#5956e9">
    <title>{{ gs()->siteName(__($pageTitle ?? '')) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo')
    
    <link rel="icon" type="image/png" sizes="32x32" href="{{ siteFavicon() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ siteFavicon() }}">
    
    

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!--new css-->
   

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/bootstrap.min.css') }}">
    
     <link rel="stylesheet" href="{{asset('new_assetts/css/common/remixicon.css')}}">

    <link rel="stylesheet" href="{{asset('new_assetts/css/common/all.min.css')}}">

    <link rel="stylesheet" href="{{asset('new_assetts/css/common/bootstrap-icons.min.css')}}">

    <!--<link rel="stylesheet" href="{{asset('new_assetts/css/common/bootstrap.min.css')}}">-->

    <link rel="stylesheet" href="{{asset('new_assetts/css/common/jquery.toast.min.css')}}">

    <!--<link rel="stylesheet" href="{{asset('new_assetts/css/common/bootstrap-toggle.min.css')}}">-->

    <link rel="stylesheet" href="{{asset('new_assetts/css/common/select2.min.css')}}">

    <link rel="stylesheet"
        href="{{asset('new_assetts/css/frontend/default/magnific-popup.css')}}">

    <link rel="stylesheet" href="{{asset('new_assetts/css/frontend/default/aos.css')}}">

    <link rel="stylesheet" href="{{asset('new_assetts/css/frontend/default/intlTelInput.css')}}">

    <link rel="stylesheet" href="{{asset('new_assetts/css/frontend/default/slick.css')}}">

    <link rel="stylesheet" href="{{asset('new_assetts/css/frontend/default/style.css')}}">
    
    <link rel="stylesheet" href="{{asset('newassets/css/style.css')}}">

    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" />-->
    <!--<script src='https://www.google.com/recaptcha/api.js'></script>-->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="{{asset('new_assetts/css/kinmoney.css')}}">

    <!--end new css-->

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/fontawesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/nouislider.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/flatpickr.min.css') }}">
    @stack('css-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/style.css') }}">
    @stack('style')

    <style>
        :root {
            --display-font: "Work Sans", sans-serif;
            --body-font: "Work Sans", sans-serif;
            --primary: #{{ gs('base_color') }};
            --primary-color: #{{ gs('base_color') }};
            --secondary: #{{ gs('base_color') }};
            --base-color: #{{ gs('base_color') }};
            --btn-base-color: #{{ gs('base_color') }};
            /* AkMoney design tokens */
            --km-crimson: #A71B47;
            --km-crimson-dark: #8a1539;
            --km-teal: #0F4650;
            --km-teal-dark: #071318;
            --km-dark: #0a0a0a;
            --km-cream: #f7f5f0;
            --km-ink: #0d1a14;
            --km-ink-60: rgba(13,26,20,0.6);
            --km-ink-12: rgba(13,26,20,0.12);
            --km-teal-200: #8ecfae;
        }
        body { font-family: var(--body-font); color: var(--km-ink); }
    </style>

</head>

<body onload=""  class="body">




    <!--@if(@$banner && $banner->breadcrumb_status == 1 && isset($banner->breadcrumb_image))-->
    <!--    @include($theme.'partials.banner')-->
    <!--@endif-->

    <main id="main header-upper header-fixed">
        @yield('panel')
    </main>

    @stack('loadModal')
    
    <!--<script src="{{ asset($activeTemplateTrue.'js/jquery-3.6.1.min.js') }}"></script>-->
    <script src="{{ asset($activeTemplateTrue.'js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/common/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/common/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/common/jquery.toast.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/common/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/common/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/common/select2.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/frontend/default/aos.js') }}"></script>
    <script src="{{ asset('new_assetts/js/frontend/default/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/frontend/default/magnifc-popup.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/frontend/default/slick.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/frontend/default/apexcharts.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/common/jquery.basictable.min.js') }}"></script>
    <script src="{{ asset('new_assetts/js/frontend/default/app.js') }}"></script>
    
    <!-- JS Global Compulsory  -->
    <script src="{{ asset('assets/global/js/notiflix-aio-3.2.6.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/vue.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/axios.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/pusher.min.js') }}"></script>

    <!-- JS Library -->
    
    <script src="{{ asset($activeTemplateTrue.'js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/select2.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/nouislider.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/modernizr.custom.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/flatpickr.js') }}"></script>
     <!--Main Js link -->
    <script src="{{ asset($activeTemplateTrue.'js/main.js') }}"></script>

    @stack('js-lib')

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
     

    @stack('script')

    @include('partials.notify')



   <script>
      $('#senderModal').on('click', function () {
      $("#senderModal").modal("hide");
      $("body").removeClass("modal-open");
      $('.modal-backdrop').remove();
     });

   </script>
   <script>
      $('#receiverModal').on('click', function () {
      $("#receiverModal").modal("hide");
      $("body").removeClass("modal-open");
      $('.modal-backdrop').remove();
     });

   </script>
   
   <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/641b54b24247f20fefe770bd/1gs5c5mpd';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->

</body>
</html>



