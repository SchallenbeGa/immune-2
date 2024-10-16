<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }}">
    <meta
  name="description"
  content="inventory's simple recipe for maple donuts
           makes a sticky, sweet treat with just a hint
           of salt that you'll keep coming back for.">
    <title>{{ $page_title ?? '' }}</title>
    <link href="https://unpkg.com/tabulator-tables@5.3.4/dist/css/tabulator.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="https://unpkg.com/luxon@2.3.0/build/global/luxon.min.js"></script>
  <script src="https://unpkg.com/tabulator-tables@5.3.4/dist/js/tabulator.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  </head>
  <body hx-ext="head-support">
    <nav class="navbar navbar-light">
      <div class="container">
        <a class="navbar-brand" 
          href="/">inventory0</a>
          
        @include('components.navbar')
      </div>
    </nav>

    <div id="app-body">
      @yield('content')
    </div>
    
    <footer>
      <div class="container">
       inventory0 - 2024
      </div>
    </footer>

    <div id="htmx-redirect"></div>

    <script src="{{ asset('js/htmx.js') }}"></script>
    <script src="{{ asset('js/htmx-head-support.js') }}"></script>
   
  </body>
</html>