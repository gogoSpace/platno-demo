@extends('layouts.demo')
@section('content')
<section class="intro" aria-labelledby="intro-title">
    <div class="intro-top"><p class="eyebrow">THE VISUAL PAGE EDITOR FOR LARAVEL</p><p class="edition">EXPERIMENT NO. 01 / AN OPEN CANVAS</p></div>
    <div class="intro-heading"><h1 id="intro-title">From blank<br>to <em>published.</em></h1><div class="intro-aside"><p>A little structure.<br>A lot of possibility.</p><p class="muted">Create with blocks. Make it your own.<br>Publish something worth sharing.</p><form action="/play" method="post">@csrf<input type="hidden" name="template" value="studio"><button class="button" type="submit">Try the editor <span aria-hidden="true">↗</span></button></form></div></div>
</section>
<section class="hero-preview" aria-label="Studio template preview">
    <img src="{{ asset('images/atelier.jpg') }}" alt="A cobalt blue sculptural chair in a sunlit design atelier, beside a travertine worktable" fetchpriority="high" width="1536" height="1024">
    <div class="hero-caption"><span class="eyebrow">01 / STUDIO FORMA</span><p>A practice in<br><em>possibility.</em></p><span class="hero-caption-note">A real page. Ready for your ideas.</span></div>
    <form action="/play" method="post" class="hero-action">@csrf<input type="hidden" name="template" value="studio"><button class="button button-light" type="submit">Try the editor <span aria-hidden="true">↗</span></button><span>Your own copy. No sign-up.</span></form>
    <span class="photo-credit">ORIGINAL DEMO ARTWORK / MADE WITH AI</span>
</section>
<div class="proof-strip"><span>Built for Laravel</span><span>Open source, MIT</span><span>No frontend build required</span><span>Your content. Your code.</span></div>
@if ($ownedWorkspace ?? null)
<section class="resume-strip"><div><strong>Your canvas is waiting.</strong><span>Pick up where you left off.</span></div><a class="text-link" href="/studio">Continue editing ↗</a></section>
@endif
<section class="templates-section" id="templates" aria-labelledby="templates-title">
    <div class="section-heading"><div><p class="eyebrow">A PLACE TO START</p><h2 id="templates-title">Different stories.<br>The same <em>canvas.</em></h2></div><p>Choose a starting point. Every page is made<br>of real, editable blocks — including the images.</p></div>
    <div class="template-grid">
        @foreach ($templates as $identifier => $template)
            <article class="template-item">
                <form method="post" action="/play">@csrf<input type="hidden" name="template" value="{{ $identifier }}"><button type="submit" class="template-picture" aria-label="Edit {{ $template['name'] }} template"><img src="{{ asset('images/'.(['studio' => 'atelier', 'story' => 'story', 'product' => 'object'][$identifier]).'.jpg') }}" alt="{{ $template['description'] }}" loading="lazy" width="1536" height="1024"><span class="template-edit">Make it yours ↗</span></button></form>
                <div class="template-heading"><h3>{{ $template['name'] }}</h3><span>0{{ $loop->iteration }}</span></div><p>{{ $template['description'] }}</p>
            </article>
        @endforeach
    </div>
    <p class="template-note">Starting another template will ask before replacing your current demo.</p>
</section>
<section class="process-section" aria-labelledby="process-title">
    <div class="section-heading"><div><p class="eyebrow">SMALL STEPS. REAL PAGES.</p><h2 id="process-title">Make a change.<br>See it <em>happen.</em></h2></div><p>A visual workspace on top of a Laravel package.<br>The editor you try is the editor you install.</p></div>
    <ol class="process-list"><li><span>01</span><h3>Shape it.</h3><p>Click a block. Write a headline. Move a section. Undo an idea and try another.</p></li><li><span>02</span><h3>Make it yours.</h3><p>Choose a photograph, add a link, or upload your own. Your copy belongs to your browser.</p></li><li><span>03</span><h3>Put it out there.</h3><p>Save your draft and publish. Open a real page, with a link that lasts as long as your demo.</p></li></ol>
</section>
<section class="developer-teaser"><div><p class="eyebrow">YOUR STACK. YOUR RULES.</p><h2>Laravel at heart.<br>Room to <em>extend.</em></h2><p>Use the ready-made editor or mount it inside Vue, React or Livewire. Build your own blocks with PHP, Blade and a few fields.</p><a href="/developers" class="button button-light">Under the canvas <span aria-hidden="true">↗</span></a></div><div class="code-art" aria-label="Example custom block definition"><span>YOUR FIRST CUSTOM BLOCK</span><pre><code>final class Callout extends Plugin
{
    public function type(): string
    {
        return 'example.callout';
    }

    <span>// Your fields. Your Blade view.</span>
}</code></pre><div class="stack-labels"><span>PHP + Blade</span><span>Vue</span><span>React</span><span>Livewire</span></div></div></section>
<section class="closing"><p class="eyebrow">EXPERIMENTAL PREVIEW</p><h2>Still taking shape.<br>Already <em>yours to try.</em></h2><p>This is an early working prototype. Explore it, break assumptions,<br>and help us decide what comes next.</p><form action="/play" method="post">@csrf<input type="hidden" name="template" value="studio"><button class="button" type="submit">Open your canvas <span aria-hidden="true">↗</span></button></form></section>
@endsection
