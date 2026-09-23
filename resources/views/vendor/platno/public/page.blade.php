@php($workspaceExpiresAt = request()->attributes->get('demoWorkspace')?->expires_at)
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A page made with Platno, the open-source editor for Laravel.">
    <title>{{ $publication->title }} — Made with Platno</title>
    @include('platno::public.styles')
</head>
<body class="platno-page demo-published-page">
    <a class="demo-skip-link" href="#page-content">Skip to content</a>
    <header class="demo-page-bar">
        <a class="demo-page-wordmark" href="/" aria-label="Back to the Platno demo">platno<span aria-hidden="true">.</span></a>
        <span class="demo-page-context">Experimental Preview</span>
        <a class="demo-page-back" href="/">Back to the demo <span aria-hidden="true">↗</span></a>
    </header>
    <main id="page-content">
        @if ($preview ?? false)
            <aside class="platno-preview-notice" role="status">{{ __('platno::editor.private_preview') }}</aside>
        @endif
        <h1>{{ $publication->title }}</h1>
        @foreach ($blocks as $block)
            {!! $block !!}
        @endforeach
    </main>
    <footer class="demo-page-footer">
        <div class="demo-page-footer-note">
            <p>A page with a point of view. Built with Platno.</p>
            <p class="demo-page-expiration">
                @if ($workspaceExpiresAt)
                    This temporary demo expires <time datetime="{{ $workspaceExpiresAt->toIso8601String() }}">{{ $workspaceExpiresAt->format('j M Y, H:i T') }}</time>.
                @else
                    Experimental Preview · Temporary demo page.
                @endif
            </p>
        </div>
        <a href="/developers#plugins">Explore the custom blocks <span aria-hidden="true">↗</span></a>
    </footer>
</body>
</html>
