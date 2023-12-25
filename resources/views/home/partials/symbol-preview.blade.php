<div id="feed-post-preview" hx-swap-oob="true">
<h3 class="pb-4 mb-4 fst-italic border-bottom">
      {{$total_invested}} made {{ $total }} / fee rate 0.1000%
      </h3>
  @forelse ($symbol as $entry)
  <a href="/symbol/{{ $entry->name }}" hx-push-url="/symbol/{{ $entry->name }}" hx-get="/htmx/symbol/{{ $entry->name }}" hx-target="#app-body" class="preview-link">
    <article class="blog-post">
      <h2 class="display-5 link-body-emphasis mb-1">{{ $entry->name }}</h2>
      <p class="blog-post-meta">{{ $entry->updated_at }}</p>
      @include('home.partials.symbol-favorite-button', [
      'symbol' => $entry,
      'favorite_count' => $entry->favoritedUsers->count(),
      'is_favorited' => auth()->user() ? $entry->favoritedByUser(auth()->user()) : false
      ])
      <p>1k $ invested {{ $entry->created_at->format('F jS') }} (without orderbook !) : {{$entry->profit}} ( {{$entry->nb_trade ?? 0}} trades)</p>
      <hr>
      <p>See symbol.</p>
    </article>
  </a>
</div>
@empty
<div class="post-preview">
  <div class="alert alert-warning" role="alert">
    Nothing to see here...
  </div>
</div>
@endforelse
</div>