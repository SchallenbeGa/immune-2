@extends('layouts.app')

@section('content')
    
    <div class="container">
        <h1>Analyses du fichier: {{ $file->file_name }}</h1>
        <a href="{{ route('structure.index') }}" class="btn btn-secondary">Retour à la structure du projet</a>
        @if($file->analyses->isEmpty())
            <p>Aucune analyse n'a encore été réalisée pour ce fichier.</p>
        @else
            <ul>
                @foreach ($file->analyses as $analysis)
                    <li>
                        <strong>Date de l'analyse :</strong> {{ \Carbon\Carbon::parse($analysis->created_at)->format('d/m/Y H:i:s') }}<br>
                        <strong>Résultat :</strong> {{ $analysis->recommendation}}
                    </li>
                @endforeach
            </ul>
        @endif

       
    </div>
@endsection
