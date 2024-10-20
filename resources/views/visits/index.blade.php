<!-- resources/views/visits/index.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Liste des Visites</h1>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Adresse IP</th>
                <th>URL Visitée</th>
                <th>Méthode HTTP</th>
                <th>User-Agent</th>
                <th>Référent</th>
                <th>Date de Visite</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
                <tr>
                    <td>{{ $visit->id }}</td>
                    <td>{{ $visit->ip_address }}</td>
                    <td>{{ $visit->url }}</td>
                    <td>{{ $visit->http_method }}</td>
                    <td>{{ $visit->user_agent }}</td>
                    <td>{{ $visit->referer ?? 'N/A' }}</td>
                    <td>{{ $visit->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
