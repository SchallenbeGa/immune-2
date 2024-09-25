<div class="post-page">

  <div class="banner">
    <div class="container">

      <h1>{{ $article->title }}</h1>

      <div class="post-meta">
        <a href="profile.html"></a>
        <div class="info">
          <span class="date">{{ $article->created_at->format('F jS') }}</span>
        </div>

        @if ($article->user->isSelf)
          <br>
          @include('articles.partials.edit-button', ['article' => $article])

          @include('articles.partials.delete-button', ['article' => $article])
        @endif
      </div>

    </div>
  </div>

  <div class="container page">

    <div class="row post-content">
      <div class="col-md-9">
      {!! $article->body !!}
      </div>
      <div class="col-md-3 m-t-2">
        <hr>
      <div id="feed-post-preview-light"
          hx-trigger="load"
          hx-get="/htmx/home/light-feed{{ isset(request()->page) ? '?page=' . request()->page : '' }}"
        ></div>
      </div>
    </div>

    <hr />

    <div class="post-actions">
      <div class="post-meta">
        <a href="profile.html"></a>

        @if ($article->user->isSelf)

          @include('articles.partials.edit-button', ['article' => $article])

          @include('articles.partials.delete-button', ['article' => $article])
            
        @else

          @include('articles.partials.favorite-button', [
            'show_text' => true,
            'favorite_count' => $favorite_count,
            'is_favorited' => $is_favorited
          ])

        @endif
      </div>
    </div>

    <div>
      <div class="col-md-9" hx-get="/htmx/articles/{{ $article->slug }}/comments" hx-trigger="load"></div>
    </div>

  </div>

</div>