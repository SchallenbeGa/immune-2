<div id="form-message"></div>

<form id="article-comment-form" hx-post="/htmx/articles/{{ $article->slug }}/comments" hx-target="#article-comments-wrapper" hx-swap="afterbegin show:top" @if (isset($oob_swap)) hx-swap-oob="true" @endif>
  @csrf

  <textarea class="form-control" style="margin-top:10px" placeholder="Write a comment..." rows="3" name="comment"></textarea>
  <button class="btn btn-sm btn-primary" style="margin-top:10px">
    Post Comment
  </button>
</form>