<div class="post-page">
  <div class="banner">
    <div class="container">

      <h1>{{ $symbol->name }}</h1>
      <div id='prev'></div>
      </script>
      <div class="post-meta">
        <div class="info">
          <span class="date">started at : {{ $symbol->created_at }}</span>
          <span class="date">updated : {{ $symbol->updated_at }}</span>
        </div>

        <script>
          var data_p = <?php echo json_encode($oh) ?>;
          var trace1 = {

            x: data_p["x"],

            close: data_p["close"],

            decreasing: {
              line: {
                color: '#FF0000'
              }
            },

            high: data_p["high"],

            increasing: {
              line: {
                color: '#008000'
              }
            },

            line: {
              color: 'rgba(31,119,180,1)'
            },

            low: data_p["low"],

            open: data_p["open"],

            type: 'candlestick',
            xaxis: 'x',
            yaxis: 'y'
          };

          var data = [trace1];

          var layout = {
            dragmode: 'zoom',
            margin: {
              r: 10,
              t: 25,
              b: 40,
              l: 60
            },
            showlegend: false,
            xaxis: {
              autorange: true,
              domain: [0, 1],
              range: [data_p["x"].at(0), data_p["x"].pop()],
              rangeslider: {
                range: [data_p["x"].at(0), data_p["x"].pop()]
              },
              title: 'Date',
              type: 'date'
            },
            yaxis: {
              autorange: true,
              domain: [0, 1],
              range: [114.609999778, 137.410004222],
              type: 'linear'
            },
          };

          Plotly.newPlot('prev', data, layout,{responsive: true});
        </script>
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