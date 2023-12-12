<div id="feed-post-preview" hx-swap-oob="true">
  @forelse ($symbol as $entry)
    <div class="post-preview">
      <div class="post-meta">
        <div class="info">
          <span class="date">{{ $entry->updated_at }}</span>
        </div>
        @include('home.partials.symbol-favorite-button', [
          'symbol' => $entry,
          'favorite_count' => $entry->favoritedUsers->count(),
          'is_favorited' => auth()->user() ? $entry->favoritedByUser(auth()->user()) : false
        ])
      </div>
      <a href="/symbol/{{ $entry->name }}"
        hx-push-url="/symbol/{{ $entry->name }}"
        hx-get="/htmx/symbol/{{ $entry->name }}"
        hx-target="#app-body"
        class="preview-link"
      >
        <h1>{{ $entry->name }} | PNL : {{$entry->pnl}}</h1>
        <p>{{ $entry->last_trade }}</p>
        <p>{{ $entry->last_msg }}</p>
        <img src="{{ $entry->graph }}" class="img-fluid" alt="unresolved chart">
        <div class="m-t-1">
          <span>See symbol</span>
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