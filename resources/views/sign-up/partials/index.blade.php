<div class="auth-page">
  <div class="container page">
    <div class="row">

      <div class="col-md-6 col-md-offset-3 col-xs-12">
        <h1 class="text-xs-center">Sign up</h1>
        <p class="text-xs-center">
          <a 
            href="/sign-in"
            hx-push-url="/sign-in"
            hx-indicator="#spinner" hx-get="/htmx/sign-in" 
            hx-target="#app-body"
          >
            Have an account?
          </a>
        </p>

        <div id="sign-up-form-messages"></div>

        <form method="POST" hx-post="/htmx/sign-up" hx-indicator="#spinner" hx-target="#app-body">
          @include('sign-up.partials.form-fields')
        </form>
      </div>

    </div>
  </div>
</div>