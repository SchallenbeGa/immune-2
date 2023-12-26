<form 
  style="margin:10px;justify-content: flex-end;display: flex;"
  hx-post="/htmx/home/symbol/{{ $symbol->name }}/favorite" 
>
@csrf
<button class="btn like btn-outline-secondary btn-sm pull-xs-right {{ $is_favorited ? 'active' : '' }}"
  hx-swap="outerHTML"
  style="color: grey;border-color:darkgrey;:hover { color: red; }"
>
{{ $favorite_count }} ♡
</button>
</form>