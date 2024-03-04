@extends('layout')
@push('styles')
<link rel="stylesheet" href="{{ URL::asset('css/home.css') }}">
@endpush
@push('jsHeader')
<script src="{{ URL::asset('js/home.js') }}"></script>
<script src='{{ URL::asset('js/fullcalendar/index.global.min.js') }}'></script>
@endpush

@section('content')

<div id="dates">
    <div id="receptionDates">
        <div id="receptionDatesSticky">
            <h2>Ügyfélfogadási idők:</h2>
            <ul id="allReceptionDatesList"><div style="height: 64px"></div></ul>
        </div>
    </div>
    <div id="calendarLoader"></div>
    <div id="calendar"></div>
    <div id="allReservations">
        <div id="allReservationsSticky">
            <h2>Már lefoglalt időpontok:</h2>
            <ul id="allReservationsList"><div style="height: 64px"></div></ul>
        </div>
    </div>
</div>
<script>new Home().run();</script>

@endsection
