<div class="card">
  <div class="card-block">
    <p style="margin:10px" class="card-text">{{ $comment->created_at->format('F jS') }} - Random : {{ $comment->body }}</p>
  </div>
</div>