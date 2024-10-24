<button style="color:black;margin:0.2rem;" class="btn btn-sm {{ $is_favorited ? 'active' : '' }} favorite-button"
  hx-indicator="#spinner" hx-post="/htmx/articles/{{ $article->slug }}/favorite"

  @if (isset($oob_swap))
  hx-swap-oob="outerHTML:.favorite-button"
  @endif
>
  <i class="ion-heart"></i>
  @if ($is_favorited)
  unlove
  @else
  love
  @endif
  ({{ $favorite_count }})
</button>