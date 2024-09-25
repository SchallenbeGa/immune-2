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
    
    <style>
      .tagify--outside{
        border: 0;
      }

      .tagify--outside .tagify__input{
        order: -1;
        flex: 100%;
        border: 1px solid var(--tags-border-color);
        margin-bottom: 1em;
        transition: .1s;
      }

      .tagify--outside .tagify__input:hover{ border-color:var(--tags-hover-border-color); }
      .tagify--outside.tagify--focus .tagify__input{
        transition:0s;
        border-color: var(--tags-focus-border-color);
      }

      .tagify__input { border-radius: 4px; margin: 0; padding: 10px 12px; }
    </style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }}"> -->
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
   
  </body>
</html>