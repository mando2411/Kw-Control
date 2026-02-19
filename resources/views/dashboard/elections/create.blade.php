@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.elections.store' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="إنشاء انتخابات" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.elections.index') }}">الانتخابات</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />


                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">بيانات الانتخابات</h6>
                        <p class="text-muted mb-4">أدخل البيانات الأساسية بدقة، لأن التواريخ والأوقات تتحكم في مراحل العمل داخل النظام.</p>

                        <x-dashboard.form.input-text error-key="name" name="name"  id="name" label-title="اسم الانتخابات"/>

<x-dashboard.form.input-text error-key="start_date" name="start_date"  id="start_date" label-title="تاريخ البداية" date/>

<x-dashboard.form.input-text error-key="end_date" name="end_date"  id="end_date" label-title="تاريخ النهاية" date/>

<x-dashboard.form.input-text error-key="start_time" name="start_time"  id="start_time" label-title="وقت البداية" time/>

<x-dashboard.form.input-text error-key="end_time" name="end_time"  id="end_time" label-title="وقت النهاية" time/>

<x-dashboard.form.input-text error-key="type" name="type"  id="type" label-title="نوع الانتخابات"/>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('dashboard.elections.index') }}" class="btn btn-light border">العودة للقائمة</a>
                            <button type="submit" class="btn btn-primary">حفظ الانتخابات</button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
