<figure class="demo-quote demo-quote--{{ $data['tone'] }}">
    <span class="demo-quote-mark" aria-hidden="true">“</span>
    <blockquote><p>{{ $data['quote'] }}</p></blockquote>
    @if ($data['author'] !== '' || $data['detail'] !== '')
        <figcaption>
            @if ($data['author'] !== '')<span class="demo-quote-author">{{ $data['author'] }}</span>@endif
            @if ($data['detail'] !== '')<span class="demo-quote-detail">{{ $data['detail'] }}</span>@endif
        </figcaption>
    @endif
</figure>
