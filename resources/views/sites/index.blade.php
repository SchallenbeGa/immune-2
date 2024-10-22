@extends('layouts.app')

@section('content')
<script
  src="https://code.jquery.com/jquery-3.7.1.slim.js"
  integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc="
  crossorigin="anonymous"></script>
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
        <div class="mb-3">
            <label for="type" class="form-label">Type (HTTP,ICMP)</label>
            <input type="type" name="type" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="port" class="form-label">Port</label>
            <input type="port" name="port" class="form-control">
        </div>
        <div class="mb-3">
            <label for="method" class="form-label">Method</label>
            <input type="method" name="method" class="form-control">
        </div>
        <div class="mb-3">
            <label for="header" class="form-label">Header</label>
            <input type="header" name="header" class="form-control">
        </div>
        <button type="submit" style="margin-top:1rem;" class="btn btn-primary">Ajouter le site</button>
    </form>
    @endauth
    <h2 class="mt-5">Sites surveillés</h2>
   
    <div class="floating-text"></div>
    <div class="service-container"></div>
     

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
<script>
    function CalculateDate(from, days) {
    var split = from.split('.');
    var year = parseInt(split[0]);
    var month = parseInt(split[1]) - 1;
    var day = parseInt(split[2]);
    var date = new Date(year, month, day);
    date.setDate(date.getDate() + days);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    month = month < 10 ? '0' + month.toString() : month.toString();
    day = day < 10 ? '0' + day.toString() : day.toString();
    date = year.toString() + '.' + month + '.' + day;
    return date;
}

    function AverageArray(array) {
    var sum = 0;
    for (var i = 0; i < array.length; i++) {
        sum += parseFloat(array[i]);
    }
    return sum / array.length;
}
    function GetColorByPercent(percent) {
    var r = 61;
    var g = 243;
    var b = 61;
    if (percent >= 99.9) {
        r = 243 - ((percent - 75) / 25) * 182;
    } else if (percent >= 90) {
        r = 243;
        g = 61 + ((percent - 50) / 25) * 182;
    } else {
        r = 243;
        g = 61;
    }
    return 'rgb(' + r.toFixed(0) + ',' + g.toFixed(0) + ',' + b.toFixed(0) + ')';
}
    @foreach ($sites as $site)
        CreateChart({{ $site->id }}, '{{ $site->name }}', @json($site->statuses));
    @endforeach
      function CreateChart(id, name, data) {
    var statusColorList = {
        normal: "#2fcc66",
        warning: "#e9a420",
        error: "#e92020",
        unknown: "#cccccc"
    }
    var statusTextList = {
        normal: "normal",
        warning: "warn",
        error: "erro",
        unknown: "unk"
    }
    var statusPercentList = {
        normal: 0,
        warning: 0,
        error: 0
    }
    var chartSvgHtml = '';
    var currentStatus = 'normal';
    var padding = 0;
    var currentDate = new Date();
    currentDate = currentDate.getFullYear().toString() + '.' + (currentDate.getMonth() + 1).toString() + '.' + currentDate.getDate().toString();
    const uniqueDays = new Set();

// 2. Parcourir le tableau et extraire uniquement les jours
data.forEach(status => {
    // Extraire la date (partie sans l'heure) de created_at
    const dateOnly = status.created_at.split('T')[0]; // Prend juste la partie YYYY-MM-DD
    uniqueDays.add(dateOnly); // Ajoute au Set, qui garde seulement les valeurs uniques
});

// 3. Afficher le nombre de jours distincts
console.log("Nombre de jours distincts:", uniqueDays.size);
    for (var i = 0; i < 90 - uniqueDays.size; i++) {
        var date = CalculateDate(currentDate, -(89 - i));
        console.log(date);
        chartSvgHtml += `<rect height="34" width="3" x="${padding * 5}" y="0" fill="#cccccc" class="uptime-day uptime-no-data day-${i}" data-date="${date}" data-incident="" tabindex="0"></rect>`;
        padding++;
    }
    var timeList = [];
    for (var i = 0; i < data.length; i++) {
        var day = data[i]['formatted_created_at'] || {
            status: 'unknown'
        };
       
        var dayIncident = '';
        var incidentTime = 0;
        var onlinePercent = 100;
        if (day.incident && day.incident.length > 0) {
            for (var j = 0; j < day.incident.length; j++) {
                var incident = day.incident[j];
                var statusTextIncident = statusTextList[incident.status];
                dayIncident += '<b>' + ConvertTimestamp(incident.start) + '</b> ' + statusTextIncident;
                if (incident.reason) {
                    dayIncident += ' | ' + incident.reason;
                }
                if (j < day.incident.length - 1) {
                    dayIncident += '<br>';
                }
                if (incident.status != 'normal') {
                    if (incident.end) {
                        incidentTime += incident.end - incident.start;
                    } else {
                        incidentTime += ((new Date(day.date)) / 1000) + 86400 - incident.start;
                    }
                }
            }
            if (incidentTime > 0) {
                var calc = incidentTime / 86400 * 100;
                console.log();
                timeList.push(100 - calc);
                onlinePercent = 100 - calc;
            } else {
                timeList.push(100);
                onlinePercent = 100;
            }
        } else {
            dayIncident = 'no inc';
            timeList.push(100);
            onlinePercent = 100;
        }
        dayIncident = encodeURIComponent(dayIncident);
        var dayStatus = day.status;
        var dayStatusColor = GetColorByPercent(timeList[i]);
        
        chartSvgHtml += `<rect height="34" width="3" x="${padding * 5}" y="0" fill="${dayStatusColor}" data-online-percent="${onlinePercent}" data-date="${day}" data-incident="${dayIncident}" class="uptime-day day-${i}"></rect>`;
        currentStatus = dayStatus;
        padding++;
    }
    var statusColor = statusColorList[currentStatus];
    var statusText = statusTextList[currentStatus];
    var statusPercent = AverageArray(timeList).toFixed(1);
    var chartHtml = `<div id="service-${id}" data-id="${id}">
        <span class="service-name draggable">
            <span> <a href="/sites/${id}">${name}
                  </a></span>&nbsp;&nbsp;
            <?php echo isset($_SESSION['user']) ? '<span title="edit" class="force-link text-small hover-text" onclick="EditService(${id});"><i class="fas fa-edit"></i></span>&nbsp;' : ''; ?>
            <?php echo isset($_SESSION['user']) ? '<span title="delete" class="force-link text-small hover-text" onclick="DeleteService(${id});"><i class="fas fa-trash"></i></span>' : ''; ?>
        </span>
        <span class="component-status status-text-${status}">${status}</span>
        <div class="shared-partial uptime-90-days-wrapper">
            <div class="graphic-container">
                <svg class="availability-time-line-graphic" preserveAspectRatio="none" height="34" tabindex="0" viewBox="0 0 448 34">${chartSvgHtml}</svg>
            </div>
            <div class="legend legend-group">
                <div class="legend-item light legend-item-date-range">
                    <span class="availability-time-line-legend-day-count">90</span> dayagos
                </div>
                <div class="spacer"></div>
                <div class="legend-item legend-item-uptime-value">
                    <span class="uptime-percent">${statusPercent}</span> % online
                </div>
                <div class="spacer"></div>
                <div class="legend-item light legend-item-date-range">today</div>
            </div>
        </div>
    </div>`;
    if (currentStatus == 'warning') {
        if (globalStatus == 'normal') {
            UpdateGlobalStatus('warning');
        }
        tmpStatus = 'warning';
    }
    if (currentStatus == 'error') {
        UpdateGlobalStatus('error');
        tmpStatus = 'error';
    }

    // scroll to right
    setTimeout(function() {
        $(`#service-${id} .graphic-container`).scrollLeft(999999);
    }, 100);
    $(".service-container").append(chartHtml);
    $(".uptime-day").on('mouseover', function() {
        console.log($(this).data('date'));
        var date = $(this).data('date');
        var incident = $(this).data('incident');
        incident = decodeURIComponent(incident);
        console.log(incident);
        var onlinePercent = $(this).data('online-percent') || 100;
        onlinePercent = onlinePercent.toFixed(1);
        $(".floating-text").html(`<b>${date}</b> | ${onlinePercent}%<br><span>${incident}</span>`);
        $(".floating-text").show();
        var x = $(this).offset().left;
        var y = $(this).offset().top;
        $(".floating-text").css('left', x - 150);
        $(".floating-text").css('top', y - $(".floating-text").height() - 38);
    });
    $(".uptime-day").on('mouseout', function() {
        $(".floating-text").hide();
    });
}

</script>
@endsection
