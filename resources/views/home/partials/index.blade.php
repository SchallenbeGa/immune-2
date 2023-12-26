<main class="container">
<div class="alert alert-danger" style="margin-top:10px;" role="alert">
  No financial advice, following content are the result from a <a href="https://fr.wikipedia.org/wiki/Backtesting" target="_blank">backtesting</a> session !
</div>
  <div class="row p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="margin-top:10px">
    <div class="col-lg-5 px-0">
      <h1 class="display-4 fst-italic">immune-web</h1>
      <p class="lead my-3">Second life of an old buy/sell automat in python.</p>

    </div>
    <!-- <div class="col-lg-7">
    <img class="img_o" width="100%" height="100%" src="/img/robo.png">
    </div> -->
  </div>
  <!-- <div class="row mb-2">
    <div class="col-md-6">
      <div class="card flex-md-row mb-4 box-shadow h-md-250">
        <div class="card-body d-flex flex-column align-items-start">
          <strong class="d-inline-block mb-2 text-primary">gab</strong>
          <h3 class="mb-0">
            <a class="text-dark" href="#">Robo Advisor or backtest ?</a>
          </h3>
          <div class="mb-1 text-muted">Nov 12</div>
          <p class="card-text mb-auto">Totally backtesting without order book match</p>
          <a href="#">Continue reading</a>
        </div>
        <img class="card-img-right flex-auto d-none d-md-block" alt="Thumbnail [200x250]" style="width: 200px; height: 250px;" src="img/1.webp">
      </div>
    </div>
    <div class="col-md-6">
      <div class="card flex-md-row mb-4 box-shadow h-md-250">
        <div class="card-body d-flex flex-column align-items-start">
          <strong class="d-inline-block mb-2 text-primary">gab</strong>
          <h3 class="mb-0">
            <a class="text-dark" href="#">Robo Advisor or backtest ?</a>
          </h3>
          <div class="mb-1 text-muted">Nov 12</div>
          <p class="card-text mb-auto">Totally backtesting without order book match</p>
          <a href="#">Continue reading</a>
        </div>
        <img class="card-img-right flex-auto d-none d-md-block" alt="Thumbnail [200x250]" style="width: 200px; height: 250px;" src="img/2.png">
      </div>
    </div>
    
    
  </div> -->
  <div class="row">
    <div class="col-md-8">

      <div id="feed-post-preview" hx-trigger="load, every 2s" @if (isset($tag)) hx-get="/htmx/home/tag-feed/{{ $tag->name }}{{ isset(request()->page) ? '?page=' . request()->page : '' }}" @elseif (isset($personal)) hx-get="/htmx/home/your-feed{{ isset(request()->page) ? '?page=' . request()->page : '' }}" @else hx-get="/htmx/home/global-feed" @endif></div>
    </div>
    <div class="col-md-4 more">
      <div class="position-sticky" style="top: 2rem;">
        <div class="p-4 mb-3 bg-body-tertiary rounded">
          <h4 class="fst-italic">About</h4>
          <p class="mb-0">Du latin immunis (« libre de charge »).</p>
        </div>

        <div>
          <h4 class="fst-italic">Recent trades</h4>
          <ul class="list-unstyled">
            <div id="trade-post-preview" hx-trigger="load" hx-get="/htmx/home/trade-feed"></div>
          </ul>
        </div>
      </div>
    </div>
  </div>
</main>