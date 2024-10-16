<ul id="navbar" class="nav navbar-nav pull-xs-right" hx-swap-oob="true">
  
  @auth
  
  <li class="nav-item">
    <a id="nav-link-home"
    aria-label="homepage"
      href="/"
      class="nav-link @if (!isset($navbar_active) || $navbar_active == 'home') active @endif" 
    >
      home
    </a>
  </li>
  <li class="nav-item">
    <a id="nav-link-home"
    aria-label="homepage"
      href="/scan"
      class="nav-link @if (!isset($navbar_active) || $navbar_active == 'home') active @endif" 
    >
      scan
    </a>
  </li>
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
        <a class="btn btn-outline-danger" href="/logout">
          logout
</a>
      </div>
</li>
  @endauth
 
</ul>