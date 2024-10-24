<div class="home-page">
  <div class="banner">
    <div class="container">
      <p>hypermedia x php</p>
      <p>content is some llm</p>
    </div>
  </div>

  <div class="container page">
    <div class="row">

      <div class="col-md-9">
      <nav id="feed-pagination"></nav>
        <div class="feed-toggle">
          <ul id="feed-navigation" class="nav nav-pills outline-active"></ul>
        </div>
       
        <div id="feed-post-preview"
          hx-trigger="load"

          @if (isset($tag))
            hx-indicator="#spinner" hx-get="/htmx/home/tag-feed/{{ $tag->name }}{{ isset(request()->page) ? '?page=' . request()->page : '' }}"
          @elseif (isset($personal))
            hx-indicator="#spinner" hx-get="/htmx/home/your-feed{{ isset(request()->page) ? '?page=' . request()->page : '' }}"
          @else
            hx-indicator="#spinner" hx-get="/htmx/home/global-feed{{ isset(request()->page) ? '?page=' . request()->page : '' }}"
          @endif
        ></div>

        <nav id="feed-pagination"></nav>
      </div>

      <div class="col-md-3">
        <div class="sidebar">
          <p>Popular Tags</p>

          <div id="popular-tag-list" class="tag-list"
            hx-trigger="load"
            hx-indicator="#spinner" hx-get="/htmx/home/tag-list"
          ></div>
        </div>
      </div>

    </div>
  </div>
</div>