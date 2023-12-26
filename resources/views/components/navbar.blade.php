<header class="border-bottom lh-1 py-3">
  <div class="row flex-nowrap justify-content-between align-items-center">
    <div class="col pt-1 sub">

      <a class="link-secondary" target="_blank" href="https://github.com/schallenbega">Subscribe</a>
    </div>
    <div class="col text-center logo">
      <a id="nav-link-home" href="/" hx-get="/htmx/home" hx-target="#app-body" hx-push-url="/" class="blog-header-logo text-body-emphasis text-decoration-none">
        Home
      </a>
    </div>
    <div class="col d-flex justify-content-end align-items-center" hx-swap-oob="true">


      @guest
      <a id="nav-link-sign-in" href="/sign-in" hx-get="/htmx/sign-in" hx-target="#app-body" hx-push-url="/sign-in" style="margin-right:1rem" class="btn btn-sm btn-outline-secondary">
        Sign in
      </a>

      <a id="nav-link-sign-up" href="/sign-up" hx-get="/htmx/sign-up" hx-target="#app-body" hx-push-url="/sign-up" class="btn btn-sm btn-outline-secondary">
        Sign up
      </a>
      @endguest

      @auth

      <!-- <a id="nav-link-editor" href="/editor" hx-get="/htmx/editor" hx-target="#app-body" hx-push-url="/editor" class="btn btn-sm btn-outline-secondary nav-link">
        New Article
      </a> -->

      <a id="nav-link-settings" href="/settings" hx-get="/htmx/settings" hx-target="#app-body" style="padding:10px" hx-push-url="/settings" class="btn btn-sm btn-outline-secondary nav-link">
        Settings
      </a>

      <a id="nav-link-profile" style="width: 100%;padding:10px;margin:10px" href="/users/{{ auth()->user()->username }}" hx-get="/htmx/users/{{ auth()->user()->username }}" hx-target="#app-body" hx-push-url="/users/{{ auth()->user()->username }}" class="btn btn-sm btn-outline-secondary nav-link">
        <img width="25px" height="25px" style="border-radius: 50%;" class="user-pic" src="{{ auth()->user()->image }}">
        {{ auth()->user()->name ?? auth()->user()->username }}
      </a>
      <form method="get" action="/logout">
        @csrf
        <button class="btn btn-sm action-btn btn-outline-danger" style="margin:10px">
          out
        </button>
      </form>
      @endauth
    </div>
  </div>
</header>