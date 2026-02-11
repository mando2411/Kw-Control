@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.contractors.update' , $contractor) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Contractor" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.contractors.index') }}">Contractors</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />

                <x-dashboard.form.personal-info-show :relations="$relations" :value="$contractor"/>

                <div class="card">
                    <div class="card-body">
                    <x-dashboard.form.input-select :options="$relations['contractors']"
                    option-lable="name"
                    track-by="id"
                    :value="$contractor->parent_id"
                   error-key="parent_id"
                   name="parent_id"
                   id="parent_id"
                   label-title="Parent Contractor"/>

                   <x-dashboard.form.input-select name="election_id"  :options="$relations['elections']" track-by="id"
                   option-lable="name" label-title="Election"
                   :value="$contractor->election_id"
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
