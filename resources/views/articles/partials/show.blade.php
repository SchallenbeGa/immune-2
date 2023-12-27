<main class="container">
  <div class="alert alert-danger" style="margin-top:10px;" role="alert">
    No financial advice, following content are the result from a <a href="https://fr.wikipedia.org/wiki/Backtesting" target="_blank">backtesting</a> session !
  </div>
  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="margin-top:10px">
    <div class="col-lg-6 px-0">
      <h1 class="display-4 fst-italic">immune-web</h1>
      <p class="lead my-3">Second life of an old buy/sell automat in python.</p>
    </div>
    <!-- <div class="col-lg-7">
    <img class="img_o" width="100%" height="100%" src="/img/robo.png">
    </div> -->
  </div>
  <div class="row">
    <div class="col-md-8">
      <div style="margin-top: 10px"><?php echo $article->body ?></div>
    </div>
    <div class="col-md-4 more">
      <div class="position-sticky" style="top: 2rem;">
        <div class="p-4 mb-3 bg-body-tertiary rounded">
          <h4 class="fst-italic">About</h4>
          <p class="mb-0">Du latin immunis (« libre de charge »).</p>
        </div>
        <div>
          <h4 class="fst-italic">Recent trades</h4>
          <ul class="list-unstyled">
            <div id="trade-post-preview" hx-trigger="load" hx-get="/htmx/home/trade-feed"></div>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="post-page">
    <div class="banner">
      <div class="container">
      </div>
    </div>
    <div class="container page">
      <div class="row post-content">
        <div class="col-md-12">
        </div>
        <div class="col-md-12 m-t-2">
          <ul class="tag-list">
            @foreach ($article->tags as $tag)
            <li class="tag-default tag-pill tag-outline">{{ $tag->name }}</li>
            @endforeach
          </ul>
        </div>
      </div>
      <hr />
      <div class="row">
        <div class="col-md-8 col-md-offset-2" hx-get="/htmx/articles/{{ $article->slug }}/comments" hx-trigger="load"></div>
      </div>
    </div>
  </div>