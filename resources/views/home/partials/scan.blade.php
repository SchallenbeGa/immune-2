<div class="home-page">
  <div class="banner">
    <div class="container">
     
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
  <div class="container page">
    <div class="">

      <div class="col" id="main">
        
      <div id="qr-reader"></div>
    <button class="btn btn-lg btn-primary pull-xs-right" id="start-scan">Start Scan</button>

    <div id="computer-details">
        <h2>Computer Details:</h2>
        <p id="details"></p>
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

</script>