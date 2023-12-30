<div class="post-page">
  <div class="banner">
    <div class="container">
<div class="alert alert-danger" style="margin-top:10px;" role="alert">
  No financial advice, following content are the result from a <a href="https://fr.wikipedia.org/wiki/Backtesting" target="_blank">backtesting</a> session !
</div>
      <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="margin-top:10px">
        <div class="col px-0">
          <h1 class="display-4 fst-italic">{{ $symbol->name }}</h1>
          <span class="date">started at : {{ $symbol->created_at }}</span>
          <br>
          <span class="date">updated : {{ $symbol->updated_at }}</span>
          <div id='prev'></div>
        </div>
      </div>
      <div class="post-meta">
        <div class="info">
          
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
            type: 'ohlc',
            xaxis: 'x',
            yaxis: 'y',
          };
          var trace2 = {
            x: data_p["date_buy"],
            y: data_p["buy"],
            mode: 'markers',
            marker: {
              symbol: "triangle-up",
              size: 15,
              standoff:10,
              color: 'green'
            }
          };
          var trace3 = {
            x: data_p["date_sell"],
            y: data_p["sell"],
            mode: 'markers',
            marker: {
              symbol: "triangle-down",
              size: 15,
              color: 'red'
            }
          };

          var data = [trace1,trace2,trace3];

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
    
              range: [data_p["x"].at(0), data_p["x"].pop()],
              rangeslider: {
                range: [data_p["x"].at(0), data_p["x"].pop()]
              },
              title: 'Date',
              type: 'date'
            },
            yaxis: {
              autorange:"min",
              title:'Price',
              type: 'linear'
            },
          };

          Plotly.newPlot('prev', data, layout, {
            responsive: true
          });
        </script>
      </div>

    </div>
  </div>

  <div class="container page">

    <div class="row post-content">

    </div>
    <hr style="border: 10px solid;border-radius: 5px;">
    <div class="row" hx-get="/htmx/symbol/{{ $symbol->name }}/data" hx-trigger="load">

    </div>
  </div>
</div>