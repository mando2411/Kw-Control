@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.settings.update' ) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Settings" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.settings.show') }}">Settings</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->


        <!-- Container-fluid starts-->
        <div class="container-fluid" id="settings-app">
            <div class="row">
                <x-dashboard.partials.message-alert/>
                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.multi-tab-card
                            :tabs="['رسائل']"
                            tab-id="settings">

                            
                            <div class="tab-pane fade active show"
                            id="{{ 'settings-0' }}" role="tabpanel"
                            aria-labelledby="{{ 'settings-0' }}-tab">

                            <x-dashboard.form.input-editor error-key="{{ \App\Enums\SettingKey::MESSAGE->value }}"
                                                             required
                                                             :value="old(\App\Enums\SettingKey::MESSAGE->value.'.0',
                                                              $settings->firstWhere('option_key', \App\Enums\SettingKey::MESSAGE->value)?->option_value[0] ?? '')"
                                                             name="{{ \App\Enums\SettingKey::MESSAGE->value }}[]" id="{{ \App\Enums\SettingKey::MESSAGE->value }}"
                                                             label-title="Whatsapp Message"/>
                       </div>

                        </x-dashboard.form.multi-tab-card>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
