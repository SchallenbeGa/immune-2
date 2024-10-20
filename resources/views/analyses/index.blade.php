@extends('layouts.app')

@section('content')
    <style>
        /* Style des dates */
        .date-link {
            display: block;
            margin: 10px 0;
            padding: 10px;
            background-color: #009879;
            color: #fff;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
        }

        .date-link:hover {
            background-color: #007d63;
        }

        /* Section détails */
        #analysis-details {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            display: none;
        }

        .analysis-item {
            margin-bottom: 20px;
        }

        .analysis-item h4 {
            color: #009879;
        }
    </style>

    <div class="container">
    @auth
    @if (auth()->user()->role>2)
    <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">view visits</a>
    @endif
    @endauth
    <a href="{{ route('sites.index') }}" class="btn btn-secondary">view sites</a>
    <a href="{{ route('command.history.index') }}" class="btn btn-secondary">view commands</a>
    <a href="{{ route('structure.index') }}" class="btn btn-secondary">view structure</a>
    @auth
    @if (auth()->user()->role>2)
    <h1>generate</h1>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @if(session('operation'))
                <h4>Dernière opération :</h4>
                <ul>
                    <li>Description : {{ session('operation')->description }}</li>
                    <li>Migration : {{ session('operation')->migration_name }}</li>
                    <li>Modèle : {{ session('operation')->model_name }}</li>
                    <li>Contrôleur : {{ session('operation')->controller_name }}</li>
                    <li>Vue : {{ session('operation')->view_name }}</li>
                </ul>
            @endif
        @endif

        <form action="{{ url('/generate-crud') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" id="description" name="description" required>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:1rem">Générer</button>
        </form>
        @endif
        @endauth
        <h1>results</h1>
     
        <div id="date-list">
            @foreach ($dates as $date)
                <a href="#" class="date-link" data-date="{{ $date->date }}">
                    {{ \Carbon\Carbon::parse($date->date)->format('d-m-Y') }}
                </a>
            @endforeach
        </div>

        <!-- Section pour afficher les détails d'analyse -->
        <div id="analysis-details">
            <h2>Détails des analyses pour la date : <span id="analysis-date"></span></h2>
            <div id="analysis-content"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Récupérer toutes les dates cliquables
            const dateLinks = document.querySelectorAll('.date-link');
            const analysisDetails = document.getElementById('analysis-details');
            const analysisContent = document.getElementById('analysis-content');
            const analysisDate = document.getElementById('analysis-date');

            // Ajouter un événement de clic sur chaque lien de date
            dateLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const selectedDate = this.dataset.date;

                    // Mettre à jour l'interface utilisateur pour la date sélectionnée
                    analysisDate.textContent = new Date(selectedDate).toLocaleDateString();

                    // Vider le contenu des analyses précédentes
                    analysisContent.innerHTML = '';

                    // Afficher les détails d'analyse
                    analysisDetails.style.display = 'block';

                    // Appeler l'API pour récupérer les analyses de cette date
                    fetch(`/analyse/${selectedDate}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            data.forEach(analysis => {
                                // Créer un élément HTML pour chaque analyse
                                const analysisItem = document.createElement('div');
                                analysisItem.classList.add('analysis-item');

                                analysisItem.innerHTML = `
                                    <h4 style="line-break: anywhere;">Fichier:<a href="/analyses/${analysis.project_file_id}">
                            ${analysis.file_name}
                        </a> </h4> <!-- Affiche uniquement le nom du fichier -->
                                    <p><strong>Action:</strong> ${analysis.action_performed}</p>
                                    <p><strong>Recommandation:</strong> ${analysis.recommendation}</p>
                                `;
                                // Ajouter à la section d'affichage
                                analysisContent.appendChild(analysisItem);
                            });
                        } else {
                            analysisContent.innerHTML = '<p>Aucune analyse trouvée pour cette date.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération des données:', error);
                        analysisContent.innerHTML = '<p>Erreur lors de la récupération des analyses.</p>';
                    });
                });
            });
        });
    </script>
@endsection
