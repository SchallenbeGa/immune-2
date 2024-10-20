<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta
  name="description"
  content="gabriel0's simple recipe for maple donuts
           makes a sticky, sweet treat with just a hint
           of salt that you'll keep coming back for.">
    <title>{{ $page_title ?? '' }} gabriel0</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  </head>
  <body hx-ext="head-support">
    <nav class="navbar navbar-light">
      <div class="container">
        <a class="navbar-brand" 
          href="/"
          hx-push-url="/"
          hx-get="/htmx/home" 
          hx-target="#app-body">gabriel0</a>
          
        @include('components.navbar')
      </div>
    </nav>

    <div id="app-body">
      @yield('content')
    </div>
    
    <footer>
      <div class="container">
       immune-web
      </div>
    </footer>

    <div id="htmx-redirect"></div>

    <script src="{{ asset('js/htmx.js') }}"></script>
    <script src="{{ asset('js/htmx-head-support.js') }}"></script>
    <script>
    // Get the session lifetime (in minutes) from your backend and multiply by 60 to convert to seconds.
    var sessionLifetimeInMinutes = {{ config('session.lifetime') }}; // Insert backend value here
    var sessionLifetimeInSeconds = sessionLifetimeInMinutes * 60; // Convert to seconds

    // Refresh the page after the session lifetime expires
    setTimeout(function() {
        window.location.reload(); // Refresh the page
    }, sessionLifetimeInSeconds * 1000); // Convert seconds to milliseconds for setTimeout
</script>
  </body>
</html>