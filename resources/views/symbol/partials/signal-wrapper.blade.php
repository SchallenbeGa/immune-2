<div id="symbol-trade-wrapper">
<table class="table">
  <tr>
    <th>Date</th>
    <th>Type</th>
    <th>Price</th>
  </tr>
  @foreach ($data[0] as $entry)
    @include('symbol.partials.trade-card', ['entry' => $entry])
  @endforeach
  </table>
  <table class="table">
  <tr>
    <th>Date</th>
    <th>Contact</th>
  </tr>
  @foreach ($data[1] as $entry)
    @include('symbol.partials.signal-card', ['entry' => $entry])
  @endforeach
  </table>
</div>

