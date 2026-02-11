@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.committees.update' , $committee) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Committee" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.committees.index') }}">Committees</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />


                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="name" name="name" :value="$committee->name" id="name" label-title="Name"/>

<x-dashboard.form.input-text error-key="type" name="type" :value="$committee->type" id="type" label-title="Type"/>
    <x-dashboard.form.input-select name="election_id"  :options="$relations['elections']" track-by="id"
    option-lable="name" label-title="Election"
    :value="$committee->election_id"
    id="election_id"
    error-key="election_id"  />

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
