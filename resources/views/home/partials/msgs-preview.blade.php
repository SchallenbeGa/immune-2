<div id="feed-post-preview" hx-swap-oob="true">
  @forelse ($msgs as $entry)
    <div class="post-preview">
      <div class="post-meta">
        <div class="info">
          <span class="date">{{ $entry->created_at->format('F jS') }}</span>
        </div>
      </div>
      <a href="/ohlvc/{{ $entry->slug }}"
        hx-push-url="/signal/{{ $entry->id }}"
        hx-get="/htmx/signal/{{ $entry->id }}"
        hx-target="#app-body"
        class="preview-link"
      >
        <h1>{{ $entry->pair }}</h1>
        <p>{{ $entry->msg }}</p>

        <div class="m-t-1">
          <span>Read more...</span>

          <ul class="tag-list">
          
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