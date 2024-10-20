<!DOCTYPE html>
<html lang="en">
<head >
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>{{(isset($language) && $language)? 'PharmIQ Academy - Платформа для обучения провизоров и фармацевтов Узбекистана' : 'PharmIQ Academy - Dorixona xodimlarini o‘qitish uchun O‘zbekistondagi birinchi onlayn-platforma'}}</title>
    <meta property="og:title" content="PharmIq Academy" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://academy.pharmiq.uz/" />
    <meta property="og:image" content="{{asset('images/icon.png')}}" />
    <meta name="description" content="PharmIQ Academy - Платформа для обучения провизоров и фармацевтов Узбекистана">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/feather.css">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/icon.png')}}">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/feather.css')}}">
    <link rel="stylesheet" href="{{asset('css/video-player.css')}}">

</head>

<body class="color-theme-blue mont-font">

    <div class="preloader" style="display:none;"></div>

    
    <div class="main-wrapper">

        <!-- navigation -->
        @include('layouts.menu')
        
        <!-- navigation -->
        <!-- main content -->
        <div class="main-content">
            @include('layouts.header')
            @yield('main')
                       
        </div>
        @include('layouts.other')
        
    </div> 


    

   


    <script src="{{asset('js/plugin.js')}}"></script>
    <script src="{{asset('js/scripts.js')}}"></script>


    <script src="{{asset('js/video-player.js')}}"></script>
    
</body>

</html>