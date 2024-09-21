<div class="card" style="margin-top:1rem">
  <div class="card-block">
    <p style="margin:0.4rem" class="card-text">{{ $comment->created_at->format('F jS') }} - Random : {{ $comment->body }}</p>
  </div>
</div>