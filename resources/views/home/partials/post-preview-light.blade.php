<div id="feed-post-preview-light" hx-swap-oob="true">
  @forelse ($articles as $article)
    <div class="post-preview">
      <div class="post-meta">
       

        <div class="info">
         
          <span class="date">{{ $article->created_at->format('F jS') }}</span>
        </div>

        
      </div>
      <a href="/articles/{{ $article->slug }}"
        hx-push-url="/articles/{{ $article->slug }}"
        hx-get="/htmx/articles/{{ $article->slug }}"
        hx-target="#app-body"
        class="preview-link"
      >
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