<main class="container">
  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="margin-top:10px">
    <div class="col-lg-6 px-0">
      <h1 class="display-4 fst-italic">immune-web</h1>
      <p class="lead my-3">Second life of an old buy/sell automat in python.</p>
    </div>
  </div>

  <div class="row g-5">
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