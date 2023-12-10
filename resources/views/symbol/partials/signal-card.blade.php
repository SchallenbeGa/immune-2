<div class="card">
  <div class="card-block">
    <p class="card-text">{{ $entry->msg }}</p>
  </div>
  <div class="card-footer">
    <span class="date-posted">{{ $entry->created_at->format('F jS') }}</span>
  </div>
</div>