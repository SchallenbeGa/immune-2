<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @stack('meta')
    <title>{{ $page_title ?? '' }} immune-web</title>
    <meta name="description" content="portfolio">
    <meta name="keywords" content="portfolio">
    <meta name="author" content="immune">
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

  </head>
  <body hx-ext="head-support">
    <nav class="navbar navbar-light">
      <div class="container">
        <a class="navbar-brand" 
          href="/"
          hx-push-url="/"
          hx-get="/htmx/home" 
          hx-target="#app-body">immune-web</a>
          
        @include('components.navbar')
      </div>
    </nav>

    <div id="app-body">
      @yield('content')
    </div>
    
    <footer>
      <div class="container">
        <a href="/" class="logo-font">immune-web</a>
        <span class="attribution">
         
        </span>
      </div>
    </footer>

    <div id="htmx-redirect"></div>

    <script src="{{ asset('js/htmx.js') }}"></script>
    <script src="{{ asset('js/htmx-head-support.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script>
      var isTagify = null;

      document.body.addEventListener('htmx:configRequest', function(evt) {
        evt.detail.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
      })

      window.addEventListener('DOMContentLoaded', function() {
        renderTagify();
      });

      document.body.addEventListener("htmx:afterSwap", function(evt) {
        renderTagify();
      });

      function renderTagify() {
        const input = document.querySelector('input[name=tags]');
        const tagify = document.querySelector('tags[class="tagify  form-control tagify--outside"]');

        if (input && !tagify) {
          new Tagify(input, {
            whitelist: [],
            dropdown: {
              position: "input",
              enabled : 0 // always opens dropdown when input gets focus
            }
          })
        }
      }
    </script>
  </body>
</html>