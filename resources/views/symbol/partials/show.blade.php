<div class="post-page">

  <div class="banner">
    <div class="container" >

      <h1>{{ $symbol->name }}</h1>
      <img src="{{ $symbol->graph }}" class="img-fluid" alt="unresolved chart">
      <div class="post-meta">
        <div class="info">

            {{ $symbol->name }}
          </a>
          <span class="date">{{ $symbol->created_at->format('F jS') }}</span>
        </div>


      </div>

    </div>
  </div>

  <div class="container page">

    <div class="row post-content">
      <div class="col-md-12">
        {{ $symbol->name }}
      </div>

    </div>
    <hr />
    <div class="row">
      <div class="col-md-12" hx-get="/htmx/symbol/{{ $symbol->name }}/data" hx-trigger="load"></div>
    </div>
  </div>
</div>