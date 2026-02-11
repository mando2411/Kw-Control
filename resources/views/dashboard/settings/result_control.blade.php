@extends('layouts.dashboard.app')

@section('content')
    <!-- Container-fluid starts-->
    <div class="container-fluid" id="settings-app">
        <div class="row">
            <x-dashboard.partials.message-alert />
            <div class="card tab2-card">
                <!-- ================================================================== -->
                <form action="{{ route('dashboard.settings.update' ) }}" method="POST" >
                    @csrf
                    @method('PUT')
                    <div class="card-body needs-validation" style="direction: rtl;">
                        <x-dashboard.form.multi-tab-card :tabs="['للتحكم بامكانية عرض صفحة النتائج العامه']"  tab-id="settings">
                            <div class="tab-pane fade active show"
                                id="{{ 'settings-0' }}" role="tabpanel"
                                aria-labelledby="{{ 'settings-0' }}-tab">
        
                                    <div class="col-12 mb-3">
                                        <div class="card border-danger text-center fw-bold">
                                            <!-- <span style="color:red; font-weight: bold;padding: 1%;">
                                                "هذه الصفحة مخصصة للتحكم بامكانية عرض صفحة النتائج العامه يرجي العلم انه يجب اضافة مرشح تحت اسم (مرشح الفرز العام ) ان لم يتم اضافته سابقا لتتمكن بالتحكم بهذه الميزة "
                                            </span>                                     -->
                                        </div>
                                    </div>
                                <x-dashboard.form.input-checkbox error-key="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}" name="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}[]" id="{{ \App\Enums\SettingKey::RESULT_CONTROL->value }}"
                                    label-title="عرض النتائج العامه" 
                                    :value="old(\App\Enums\SettingKey::RESULT_CONTROL->value.'.0', $settings->firstWhere('option_key', \App\Enums\SettingKey::RESULT_CONTROL->value)?->option_value[0] ?? '')"
                                    />
                                    
                                <select name="{{ \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value }}[]" id="{{ \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value }}" class="form-control">
                                    <option value="" selected>أختر المرشح</option>
                                    @foreach ($candidates as $candidate )
                                        <option value="{{$candidate->user_id}}" 
                                        <?php
                                            $check_setting=$settings->firstWhere('option_key', \App\Enums\SettingKey::RESULT_CONTROL_CANDIDATE->value);
                                            if((isset($check_setting)) && ($check_setting->option_value != null) && ($check_setting->option_value[0] == $candidate->user_id) ){
                                                echo 'selected';
                                            }
                                        ?>
                                        > ({{$candidate->user_id}}) - {{$candidate->user->name}}</option>
                                    @endforeach
                                </select>
                                <br>
                
                            </div>
                        </x-dashboard.form.multi-tab-card>
                        <div style="float: left;">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
                <!-- ================================================================== -->
                <hr>
                <!-- ================================================================== -->
                <form action="{{ route('attendant.initalize' ) }}" method="POST" >
                    @csrf
                    @method('PUT')
                    <div class="card-body needs-validation" style="direction: rtl;" >
                        <x-dashboard.form.multi-tab-card :tabs="['لتصفير الحضور ل انتخابات معينه']" tab-id="initalize_attendant" >
                            <div class="tab-pane fade active show">
                                <select name="election_id" id="election_id" class="form-control" required>
                                    <option value="" selected>أختر الانتخابات</option>
                                    @foreach ($elections as $election )
                                        <option value="{{$election->id}}" > ({{$election->name}})</option>
                                    @endforeach
                                </select>
                                <br>
                
                            </div>
                        </x-dashboard.form.multi-tab-card>
                        <div style="float: left;">
                            <button type="submit" class="btn btn-primary">تصفير الحضور</button>
                        </div>
                    </div>
                </form>
                <br>
                <!-- ============================================== -->
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
@endsection