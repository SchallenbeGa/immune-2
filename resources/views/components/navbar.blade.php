<ul id="navbar" class="nav navbar-nav pull-xs-right" hx-swap-oob="true">
  <li class="nav-item">
    <a id="nav-link-home"
    aria-label="homepage"
      href="/"
      hx-get="/htmx/home"
      hx-target="#app-body"
      hx-push-url="/"
      class="nav-link @if (!isset($navbar_active) || $navbar_active == 'home') active @endif" 
    >
      home
    </a>
  </li>
  <li class="nav-item">
  <a class="nav-link @if (isset($navbar_active) && $navbar_active == 'analyse') active @endif" href="{{ route('analyse.index') }}">analyse</a>
  </li>
  @guest
  <li class="nav-item">
    <a id="nav-link-sign-in"
    aria-label="signin page"
      href="/sign-in"
      hx-get="/htmx/sign-in"
      hx-target="#app-body"
      hx-push-url="/sign-in"
      class="nav-link @if (isset($navbar_active) && $navbar_active == 'sign-in') active @endif" 
    >
      sign in
    </a>
  </li>
  <li class="nav-item">
    <a id="nav-link-sign-up"
    aria-label="signup page"
      href="/sign-up"
      hx-get="/htmx/sign-up"
      hx-target="#app-body"
      hx-push-url="/sign-up"
      class="nav-link @if (isset($navbar_active) && $navbar_active == 'sign-up') active @endif" 
    >
      sign up
    </a>
  </li>
  @endguest
  
  @auth
  @if (auth()->user()->role>2)
    <li class="nav-item">
    <a id="nav-link-editor"
    aria-label="new article page"
      href="/editor"
      hx-get="/htmx/editor"
      hx-target="#app-body"
      hx-push-url="/editor"
      class="nav-link @if (isset($navbar_active) && $navbar_active == 'editor') active @endif"
    >
      <i class="ion-compose"></i>
      new article
    </a>
  </li>
  @endif

  <!-- <li class="nav-item">
    <a id="nav-link-settings"
    aria-label="user's setting page"
      href="/settings"
      hx-get="/htmx/settings"
      hx-target="#app-body"
      hx-push-url="/settings"
      class="nav-link @if (isset($navbar_active) && $navbar_active == 'settings') active @endif"
    >
      settings
    </a>
  </li> -->
  <!-- <li class="nav-item">
    <a id="nav-link-profile"
      href="/users/{{ auth()->user()->username }}"
      hx-get="/htmx/users/{{ auth()->user()->username }}"
      hx-target="#app-body"
      hx-push-url="/users/{{ auth()->user()->username }}"
      class="nav-link @if (isset($navbar_active) && $navbar_active == 'profile') active @endif"
    >
      profile
    </a>
  </li> -->
  <li class="nav-item">
    <div class="col-md-4 col-md-offset-3">
        <button class="btn btn-outline-danger" hx-post="/htmx/logout">
          logout
        </button>
      </div>
</li>
  @endauth
</ul>