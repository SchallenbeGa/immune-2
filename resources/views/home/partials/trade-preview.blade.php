<div id="feed-post-preview" hx-swap-oob="true">
  @forelse ($trades as $entry)
    <div class="post-preview">
      <div class="post-meta">
        <div class="info">
          <span class="date">{{ $entry->updated_at }}</span>
        </div>
      </div>
      <a href="/symbol/{{ $entry->name }}"
        hx-push-url="/symbol/{{ $entry->name }}"
        hx-get="/htmx/symbol/{{ $entry->name }}"
        hx-target="#app-body"
        class="preview-link"
      >
        <p>{{ $entry->name }} - {{ $entry->side}} at {{ $entry->price}}</p>
        <div class="m-t-1">
          <span>See symbol</span>
        </div>
      </a>
    </div>
  @empty
  <div class="post-preview">
    <div class="alert alert-warning" role="alert">
      No trades yet...
    </div>
  </div>
  @endforelse
</div>