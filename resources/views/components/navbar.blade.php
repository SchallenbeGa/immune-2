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
</ul>