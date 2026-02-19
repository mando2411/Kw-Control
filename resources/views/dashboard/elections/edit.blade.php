@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.elections.update' , $election) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="تعديل الانتخابات" :hideFirst="true">
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
                        <h6 class="mb-3">تعديل بيانات الانتخابات</h6>
                        <p class="text-muted mb-4">عدّل البيانات المطلوبة ثم احفظ، وسيتم تحديث المعلومات مباشرة في صفحة الانتخابات.</p>

                        <x-dashboard.form.input-text error-key="name" name="name" :value="$election->name" id="name" label-title="اسم الانتخابات"/>

<x-dashboard.form.input-text date error-key="start_date" name="start_date" :value="old('start_date', optional($election->start_date)->format('Y-m-d'))" id="start_date" label-title="تاريخ البداية"/>

<x-dashboard.form.input-text date error-key="end_date" name="end_date" :value="old('end_date', optional($election->end_date)->format('Y-m-d'))" id="end_date" label-title="تاريخ النهاية"/>

<x-dashboard.form.input-text time error-key="start_time" name="start_time" :value="old('start_time', $election->start_time ? \Carbon\Carbon::parse($election->start_time)->format('H:i') : '')" id="start_time" label-title="وقت البداية"/>

<x-dashboard.form.input-text time error-key="end_time" name="end_time" :value="old('end_time', $election->end_time ? \Carbon\Carbon::parse($election->end_time)->format('H:i') : '')" id="end_time" label-title="وقت النهاية"/>

<x-dashboard.form.input-text error-key="type" name="type" :value="$election->type" id="type" label-title="نوع الانتخابات"/>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('dashboard.elections.index') }}" class="btn btn-light border">العودة للقائمة</a>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
