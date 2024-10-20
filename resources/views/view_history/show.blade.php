@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails de la Commande</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Commande : {{ $commandHistory->command }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Date : {{ $commandHistory->created_at->format('d/m/Y H:i:s') }}</h6>
            <p class="card-text">Sortie de la commande :</p>
            <pre>{{ $commandHistory->output }}</pre>
        </div>
    </div>

    <a href="{{ route('command.history.index') }}" class="btn btn-primary mt-3">Retour à l'historique</a>
</div>
@endsection
