<div class="card">
  <div class="card-block">
    <p class="card-text">{{ $entry->open }} - {{ $entry->close }}</p>
  </div>
  <div class="card-footer">
      {{ $entry->volume }} 
    <span class="date-posted">{{ $entry->created_at->format('F jS') }}</span>
  </div>
</div>