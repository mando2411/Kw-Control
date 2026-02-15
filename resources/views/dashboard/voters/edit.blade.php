@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.voters.update' , $voter) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Voter" :hideFirst="true">
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
                        <x-dashboard.form.input-text error-key="name" name="name" :value="$voter->name" id="name" label-title="Name"/>

<x-dashboard.form.input-text error-key="type" name="type" :value="$voter->type" id="type" label-title="Type"/>

<x-dashboard.form.input-text error-key="almrgaa" name="almrgaa" :value="$voter->almrgaa" id="almrgaa" label-title="Almrgaa"/>

<x-dashboard.form.input-text error-key="albtn" name="albtn" :value="$voter->albtn" id="albtn" label-title="Albtn"/>

<x-dashboard.form.input-text error-key="alfraa" name="alfraa" :value="$voter->alfraa" id="alfraa" label-title="Alfraa"/>

<x-dashboard.form.input-text error-key="yearOfBirth" name="yearOfBirth" :value="$voter->yearOfBirth" id="yearOfBirth" label-title="YearOfBirth"/>

<x-dashboard.form.input-text error-key="btn_almoyhy" name="btn_almoyhy" :value="$voter->btn_almoyhy" id="btn_almoyhy" label-title="BtnAlmoyhy"/>

<x-dashboard.form.input-text error-key="tary_kh_alandmam" name="tary_kh_alandmam" :value="$voter->tary_kh_alandmam" id="tary_kh_alandmam" label-title="TaryKhAlandmam"/>

<x-dashboard.form.input-text error-key="alrkm_ala_yl_llaanoan" name="alrkm_ala_yl_llaanoan" :value="$voter->alrkm_ala_yl_llaanoan" id="alrkm_ala_yl_llaanoan" label-title="AlrkmAlaYlLlaanoan"/>

<x-dashboard.form.input-text error-key="alktaa" name="alktaa" :value="$voter->alktaa" id="alktaa" label-title="Alktaa"/>

<x-dashboard.form.input-text error-key="alrkm_almd_yn" name="alrkm_almd_yn" :value="$voter->alrkm_almd_yn" id="alrkm_almd_yn" label-title="AlrkmAlmdYn"/>

<x-dashboard.form.input-text error-key="alsndok" name="alsndok" :value="$voter->alsndok" id="alsndok" label-title="Alsndok"/>

<x-dashboard.form.input-text error-key="phone" name="phone" :value="$voter->phone" id="phone" label-title="Phone"/>

<x-dashboard.form.input-text error-key="region" name="region" :value="$voter->region" id="region" label-title="Region"/>

<x-dashboard.form.input-text error-key="status" name="status" :value="$voter->status" id="status" label-title="Status"/>

<x-dashboard.form.input-text error-key="committee_id" name="committee_id" :value="$voter->committee_id" id="committee_id" label-title="CommitteeId"/>

<x-dashboard.form.input-text error-key="family_id" name="family_id" :value="$voter->family_id" id="family_id" label-title="FamilyId"/>

<x-dashboard.form.input-text error-key="alfkhd" name="alfkhd" :value="$voter->alfkhd" id="alfkhd" label-title="Alfkhd"/>

<x-dashboard.form.input-text error-key="age" name="age" :value="$voter->age" id="age" label-title="Age"/>

<x-dashboard.form.input-text error-key="user_id" name="user_id" :value="$voter->user_id" id="user_id" label-title="UserId"/>


                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
