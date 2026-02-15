@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.voters.store' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Voter" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.voters.index') }}">Voters</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />


                <div class="card">
                    <div class="card-body">
                        <x-dashboard.form.input-text error-key="name" name="name"  id="name" label-title="{{__('main.Name')}}"/>

<x-dashboard.form.input-text error-key="type" name="type"  id="type" label-title="{{__('main.Type')}}"/>

<x-dashboard.form.input-text error-key="almrgaa" name="almrgaa"  id="almrgaa" label-title="{{__('main.Almrgaa')}}"/>

<x-dashboard.form.input-text error-key="albtn" name="albtn"  id="albtn" label-title="{{__('main.Albtn')}}"/>

<x-dashboard.form.input-text error-key="alfraa" name="alfraa"  id="alfraa" label-title="{{__('main.Alfraa')}}"/>

<x-dashboard.form.input-text error-key="btn_almoyhy" name="btn_almoyhy"  id="btn_almoyhy" label-title="{{__('main.BtnAlmoyhy')}}"/>

<x-dashboard.form.input-text error-key="tary_kh_alandmam" name="tary_kh_alandmam"  id="tary_kh_alandmam" label-title="{{__('main.TaryKhAlandmam')}}"/>

<x-dashboard.form.input-text error-key="alrkm_ala_yl_llaanoan" name="alrkm_ala_yl_llaanoan"  id="alrkm_ala_yl_llaanoan" label-title="{{__('main.AlrkmAlaYlLlaanoan')}}"/>

<x-dashboard.form.input-text error-key="alktaa" name="alktaa"  id="alktaa" label-title="{{__('main.alktaa')}}"/>

<x-dashboard.form.input-text error-key="alrkm_almd_yn" name="alrkm_almd_yn"  id="alrkm_almd_yn" label-title="{{__('main.AlrkmAlmdYn')}}"/>

<x-dashboard.form.input-text error-key="alsndok" name="alsndok"  id="alsndok" label-title="{{__('main.Job')}}"/>

<x-dashboard.form.input-text error-key="yearOfBirth" name="yearOfBirth"  id="yearOfBirth" label-title="{{__('main.YearOfBirth')}}"/>

<x-dashboard.form.input-text error-key="phone" name="phone"  id="phone" label-title="{{__('main.Phone')}}"/>

<x-dashboard.form.input-text error-key="region" name="region"  id="region" label-title="{{__('main.Region')}}"/>



<x-dashboard.form.input-checkbox  error-key="status" name="status"
id="status" label-title="{{__('main.Status')}}" />

<x-dashboard.form.input-text error-key="alfkhd" name="alfkhd"  id="alfkhd" label-title="{{__('main.Alfkhd')}}"/>

<x-dashboard.form.input-text error-key="age" name="age"  id="age" label-title="{{__('main.Age')}}"/>

<x-dashboard.form.input-select error-key="family_id"
:options="$relations['families']"
option-lable="name"
track-by="id"
name="family_id" id="fam"
label-title="{{__('main.Family')}}"/>

<x-dashboard.form.input-select error-key="committee_id"
:options="$relations['committees']"
option-lable="name"
track-by="id"
name="committee_id" id="committee_id"
label-title="{{__('main.committee')}}"/>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
