<section class="demo-hero demo-hero--{{ $data['variant'] }}">
    @if ($data['eyebrow'] !== '')
        <p class="demo-hero-eyebrow">{{ $data['eyebrow'] }}</p>
    @endif
    <div class="demo-hero-heading">
        <h2 class="demo-hero-title">{{ $data['title'] }}</h2>
        @if ($data['description'] !== '')
            <p class="demo-hero-description">{{ $data['description'] }}</p>
        @endif
    </div>
    <figure class="demo-hero-media">
        <img src="{{ route($preview ? 'platno.assets.show' : 'platno.public.asset', $data['image']) }}" alt="{{ $data['alt'] }}" width="1536" height="1024" fetchpriority="high">
        @if ($data['caption'] !== '')
            <figcaption>{{ $data['caption'] }}</figcaption>
        @endif
    </figure>
</section>
