@extends('layouts.demo')
@section('title', $page->title.' · Platno studio')
@section('body-class', 'demo-studio')
@section('no-footer', 'yes')
@push('head')
    <meta name="robots" content="noindex, nofollow">
    @if (($adapter ?? 'native') === 'livewire')
        @livewireStyles
    @endif
    @if (in_array($adapter ?? 'native', ['vue', 'react'], true))
        <script type="importmap">{"imports":{"react":"https://esm.sh/react@19.3.0","react-dom/client":"https://esm.sh/react-dom@19.3.0/client?external=react","vue":"https://cdn.jsdelivr.net/npm/vue@3.5.43/dist/vue.esm-browser.prod.js"}}</script>
    @endif
@endpush
@section('content')
<section class="studio-intro">
    <div><h1>Your canvas<span class="blue-dot">.</span></h1><p>{{ $page->title }} · Your private demo copy</p></div>
    <div class="studio-actions"><span>Available until <time data-expires-at="{{ $expiresAt }}">{{ $workspace->expires_at->format('M j, H:i') }} UTC</time></span><a href="/#templates">Templates</a><button type="button" class="button button-quiet" id="restart-demo">Start over</button></div>
</section>
<section class="studio-shell" aria-label="Page editor">
    <div class="studio-meta"><span>This is the Platno package editor. Click a block to begin.</span><label class="adapter-picker" for="adapter">Integration <select id="adapter" name="adapter">@foreach (['native' => 'Native · no framework', 'vue' => 'Vue', 'react' => 'React', 'livewire' => 'Livewire'] as $value => $label)<option value="{{ $value }}" @selected(($adapter ?? 'native') === $value)>{{ $label }}</option>@endforeach</select></label></div>
    <p id="workspace-feedback" class="studio-feedback" role="status" aria-live="polite"></p>
    @if (($adapter ?? 'native') === 'livewire')
        <livewire:platno-editor :page-identifier="$page->getKey()" />
    @else
        <div id="workspace" class="workspace-mount"><div class="workspace-loading" role="status">Preparing your canvas…</div></div>
    @endif
    <details class="studio-help"><summary>A few things to know</summary><p>Click text or a heading to edit it on the canvas. Select other blocks to change their settings in the inspector. Save your draft, then publish to create a read-only share link. Changes to the draft stay private until you publish again.</p><p>Your browser owns this demo. It expires 24 hours after creation, including its published pages and uploads. The integration selector changes how the same editor is mounted; it keeps editing the same page.</p></details>
</section>
<dialog id="restart-dialog" class="demo-dialog"><h2>A fresh canvas?</h2><p>This permanently removes your current demo, its published links and uploaded files. You will get a new copy of this template.</p><div class="dialog-actions"><button class="button button-quiet" type="button" data-close-dialog>Keep editing</button><form method="post" action="/restart">@csrf<input type="hidden" name="confirmed" value="1"><input type="hidden" name="template" value="{{ $workspace->template }}"><button class="button" type="submit">Start fresh</button></form></div></dialog>
<script type="application/json" id="studio-settings">{!! json_encode(['adapter' => $adapter ?? 'native', 'editorAddress' => $editorAddress, 'moduleAddress' => route('platno.editor.workspace'), 'shareAddress' => $shareAddress, 'published' => $page->publication_id !== null, 'expiresAt' => $expiresAt], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endsection
@push('scripts')
    @if (($adapter ?? 'native') === 'livewire')
        @livewireScripts
    @endif
    <script type="module" src="{{ asset('scripts/studio.js') }}"></script>
@endpush
