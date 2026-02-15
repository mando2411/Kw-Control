@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.candidates.update' , $candidate) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Candidate" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.candidates.index') }}">Candidates</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />


                <x-dashboard.form.personal-info-show :relations="$relations" :value="$candidate"/>

                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.media title="Add Image" :images="$candidate->banner"
                            error-key="banner" name="banner"  id="banner"  />
                            <x-dashboard.form.input-select name="election_id"  :options="$relations['elections']" track-by="id"
                            option-lable="name" label-title="Election"
                            :value="$candidate->election_id"
                            id="election_id"
                            error-key="election_id"  />
<x-dashboard.form.input-text error-key="max_contractor" name="max_contractor" :value="$candidate->max_contractor" id="max_contractor" label-title="MaxContractor"/>

<x-dashboard.form.input-text error-key="max_represent" name="max_represent" :value="$candidate->max_represent" id="max_represent" label-title="MaxRepresent"/>


                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
