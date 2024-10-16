<div class="home-page">
  <div class="banner">
    <div class="container">
     
    </div>
  </div>
 
  <div class="container page">
    <div class="">

      <div class="col" id="main">
        
  

        @include('home.partials.form-message')
        @include('home.partials.import')
        <hr>
        <br>
        <!-- <div id="inventory-preview-light"
          hx-trigger="load"
      hx-get="/htmx/inventory/list{{ isset(request()->page) ? '?page=' . request()->page : '' }}"
       
        ></div>
       -->
       <button id="exportBtn">Export to CSV</button>
       <button id="printBtn">Print All QR Codes</button>
       <div id="qr-codes-container" style="display:none;"></div>
       <button id="get-data-btn">Générer QR Codes</button>
       <div id="qr-container"></div>

       <div id="computer-table"></div>
       <div id="qr-codes"></div>
    </div>
  </div>
</div>


<script>
  
    var table = new Tabulator("#computer-table", {
        ajaxURL: "{{ route('computers.json') }}", // URL pour charger les données JSON
        height: "500px", // Hauteur du tableau
        layout: "fitColumns", // Ajuster les colonnes à la largeur du tableau
        paginationSize: 10, // Nombre de lignes par page
        columns: [
            {title: "Référence de la machine", field: "reference", sorter: "string", width: 150},
            {title: "Utilisateur", field: "employee.name", sorter: "string", width: 150},
            {title: "Date d'ajout", field: "created_at", sorter: "date"},
            {title: "Dernière mise à jour", field: "updated_at", sorter: "date"},
            {
                    title: 'QR Code', 
                    field: 'detailUrl',
                    formatter: function(cell, formatterParams, onRendered){
                        // Générer un QR code pour chaque ordinateur avec l'URL des détails
                        const qrCodeContainer = document.createElement('a');
                        const qrCode = new QRCode(qrCodeContainer, {
                            text: cell.getValue(),
                            width: 100,
                            height: 100
                        });
                        qrCodeContainer.style.cursor = 'pointer';
                        qrCodeContainer.addEventListener('click', function() {
                            window.location.href = cell.getValue();  // Redirige vers l'URL du QR code
                        });
                        return qrCodeContainer; // Afficher le QR code dans la colonne
                    }
                }
        ]
    });
    function generateQRCode(url, callback) {
   
        }

        // Bouton pour récupérer les données et générer les QR codes
        document.getElementById("get-data-btn").addEventListener("click", function() {
            var data = table.getData(); // Récupérer les données du tableau

            // Vider le conteneur de QR codes avant d'ajouter les nouveaux
            var qrContainer = document.getElementById("qr-container");
            qrContainer.innerHTML = "";

            // Parcourir chaque ligne de données et générer un QR code pour chaque URL
            data.forEach(function(row) {
                var url = row.url; // Récupérer l'URL

                // Générer le QR code pour chaque URL
                const qrCodeContainer = document.createElement('a');
                        const qrCode = new QRCode(qrCodeContainer, {
                            text: cell.getValue(),
                            width: 100,
                            height: 100
                        });
                generateQRCode(url, function(qrCodeBase64) {
                    // Créer un élément <img> pour afficher le QR code
                    var img = document.createElement("img");
                    img.src = qrCodeBase64;

                    // Ajouter l'image au conteneur
                    qrContainer.appendChild(img);
                });
            });
        });
    // Fonction pour imprimer tous les QR codes
    document.getElementById('printBtn').addEventListener('click', function() {
            const qrContainer = document.getElementById('qr-codes-container');
            const images = document.querySelectorAll('img');
            // Ouvrir une nouvelle fenêtre pour imprimer les QR codes
            const printWindow = window.open('', '_blank', 'width=600,height=600');
            printWindow.document.write('<html><head><title>Print QR Codes</title></head><body>');
            printWindow.document.write('<h2>All QR Codes</h2>');
            
            images.forEach(function(image) {
              console.log(image);
              img = image.cloneNode();
                img.height = "100";
                img.width="100";
                printWindow.document.write(img.outerHTML); 
                printWindow.document.write('<br><br>'); 
                printWindow.document.write('<p>'+image.parentElement.title+'</p>');
                printWindow.document.write('<hr>')
            });
          
            printWindow.document.write('</body></html>');
            printWindow.document.close();  // Fermer la fenêtre pour permettre le rendu
            printWindow.print();  // Lancer l'impression
        });
    // Fonction pour exporter les données en CSV
    document.getElementById('exportBtn').addEventListener('click', function() {
            table.download('csv', 'computers_list.csv'); // Exporter les données en CSV
        });
    function refreshTableData() {
    // Appeler l'URL de l'API pour obtenir de nouvelles données
    fetch('{{ route('computers.json') }}')
        .then(response => response.json())
        .then(data => {
            // Remplacer les données existantes dans le tableau par les nouvelles données
            table.replaceData(data);
        });
}
</script>