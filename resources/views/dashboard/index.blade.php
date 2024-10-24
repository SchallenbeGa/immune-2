@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tableau de bord des visites</h1>


    <!-- Graphiques -->
    <div class="row" style="margin:1rem;">
        <div class="col">
            <canvas id="browserChart"></canvas>
        </div>
        <div class="col">
            <canvas id="dateChart"></canvas>
        </div>
        <div class="col">
            <canvas id="countryChart"></canvas>
        </div>

        
    </div>
      <!-- Formulaire de filtrage et recherche -->
      <form method="GET" action="{{ route('dashboard.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par IP ou URL" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="browser" class="form-control" placeholder="Filtrer par navigateur" value="{{ request('browser') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="os" class="form-control" placeholder="Filtrer par OS" value="{{ request('os') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="country" class="form-control" placeholder="Filtrer par pays" value="{{ request('country') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3" style="margin:1rem;">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </div>
    </form>

    <!-- Tableau des visites -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>IP</th>
                <th>URL</th>
                <th>Navigateur</th>
                <th>OS</th>
                <th>Pays</th>
                <th>Date de visite</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
                <tr>
                    <td>{{ $visit->id }}</td>
                    <td>{{ $visit->ip_address }}</td>
                    <td>{{ $visit->url }}</td>
                    <td>{{ $visit->browser }}</td>
                    <td>{{ $visit->os }}</td>
                    <td>{{ $visit->country }}</td>
                    <td>{{ $visit->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $visits->links() }}
    </div>
   
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Préparer les données pour les graphiques
    var browserData = {
        labels: {!! json_encode($visitsByBrowser->pluck('user_agent')) !!},
        datasets: [{
            label: 'Nombre de visites par navigateur',
            data: {!! json_encode($visitsByBrowser->pluck('count')) !!},
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    var countryData = {
        labels: {!! json_encode($visitsByCountry->pluck('country')) !!},
        datasets: [{
            label: 'Nombre de visites par pays',
            data: {!! json_encode($visitsByCountry->pluck('count')) !!},
            backgroundColor: 'rgba(153, 102, 255, 0.2)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }]
    };

    var dateData = {
        labels: {!! json_encode($visitsByDate->pluck('date')) !!},
        datasets: [{
            label: 'Nombre de visites par date',
            data: {!! json_encode($visitsByDate->pluck('count')) !!},
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1
        }]
    };

    // Initialisation des graphiques
    var ctxBrowser = document.getElementById('browserChart').getContext('2d');
    new Chart(ctxBrowser, {
        type: 'bar',
        data: browserData,
    });

    var ctxCountry = document.getElementById('countryChart').getContext('2d');
    new Chart(ctxCountry, {
        type: 'pie',
        data: countryData,
    });

    var ctxDate = document.getElementById('dateChart').getContext('2d');
    new Chart(ctxDate, {
        type: 'line',
        data: dateData,
    });
</script>
@endsection
