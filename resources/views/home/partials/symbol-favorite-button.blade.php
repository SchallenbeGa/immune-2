<button class="btn btn-outline-primary btn-sm pull-xs-right {{ $is_favorited ? 'active' : '' }}"
  hx-post="/htmx/home/symbol/{{ $symbol->name }}/favorite"
  hx-swap="outerHTML"
>
  <i class="ion-heart"></i> {{ $favorite_count }}
</button>