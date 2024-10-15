<ul id="navbar" class="nav navbar-nav pull-xs-right" hx-swap-oob="true">
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
</ul>