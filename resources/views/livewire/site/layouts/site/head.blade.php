<head>
    <!-- Required meta tags -->
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! JsonLd::generate() !!}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=chrome" />
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('site/styles/style.global.css')}}" />
    <link rel="stylesheet" href="{{ asset('site/styles/bootstrap-grid.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('site/styles/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('site/styles/bootstrap.rtl.min.css')}}" />
    <link
        href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet"
    />
    <script src="https://cdn.ckeditor.com/4.13.0/basic/ckeditor.js"></script>
    <link rel="icon" type="image/png" href="{{ asset(\App\Models\Setting::getSingleRow('logo')) }}">
    @livewireStyles
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset('site/library/swiper/swiper.min.css?v=1.0.1')}}">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

</head>
