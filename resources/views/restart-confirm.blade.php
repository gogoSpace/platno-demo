@extends('layouts.demo')
@section('title', 'Choose a new canvas · Platno')
@section('content')
<section class="status-panel"><p class="eyebrow">YOUR NEXT CANVAS</p><h1>Start a<br><em>different story?</em></h1><p>You already have a demo in progress. Starting this template replaces it, including its saved pages, published links and uploaded files.</p><form method="post" action="/restart">@csrf<input type="hidden" name="confirmed" value="1"><input type="hidden" name="template" value="{{ $template }}"><button class="button" type="submit">Start this template ↗</button></form><a href="/studio" class="text-link" style="margin-top:24px">Keep my current demo ↗</a></section>
@endsection
