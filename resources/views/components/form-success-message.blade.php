@if (isset($message) && $message)
  <div class="alert alert-warning" id="alert" role="alert">
  <script>
    $(document).ready(function () {
      $("#alert").fadeTo(2000, 500).slideUp(500, function() {
      $("#alert").slideUp(500);
    });
});
   
  </script>
    <ul>
      <li>{{ $message }}</li>
    </ul>
  </div>
@endif