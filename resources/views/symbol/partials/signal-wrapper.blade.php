<div id="symbol-trade-wrapper">
  @foreach ($data[0] as $entry)
    @include('symbol.partials.trade-card', ['entry' => $entry])
  @endforeach
  @foreach ($data[1] as $entry)
    @include('symbol.partials.signal-card', ['entry' => $entry])
  @endforeach
</div>