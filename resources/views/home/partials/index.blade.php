<div class="home-page">
  <div class="banner">
    <div class="container">
      <h1 class="logo-font">immune-web</h1>
      <p>A place to share cooking knowledge.</p>
    </div>
  </div>

  <div class="container page">
    <div class="row">
    
      <div class="col-md-9">
      @include('settings.partials.form-message')
      <input class="form-control" type="search" name="content" placeholder="Search for symbol or ISIN" 
       hx-post="/htmx/home/search" 
       hx-trigger="input changed delay:500ms, search" 
       hx-target="#symbol-list" 
       hx-indicator=".htmx-indicator">
       <span class="htmx-indicator"> 
    <img src="/img/bars.svg"/> Searching... 
   </span> 
       <div id="symbol-list" class="tag-list"
       hx-trigger="every 1s"
            hx-get="/htmx/home/symbol-list"
        ></div>

        <div class="feed-toggle">
          <ul id="feed-navigation" class="nav nav-pills outline-active"></ul>
        </div>

        <div id="feed-post-preview"
        hx-trigger="every 1s"

          @if (isset($tag))
            hx-get="/htmx/home/tag-feed/{{ $tag->name }}{{ isset(request()->page) ? '?page=' . request()->page : '' }}"
          @elseif (isset($personal))
            hx-get="/htmx/home/your-feed{{ isset(request()->page) ? '?page=' . request()->page : '' }}"
          @else
            hx-get="/htmx/home/global-feed"
          @endif
        ></div>

        <nav id="feed-pagination"></nav>
      </div>

      <div class="col-md-3">
        <div class="sidebar">
         

          
        </div>
      </div>

    </div>
  </div>
</div>