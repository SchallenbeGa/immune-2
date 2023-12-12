<div class="profile-page">
  <div class="user-info">
    <div class="container">
      <div class="row">

        <div class="col-md-10 col-md-offset-1">
          <img src="{{ $user->image }}" class="user-img" />
          <h4>{{ $user->name }}</h4>
          <p>{{ $user->bio }}</p>

          @if ($user->is_self)
            <a class="btn btn-sm btn-outline-secondary action-btn"
              href="/settings"
              hx-push-url="/settings"
              hx-get="/htmx/settings"
              hx-target="#app-body"
            >
              <i class="ion-ios-gear"></i>
              &nbsp;
              Edit Profile Settings</span>
            </a>
          @else
            @include('users.partials.follow-button')
          @endif
        </div>

      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-18 col-md-offset-1">
        <div id="user-post-preview"
            hx-get="/htmx/users/{{ $user->username }}/favorites"
            hx-trigger="every 1s"
        ></div>
      </div>
    </div>
  </div>
</div>