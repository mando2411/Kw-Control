@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.candidates.store' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Candidate" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.candidates.index') }}">Candidates</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />


                <x-dashboard.form.personal-info :relations="$relations" />

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.media title="Add Banner" :images="old('gallery')"
                        name="banner" />

                <x-dashboard.form.input-select name="election_id"  :options="$relations['elections']" track-by="id"
                option-lable="name" label-title="Election" id="election_id"
                error-key="election_id" />

<x-dashboard.form.input-text error-key="max_contractor" name="max_contractor"  id="max_contractor" label-title="MaxContractor"/>

<x-dashboard.form.input-text error-key="max_represent" name="max_represent"  id="max_represent" label-title="MaxRepresent"/>


                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
