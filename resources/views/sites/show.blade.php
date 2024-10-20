@extends('layouts.app')

@section('content')
<div class="container">
@if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <h1>Détails du site : {{ $site->name }}</h1>

    <div class="mb-3">
        @auth
        <p><strong>URL :</strong> <a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a></p>
        @endauth
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
    @auth
    <a href="{{ route('sites.screenshot', $site->id) }}" class="btn btn-primary">Capturer l'image du site</a>

    @if ($site->screenshot_path)
    <h2>Capture d'écran du site</h2>
    <div>
        <img style="max-width: 100%;" src="{{ asset($site->screenshot_path) }}" alt="Capture du site" class="img-fluid">
    </div>
@else
    <p>Aucune capture d'écran disponible.</p>
@endif
@endauth
    <a href="{{ route('sites.index') }}" style="margin-top:1rem;" class="btn btn-primary mt-4">Retour à la liste des sites</a>
</div>
@endsection
