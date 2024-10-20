@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails du site : {{ $site->name }}</h1>

    <div class="mb-3">
        <p><strong>URL :</strong> <a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a></p>
        <p><strong>Statut actuel :</strong> 
            @if ($site->status)
                <span class="text-success">En ligne</span>
            @else
                <span class="text-danger">Hors ligne</span>
            @endif
        </p>
    </div>

    <h2>Historique des vérifications</h2>

    @if ($statuses->isEmpty())
        <p>Aucune vérification n'a été effectuée pour ce site.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statuses as $status)
                    <tr>
                        <td>{{ $status->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if ($status->status)
                                <span class="text-success">En ligne</span>
                            @else
                                <span class="text-danger">Hors ligne</span>
                            @endif
                        </td>
                        <td>{{ $status->message ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Capture d'écran du site</h2>
    <div>
        <img src="https://api.screenshotmachine.com?key=YOUR_API_KEY&url={{ $site->url }}&dimension=1024x768" alt="Capture du site" class="img-fluid">
    </div>

    <a href="{{ route('sites.index') }}" class="btn btn-primary mt-4">Retour à la liste des sites</a>
</div>
@endsection
