@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.reports.update' , $report) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Report" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.reports.index') }}">Reports</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />

                
                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="pdf" name="pdf" :value="$report->pdf" id="pdf" label-title="Pdf"/>

<x-dashboard.form.input-text error-key="creator_id" name="creator_id" :value="$report->creator_id" id="creator_id" label-title="CreatorId"/>


                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                
            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
