<div id="feed-post-preview" hx-swap-oob="true">
  @forelse ($ohlvc as $entry)
    <div class="post-preview">
      <div class="post-meta">
        <div class="info">
          <span class="date">{{ $entry->created_at->format('F jS') }}</span>
        </div>
      </div>
      <a href="/ohlvc/{{ $entry->slug }}"
        hx-push-url="/ohlvc/{{ $entry->slug }}"
        hx-get="/htmx/ohlvc/{{ $entry->slug }}"
        hx-target="#app-body"
        class="preview-link"
      >
        <h1>{{ $entry->slug }}</h1>
        <p>{{ $entry->open }}</p>

        <div class="m-t-1">
          <span>Read more...</span>

          <ul class="tag-list">
            
              <li class="tag-default tag-pill tag-outline">{{ $entry->volume }}</li>
          
          </ul>
        </div>
      </a>
    </div>
  @empty
  <div class="post-preview">
    <div class="alert alert-warning" role="alert">
      No trends here...
    </div>
  </div>
  @endforelse
</div>