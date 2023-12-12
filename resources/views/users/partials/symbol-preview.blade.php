<div id="user-post-preview" hx-swap-oob="true">
  @forelse ($symbols as $entry)
    <div class="post-preview">
      <div class="post-meta">
          <img src="{{ $entry->graph }}" class="img-fluid" alt="unresolved chart">
        <div class="info">
        <h1>{{ $entry->name }}</h1>
        
          <span class="date">{{ $entry->created_at }}</span>
        </div>
    
        @include('users.partials.favorite-button', [
          'favorite_count' => $entry->favoritedUsers->count(),
          'is_favorited' => auth()->user() ? $entry->favoritedByUser(auth()->user()) : false
        ])
    
      </div>
      <h1>{{ $entry->name }}</h1>

        <div>
          <span>Read more...</span>

         
        </div>
      </a>
    </div>
  @empty
  <div class="post-preview">
    <div class="alert alert-warning" role="alert">
      No articles are here... yet.
    </div>
  </div>
  @endforelse
</div>