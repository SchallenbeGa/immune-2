<div class="col-md-12" >
<div id="symbol-trade-wrapper">
<table class="table">
  <tr>
    <th>Date</th>
    <th>Type</th>
    <th>Price</th>
  </tr>
  @forelse ($data[0] as $entry)
    @include('symbol.partials.trade-card', ['entry' => $entry])
    @empty
    <div class="alert alert-warning" role="alert">
      No trades here yet..
    </div>
  @endforelse
  </table>
</div>
  <div class="col-md-12" >
  <table class="table">
  <tr>
    <th>Date</th>
    <th>Contact</th>
  </tr>
  @forelse ($data[1] as $entry)
    @include('symbol.partials.signal-card', ['entry' => $entry])
  @empty
    <div class="alert alert-warning" role="alert">
      No signals here yet..
    </div>
  @endforelse
  </table>
</div>
</div>