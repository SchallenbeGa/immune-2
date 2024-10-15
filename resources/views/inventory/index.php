<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaire</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Inventaire des Machines</h1>
    <table>
        <thead>
            <tr>
                <th>Référence de la machine</th>
                <th>Nom de l'utilisateur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($computers as $computer)
                <tr>
                    <td>{{ $computer->reference }}</td>
                    <td>{{ $computer->user->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
