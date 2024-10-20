@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Historique des Commandes</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Commande</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $history)
                <tr>
                    <td>{{ $history->id }}</td>
                    <td>{{ $history->command }}</td>
                    <td>
                        @if($history->success)
                            <span class="badge badge-success">Réussie</span>
                        @else
                            <span class="badge badge-danger">Échouée</span>
                        @endif
                    </td>
                    <td>{{ $history->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <a href="{{ route('command.history.show', $history) }}" class="btn btn-info btn-sm">Détails</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $histories->links() }}
    </div>

    <h2 class="mt-5">Logs d'Erreur</h2>
    <div class="card">
        <div class="card-body">
            <textarea>{{ implode("\n", $logs) }}</textarea>
        </div>
    </div>
</div>
@endsection
