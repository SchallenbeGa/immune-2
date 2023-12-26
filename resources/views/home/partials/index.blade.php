<main class="container">
  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary">
    <div class="col-lg-6 px-0">
      <h1 class="display-4 fst-italic">immune-web</h1>
      <p class="lead my-3">Second life of an old buy/sell automat in python.</p>
    </div>
  </div>

  <div class="row g-5">
    <div class="col-md-8">
      <div id="feed-post-preview" hx-trigger="load, every 2s" @if (isset($tag)) hx-get="/htmx/home/tag-feed/{{ $tag->name }}{{ isset(request()->page) ? '?page=' . request()->page : '' }}" @elseif (isset($personal)) hx-get="/htmx/home/your-feed{{ isset(request()->page) ? '?page=' . request()->page : '' }}" @else hx-get="/htmx/home/global-feed" @endif></div>
    </div>
  </div>
  </div>
</main>