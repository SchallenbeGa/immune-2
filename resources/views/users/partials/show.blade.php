<main class="container">
<div class="alert alert-danger" style="margin-top:10px;" role="alert">
  No financial advice, following content are the result from a <a href="https://fr.wikipedia.org/wiki/Backtesting" target="_blank">backtesting</a> session !
</div>
  <div class="p-4 p-md-5 mb-4 rounded text-body-emphasis bg-body-secondary" style="margin-top:10px">
    <div class="col">
      <h1 class="display-4 fst-italic">
        <p>Hello {{ $user->name }}</p>
        <p>{{ $user->bio }}</p>
      </h1>
      <p class="lead my-3">This is your homepage</p>
    </div>
    @if ($user->is_self)
    <a class="btn btn-sm btn-outline-secondary action-btn" href="/settings" style="margin:10px" hx-push-url="/settings" hx-get="/htmx/settings" hx-target="#app-body">
      Edit Profile Settings</span>
    </a>
    @else
    @include('users.partials.follow-button')
    @endif
  </div>

  <div class="row g-5">
    <div class="col-md-12">
      <div id="feed-post-preview" hx-get="/htmx/users/{{ $user->username }}/favorites" hx-trigger="every 1s"></div>
    </div>
  </div>
  </div>
</main>