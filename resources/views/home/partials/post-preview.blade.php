<div id="feed-post-preview" hx-swap-oob="true">
  @forelse ($articles as $article)
    <div class="post-preview">
      <div class="post-meta">
       

        <div class="info">
         
          <span class="date">{{ $article->created_at->format('F jS') }}</span>
        </div>

        @include('home.partials.article-favorite-button', [
          'article' => $article,
          'favorite_count' => $article->favoritedUsers->count(),
          'is_favorited' => auth()->user() ? $article->favoritedByUser(auth()->user()) : false
        ])

      </div>
      <a href="/articles/{{ $article->slug }}"
        hx-push-url="/articles/{{ $article->slug }}"
        hx-indicator="#spinner" hx-get="/htmx/articles/{{ $article->slug }}"
        hx-target="#app-body"
        class="preview-link"
      >
        <h1>{{ $article->title }}</h1>
        <p>{{ $article->description }}</p>

        <div class="m-t-1" style="margin-bottom:1rem;">
          <span>Read more...</span>

          <ul class="tag-list" >
            @foreach ($article->tags as $tag)
              <li class="tag-default tag-pill tag-outline">{{ $tag->name }}</li>
            @endforeach
          </ul>
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