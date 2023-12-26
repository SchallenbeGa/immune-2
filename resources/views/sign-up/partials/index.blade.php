<main class="container">
    <div class="alert alert-danger" style="margin-top:10px;" role="alert">
  No financial advice, following content are the result from a <a href="https://fr.wikipedia.org/wiki/Backtesting" target="_blank">backtesting</a> session !
</div>
  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="margin-top:10px">
    <div class="col-lg-6 px-0">
      <h1 class="display-4 fst-italic">immune-web</h1>
      <p class="lead my-3">Second life of an old buy/sell automat in python.</p>
    </div>
  </div>
  <div class="justify-content-between align-items-center">
    <div class="auth-page">
      <div class="container page">
        <div class="row g-5">
          <div class="col-md-8">
            <h1 class="text-xs-center">Sign up</h1>
            <p class="text-xs-center">
              <a href="/sign-in" hx-push-url="/sign-in" hx-get="/htmx/sign-in" hx-target="#app-body">
                Have an account?
              </a>
            </p>
            <div id="sign-up-form-messages"></div>
            <form method="POST" target="/htmx/sign-up" hx-target="#app-body">
              @include('sign-up.partials.form-fields')
            </form>
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
      </div>
    </div>
  </div>
</main>