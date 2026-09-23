@extends('layouts.demo')
@section('title', 'This canvas has expired · Platno')
@section('content')
<section class="status-panel"><p class="eyebrow">EXPERIMENTAL PREVIEW / DEMO EXPIRED</p><h1>A canvas for<br>the <em>next idea.</em></h1><p>This {{ ($shared ?? false) ? 'shared page' : 'demo' }} is no longer available. Demo spaces last 24 hours, and starting over also retires their published links.</p><p>Your next canvas is one click away. No account needed.</p><form method="post" action="/play">@csrf<input type="hidden" name="template" value="studio"><button class="button" type="submit">Create a new demo <span aria-hidden="true">↗</span></button></form></section>
@endsection
