<main class="container">

  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary">
    <div class="col-lg-6 px-0">
      <h1 class="display-4 fst-italic">immune-web</h1>
      <p class="lead my-3">Second life of an old buy/sell automat in python.</p>
    </div>
  </div>
  <div class="p-4 justify-content-between align-items-center">
    <div class="auth-page">
      <div class="container page">
        <div class="row">

          <div class="col-md-6 col-md-offset-3 col-xs-12">
            <h1 class="text-xs-center">Sign up</h1>
            <p class="text-xs-center">
              <a href="/sign-in" hx-push-url="/sign-in" hx-get="/htmx/sign-in" hx-target="#app-body">
                Have an account?
              </a>
            </p>

            <div id="sign-up-form-messages"></div>

            <form method="POST" hx-post="/htmx/sign-up" hx-target="#app-body">
              @include('sign-up.partials.form-fields')
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>