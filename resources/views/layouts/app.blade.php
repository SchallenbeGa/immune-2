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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="https://unpkg.com/tabulator-tables@5.3.4/dist/js/tabulator.min.js"></script>
  <script src="{{ asset('js/htmx.js') }}"></script>
  <script src="{{ asset('js/htmx-head-support.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
    /* Spinner */
#spinne {
    display:none;
    border: 16px solid #f3f3f3; /* Light gray */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 2s linear infinite;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000; /* S'assurer qu'il est au-dessus du reste */
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
/* Style de base pour le menu */
.dropdown-menu {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    padding: 10px;
    position: absolute;
    top: 50px; /* Ajuste selon la position de ton bouton */
    left: 0;
    z-index: 1000;
    width: 200px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}

/* Ajuste les boutons à l'intérieur du menu */
.dropdown-menu button {
    width: 100%;
    margin-bottom: 10px;
}

/* Cache le menu lorsqu'il est masqué */
.dropdown-menu.hidden {
    display: none;
}

</style>
  </head>
  <body hx-ext="head-support">
    <nav class="navbar navbar-light">
      <div class="container">
        <a class="navbar-brand" 
          href="/">inventory0 <img id="spinner" style="height: 16px;" class="htmx-indicator" src="/assets/images/audio.svg"/></a>
         
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
   
  </body>
 
</html>