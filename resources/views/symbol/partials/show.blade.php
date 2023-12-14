<div class="post-page">

  <div class="banner">
    <div class="container" >

      <h1>{{ $symbol->name }}</h1>
      <div id='myDiv'><!-- Plotly chart will be drawn inside this DIV --></div>
      </script>
      <img src="{{ $symbol->graph }}" class="img-fluid" alt="unresolved chart">
      <div class="post-meta">
        <div class="info">
        <span class="date">started at : {{ $symbol->created_at }}</span>
          <span class="date">updated : {{ $symbol->updated_at }}</span>
        </div>


      </div>

    </div>
  </div>

  <div class="container page">

    <div class="row post-content">

    </div>
    <hr />
    <div class="row" hx-get="/htmx/symbol/{{ $symbol->name }}/data" hx-trigger="load">
     
    </div>
  </div>
</div>