@extends('layouts.dashboard.app')

@section('content')


<form action="{{ route('elecUp') }}" method="POST">
@csrf

<x-dashboard.form.input-select name="election_id"  :options="$elections" track-by="id"
option-lable="name" label-title="Election" id="election_id"
error-key="election_id" />

<x-dashboard.form.submit-button/>



</form>

@endsection