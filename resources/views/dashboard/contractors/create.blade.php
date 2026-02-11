@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.con-main' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Contractor" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.contractors.index') }}">Contractors</a>
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
                   <x-dashboard.form.input-select name="election_id"  :options="$relations['elections']" track-by="id"
                   option-lable="name" label-title="Election" id="election_id"
                   error-key="election_id" />


                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
