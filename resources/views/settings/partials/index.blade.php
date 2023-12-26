<div class="settings-page">
  <div class="container page">
    <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="margin-top:10px">
      <div class="col-lg-6 px-0">
        <h1 class="display-4 fst-italic">immune-web</h1>
        <p class="lead my-3">Second life of an old buy/sell automat in python.</p>
      </div>
    </div>
    @include('settings.partials.form-message')

    @include('settings.partials.form')
    <hr>
    <form hx-post="/htmx/logout">
      @csrf
      <button class="btn btn-outline-danger" style="margin:10px">
        Logout
      </button>
    </form>
  </div>
</div>
</div>