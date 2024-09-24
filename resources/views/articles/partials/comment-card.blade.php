<div class="card">
  <div class="card-block">
    <p class="card-text">{{ $comment->body }}</p>
  </div>
  <div class="card-footer">
   
    &nbsp;
   
    <span class="date-posted">{{ $comment->created_at->format('F jS') }}</span>
  </div>
</div>