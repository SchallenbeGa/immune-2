<div class="home-page">
  <div class="banner">
    <div class="container">
     
    </div>
  </div>
  <link href="https://unpkg.com/tabulator-tables@5.3.4/dist/css/tabulator.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="https://unpkg.com/luxon@2.3.0/build/global/luxon.min.js"></script>
  <script src="https://unpkg.com/tabulator-tables@5.3.4/dist/js/tabulator.min.js"></script>
  <div class="container page">
    <div class="">

      <div class="col" id="main">
        
      <div id="qr-reader"></div>
    <button id="start-scan">Start Scan</button>

    <div id="computer-details">
        <h2>Computer Details:</h2>
        <p id="details"></p>
    </div>

        @include('home.partials.form-message')
        @include('home.partials.import')
        <!-- <div id="inventory-preview-light"
          hx-trigger="load"
      hx-get="/htmx/inventory/list{{ isset(request()->page) ? '?page=' . request()->page : '' }}"
       
        ></div>
       -->
       <button id="exportBtn">Export to CSV</button>
       <button id="printBtn">Print All QR Codes</button>
       <div id="qr-codes-container" style="display:none;"></div>

       <div id="computer-table"></div>
       <div id="qr-codes"></div>
    </div>
  </div>
</div>


<script>
   document.getElementById("start-scan").addEventListener("click", function() {
    // Start the QR code scanner
    let qrCodeScanner = new Html5Qrcode("qr-reader");
    
    qrCodeScanner.start(
        { facingMode: "environment" }, // Use the back camera for better results
        {
            fps: 10, // Frames per second
            qrbox: 250 // Size of the scanning box
        },
        qrCodeMessage => {
            // When a QR code is scanned, stop the scanner
            qrCodeScanner.stop().then(() => {
                // The QR code contains the URL
                console.log("Scanned QR Code:", qrCodeMessage);
                fetchComputerDetails(qrCodeMessage);
            }).catch(err => {
                console.error("Failed to stop the scanner", err);
            });
        },
        errorMessage => {
            console.log("QR scanning error:", errorMessage);
        }
    ).catch(err => {
        console.error("Unable to start scanning", err);
    });
});

// Function to fetch computer details from the URL in the QR code
function fetchComputerDetails(computerUrl) {
    fetch(computerUrl+"/json")
        .then(response => response.json())
        .then(data => {
            displayComputerDetails(data);
        })
        .catch(error => {
            console.error("Error fetching computer details:", error);
            document.getElementById("details").textContent = "Failed to load computer details.";
        });
}

// Function to display computer details on the page
function displayComputerDetails(data) {
    const details = `
        <strong>PC Name:</strong> ${data.reference} <br>
        <strong>Employee:</strong> ${data.employee.name} <br>
        <strong>Added on:</strong> ${data.created_at} <br>
        <strong>Last Updated:</strong> ${data.updated_at}
    `;
    document.getElementById("details").innerHTML = details;
}

    var table = new Tabulator("#computer-table", {
        ajaxURL: "{{ route('computers.json') }}", // URL pour charger les données JSON
        height: "500px", // Hauteur du tableau
        layout: "fitColumns", // Ajuster les colonnes à la largeur du tableau
        pagination: "local", // Activer la pagination
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