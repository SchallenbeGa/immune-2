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
   
        <h1>Résultats d'analyse par date (fr)</h1>
        <a href="{{ route('structure.index') }}" class="btn btn-secondary">Voir la structure du projet</a>

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
