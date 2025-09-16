<head>
    {{-- Tambahkan baris ini --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | Loogbook Apps</title>
    
    <meta charset="utf-8" />
    <meta name="description" content="Aplikasi logbook untuk mencatat dan mengelola kegiatan harian Anda dengan mudah. Dirancang untuk efisiensi, produktivitas, dan monitoring pekerjaan." />
    <meta name="keywords" content="logbook, aplikasi logbook, catatan harian, laporan harian, produktivitas, manajemen tugas, daily report, aplikasi kerja, monitoring, absensi online" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Aplikasi Logbook Harian" />
    
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    {{-- Tambahkan setelah link CSS yang sudah ada --}}
    <link href="{{ asset('assets/css/mobile-nav.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}" />
    
    @stack('css')

</head>