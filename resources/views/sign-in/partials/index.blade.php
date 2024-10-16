<div class="auth-page">
  <div class="container page">
    <div class="row">

      <div class="col-md-6 col-md-offset-3 col-xs-12">
        <h1 class="text-xs-center">Login</h1>
        <div id="sign-in-form-messages"></div>

        <form method="POST" hx-post="/htmx/sign-in" hx-target="#app-body">
          @include('sign-in.partials.form-fields')
        </form>
      </div>

    </div>
  </div>
</div>