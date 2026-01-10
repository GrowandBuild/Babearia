@extends('layouts.app')

@section('title', 'Agenda - Admin')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Agenda (Admin)</h1>

    <!-- FullCalendar CSS/JS (v5 global build via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/resource-timegrid.min.js"></script>

    <div id="calendar"></div>
</div>

<style>
    /* Ajustes visuais semelhantes ao mock */
    #calendar { max-width: 1200px; margin: 0 auto; }
    .fc-resource { display:flex; align-items:center; gap:8px }
    .fc-resource img { width:28px; height:28px; border-radius:50%; object-fit:cover }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ 'resourceTimeGrid' ],
        initialView: 'resourceTimeGridDay',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimeGridDay,resourceTimeGridWeek'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        resources: function(fetchInfo, successCallback, failureCallback) {
            fetch('/admin/agenda/events?resources=1&start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr)
                .then(r => r.json())
                .then(data => successCallback(data.resources))
                .catch(err => failureCallback(err));
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('/admin/agenda/events?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr)
                .then(r => r.json())
                .then(data => successCallback(data.events))
                .catch(err => failureCallback(err));
        },
        resourceLabelContent: function(arg) {
            var html = '';
            if (arg.resource.extendedProps && arg.resource.extendedProps.avatar) {
                html += '<img src="' + arg.resource.extendedProps.avatar + '"/> ';
            }
            html += '<strong>' + arg.resource.title + '</strong>';
            return { html: html };
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        height: 'auto',
        nowIndicator: true,
        selectable: false,
        editable: false,
        eventDidMount: function(info) {
            // adicionar tooltip simples
            info.el.title = info.event.title + ' — ' + (info.event.extendedProps.status || '');
        }
    });

    calendar.render();
});
</script>
@endpush

@endsection
