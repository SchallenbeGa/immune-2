<div id="trade-post-preview" hx-swap-oob="true">
  @forelse ($trades as $entry)
  <li style="
  background: rgba(var(--bs-tertiary-bg-rgb),var(--bs-bg-opacity))!important;
  box-shadow: 6px 0px 0px {{ $entry->side === 'BUY' ? '#77DD77' : '#FF6961' }};
  outline: transparent;
  position: relative;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;">
    <a class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center py-3 link-body-emphasis text-decoration-none border-top" href="/symbol/{{ $entry->name }}" hx-push-url="/symbol/{{ $entry->name }}" hx-get="/htmx/symbol/{{ $entry->name }}" hx-target="#app-body" class="preview-link">
      
      <div class="col-lg-8">
        <h5 class="mb-0">{{ $entry->name }} - {{ $entry->side}} at {{ $entry->price}}</h5>
        <small class="text-body-secondary">{{ $entry->updated_at }}</small>
      </div>
    </a>
  </li>
  @empty
  <div class="post-preview">
    <div class="alert alert-warning" role="alert">
      No trades yet...
    </div>
  </div>
  @endforelse
</div>