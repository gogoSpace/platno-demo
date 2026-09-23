<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="From blank to published. Try Platno, an open-source visual page editor for Laravel. Experimental Preview.">
    <meta name="theme-color" content="#f5f3ed">
    <meta property="og:title" content="Platno — From blank to published.">
    <meta property="og:description" content="A visual page editor for Laravel. Open source. Yours to shape. Experimental Preview.">
    <meta property="og:image" content="{{ url('/images/atelier.jpg') }}">
    <title>@yield('title', 'Platno — From blank to published.')</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('styles/demo.css') }}">
    @stack('head')
</head>
<body class="@yield('body-class', 'demo-site')">
    <a class="skip-link" href="#main">Skip to content</a>
    @include('partials.header')
    @if ($errors->any())
        <div class="demo-feedback" role="alert">{{ $errors->first() }}</div>
    @endif
    @if (session('status'))
        <div class="demo-feedback" role="status">{{ session('status') }}</div>
    @endif
    <main id="main">@yield('content')</main>
    @hasSection('no-footer')
    @else
        @include('partials.footer')
    @endif
    <script type="module" src="{{ asset('scripts/demo.js') }}"></script>
    @stack('scripts')
</body>
</html>
