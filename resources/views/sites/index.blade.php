@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Surveillance des Sites</h1>
    @auth
    <!-- Formulaire d'ajout de site -->
    <form action="{{ route('sites.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nom du site</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="url" class="form-label">URL</label>
            <input type="url" name="url" class="form-control" required>
        </div>
        <button type="submit" style="margin-top:1rem;" class="btn btn-primary">Ajouter le site</button>
    </form>
    @endauth
    <h2 class="mt-5">Sites surveillés</h2>
    <ul>
        @foreach ($sites as $site)
            <li>
                <a href="{{ route('sites.show', $site) }}">{{ $site->name }} 
                    @auth
                     ({{ $site->url }})
                      @endauth</a>
                @if($site->status)
                    <span class="text-success">En ligne</span>
                @else
                    <span class="text-danger">Hors ligne</span>
                @endif
            </li>
        @endforeach
    </ul>

    <h1>Sites hors ligne par date</h1>

@if ($offlineStatuses->isEmpty())
    <p>Aucun site n'a été hors ligne récemment.</p>
@else
    @foreach ($offlineStatuses as $date => $statuses)
        <h3>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
        <ul>
            @foreach ($statuses as $status)
                <li>
                    <a href="{{ route('sites.show', $status->site->id) }}">
                        {{ $status->site->name }} ({{ $status->site->url }})
                    </a>
                    <br>
                    <span>
                        Erreur signalée le {{ $status->created_at->format('d/m/Y H:i:s') }} : 
                        {{ $status->message }}
                    </span>
                </li>
            @endforeach
        </ul>
    @endforeach
@endif

</div>
@endsection
