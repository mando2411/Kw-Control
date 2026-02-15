@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.representatives.update' , $representative) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Representative" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.representatives.index') }}">Representatives</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />

                <x-dashboard.form.personal-info-show :relations="$relations" :value="$representative"/>


                <div class="card">
                    <div class="card-body">


                   <x-dashboard.form.input-select name="election_id"  :options="$relations['elections']" track-by="id"
                   option-lable="name" label-title="Election"
                   :value="$representative->election_id"
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
