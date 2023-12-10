<div id="symbol-list" class="tag-list" hx-swap-oob="true">
  @foreach ($symbols as $symbol)
    <a class="label label-pill label-default"
      href="/symbol/{{ $symbol->name }}"
    >{{ $symbol->name }}</a>
    @endforeach
</div>