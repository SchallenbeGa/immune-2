<div id="symbol-data-wrapper">
  @foreach ($data as $entry)
    @include('symbol.partials.data-card', ['entry' => $entry])
  @endforeach
</div>