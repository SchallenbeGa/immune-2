@if (isset($message) && $message)
  <div class="alert alert-warning" id="alert">
  <span class="closebtn" onclick="closeAlert()">&times;</span> {{ $message }}</li>
  </div>
@endif
<script>function closeAlert() {
            var alertElement = document.getElementById("alert");
            alertElement.style.display = "none"; 
        }
        function autoCloseAlert() {
            var alertElement = document.getElementById("alert");
            if(alertElement){
              setTimeout(function() {
                  alertElement.style.display = "none";  // Masquer l'alerte après 2 secondes
              }, 2000);  // 2000 ms = 2 secondes
            }
        }

        // Appeler la fonction pour fermer l'alerte automatiquement après 2 secondes
        autoCloseAlert();</script>