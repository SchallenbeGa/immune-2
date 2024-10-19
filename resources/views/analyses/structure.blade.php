@extends('layouts.app')

@section('content')
<style>
        .folder {
            font-weight: bold;
            cursor: pointer;
            margin: 10px 0;
        }

        .folder::before {
            content: "📁";
            margin-right: 5px;
        }

        .files {
            display: none; /* Les fichiers sont cachés par défaut */
            margin-left: 20px;
        }

        .file-item {
            margin: 5px 0;
        }

        .file-item::before {
            content: "📄";
            margin-right: 5px;
        }
    </style>
    <div class="container">
        <h1>Structure du projet</h1>

        <a href="{{ route('analyse.index') }}" class="btn btn-primary">Retour à l'analyse</a>
        @foreach ($files as $folder => $fileList)
            <!-- Répertoire parent -->
            <div class="folder" onclick="toggleFolder('{{ sha1($folder) }}')">
                {{ basename($folder) }} <!-- Affiche juste le nom du dossier, pas le chemin complet -->
            </div>

            <!-- Liste des fichiers dans le répertoire parent -->
            <div class="files" id="{{ sha1($folder) }}">
                @foreach ($fileList as $file)
                    <div class="file-item">
                    <a href="{{ route('analyses.file', $file->id) }}">
                            {{ $file->file_name }}
                        </a>
                        <span>({{ $file->file_size }} octets, modifié le {{ \Carbon\Carbon::parse($file->last_modified)->format('d/m/Y H:i:s') }})</span>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <script>
        // Fonction pour montrer ou cacher les fichiers dans un dossier
        function toggleFolder(folderId) {
            var filesElement = document.getElementById(folderId);
            if (filesElement.style.display === "none" || filesElement.style.display === "") {
                filesElement.style.display = "block"; // Afficher les fichiers
            } else {
                filesElement.style.display = "none";  // Cacher les fichiers
            }
        }
    </script>
@endsection
