@extends('layouts.app')

@section('content')
<div class="container">
@if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulaire pour exécuter la commande Git Pull Update -->
    <form action="{{ route('git.pull.update') }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-primary">Mettre à jour le projet (Git Pull)</button>
    </form>
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
    @auth
    <h2 class="mt-5">Logs</h2>
    <textarea class="form-control" rows="10" readonly>
        {{ implode("\n", $logs) }}
    </textarea>
    @endauth
</div>
@endsection
