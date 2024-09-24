<div id="feed-post-preview-light" hx-swap-oob="true">
  @forelse ($articles as $article)
      <a href="/articles/{{ $article->slug }}"
        hx-push-url="/articles/{{ $article->slug }}"
        hx-get="/htmx/articles/{{ $article->slug }}"
        hx-target="#app-body"
      >
        <h3>{{ $article->title }}</h3>
        <p>{{ $article->description }}</p>
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