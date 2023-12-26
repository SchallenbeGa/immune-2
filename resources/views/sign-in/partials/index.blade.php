<main class="container">

  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="margin-top:10px">
    <div class="col-lg-6 px-0">
      <h1 class="display-4 fst-italic">immune-web</h1>
      <p class="lead my-3">Second life of an old buy/sell automat in python.</p>
    </div>
  </div>
  <div class="justify-content-between align-items-center">
    <div class="auth-page">
      <div class="container page">
        <div class="row justify-content-center">

          <div class="col-md-6 col-md-offset-3 col-xs-12">
            <h1 class="text-xs-center">Sign in</h1>
            <p class="text-xs-center">
              <a href="/sign-up" hx-push-url="/sign-up" hx-get="/htmx/sign-up" hx-target="#app-body">
                Need an account?
              </a>
            </p>

            <div id="sign-in-form-messages"></div>

            <form method="POST" hx-post="/htmx/sign-in" hx-target="#app-body">
              @include('sign-in.partials.form-fields')
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>