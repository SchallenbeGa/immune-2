<div class="card">
  <div class="card-block">
    <p class="card-text">{{ $entry->price }}</p>
    <p class="card-text">{{ $entry->side }}</p>
  </div>
  <div class="card-footer">
    <span class="date-posted">{{ $entry->created_at->format('F jS') }}</span>
  </div>
</div>