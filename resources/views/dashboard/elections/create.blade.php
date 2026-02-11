@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.elections.store' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Election" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.elections.index') }}">Elections</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />


                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="name" name="name"  id="name" label-title="Name"/>

<x-dashboard.form.input-text error-key="start_date" name="start_date"  id="start_date" label-title="StartDate" date/>

<x-dashboard.form.input-text error-key="end_date" name="end_date"  id="end_date" label-title="EndDate" date/>

<x-dashboard.form.input-text error-key="start_time" name="start_time"  id="start_time" label-title="StartTime" time/>

<x-dashboard.form.input-text error-key="end_time" name="end_time"  id="end_time" label-title="EndTime" time/>

<x-dashboard.form.input-text error-key="type" name="type"  id="type" label-title="Type"/>


                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
