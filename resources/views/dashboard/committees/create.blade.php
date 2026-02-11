@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.committees.store' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Committee" :hideFirst="true">
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
                        <x-dashboard.form.input-text error-key="name" name="name"  id="name" label-title="Name"/>
                        <label for="type">TYPE </label>
                        <select name="type" id="type" error-key="type" >
                            <option value="ذكور">ذكور</option>
                            <option value="اناث">اناث</option>
                        </select>
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
