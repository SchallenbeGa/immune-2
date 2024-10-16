@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<div class="home-page">
  <div class="banner">
    <div class="container">
     
    </div>
  </div>
  <div class="container page">
    <div class="">

      <div class="col" id="main">
<h1>Computer Details</h1>

<!-- Affichage des détails de l'ordinateur -->
<div>
    <h2>ID: {{ $computer->id }}</h2>
    <p>Reference: {{ $computer->reference }}</p>
    <p>User: {{ $computer->employee->name}}</p>
    <p>Details URL: {{ $computer->detailUrl }}</p>
</div>
<hr>
  
@auth
  
@foreach($employee_history as $entry)
<div>
    <h2>Old user : {{ $entry['employee'] }}</h2>
    <p>Details URL: {{ $entry['assigned_at'] }}</p>
</div>
@endforeach
@endauth
<!-- Affichage du QR code correspondant à l'URL de l'ordinateur -->
<div id="qr-code-container"></div>

<script>
    // Générer le QR code en utilisant le detailsUrl de l'ordinateur
    const qrCodeContainer = document.getElementById('qr-code-container');
    new QRCode(qrCodeContainer, {
        text: "{{ $computer->detailsUrl }}",  // Utiliser l'URL de détails de l'ordinateur
        width: 200,
        height: 200
    });
</script>
</div>
</div>
@endsection

  