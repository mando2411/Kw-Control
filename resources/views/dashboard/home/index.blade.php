@extends('layouts.dashboard.app')

@section('content')

{{-- voter --}}
{{-- <style>
    .button-arrived {
        background-color: green;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
    .button-not-arrived {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
    .button-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style> --}}


{{-- canditates --}}
<style>
    .button {
        margin: 5px;
        padding: 10px;
        font-size: 1.2em;
        cursor: pointer;
    }

    .button-increment {
        background-color: green;
        color: white;
    }

    .button-decrement {
        background-color: red;
        color: white;
    }

    .button-update {
        background-color: blue;
        color: white;
        margin-top: 10px;
    }

    .form-container {
        max-width: 500px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
    }

    .custom-file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        opacity: 0;
        cursor: pointer;
    }

    .custom-file-label {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 10px;
        display: inline-block;
        width: 100%;
        text-align: center;
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
    }

    .btn-custom {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .btn-custom:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .online {
        animation: myAnim 1000ms ease 0s infinite normal both;
    }

    @keyframes myAnim {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        /* Start slightly lower */
        transition: opacity 0.5s ease, transform 0.5s ease;
        /* Transition effect */
    }

    /* Final state (visible) */
    .fade-in.show {
        opacity: 1;
        transform: translateY(0);
        /* Move to its original position */
    }

    .import-card {
        border: 1px solid #e3e6ea;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .import-header {
        border-bottom: 1px solid #eef1f4;
    }

    .import-help {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .import-warning {
        background: #fff4e5;
        border: 1px solid #ffd19a;
        color: #8a4b00;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 0.95rem;
    }

    .import-option {
        border: 1px solid #e3e6ea;
        border-radius: 10px;
        padding: 12px 14px;
        height: 100%;
        background: #f8f9fb;
    }

    .import-option input {
        margin-top: 6px;
    }

    .import-option .option-title {
        font-weight: 600;
    }

    .import-option .option-desc {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .import-option.option-danger {
        border-color: #f1b0b7;
        background: #fff5f5;
    }

    .import-field.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
    }

    .import-field.is-valid {
        border-color: #198754 !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15);
    }

    .import-error {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 6px;
    }
</style>
<div class="projectContainer mx-auto">
    <!-- banner -->
    @if (auth()->user()->candidate()->exists())

    <div class="pt-5 banner rtl">
        <div class="container-fluid">
            <div class="d-flex border-bottom justify-content-center align-items-center">
                <div class="">
                    <figure class="mx-auto text-center rounded-circle overflow-hidden" style="@auth
                          width: 150px;
      height: 150px;
      margin-left: 25px !important;
                  @endauth"><img src="{{auth()->user()->image}}" class="w-100 h-100" alt=""></figure>
                </div>
                <div class="text-center me-3">
                    <h1>{{auth()->user()->name}}</h1>
                    <p class="fs-5"> {{"مرشح". "  ".  auth()->user()->election?->name }} </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <section class="countDown py-3">
        <div class="container">

            <div class="text-center madameenControl mx-auto">
                <h2 class="textMainColor time-election">
                    باقى على موعد الانتخابات
                </h2>

                <div class="row g-3 align-items-center mt-3 mb-1">
                    <div class="col-3">
                        <div class="inner border border-2 p-2 rounded-3 rounded">
                            <div class="textMainColor text-center fw-bold">
                                <span class="fs-2" id="days"></span> <br />
                                يوم
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="inner border border-2 p-2 rounded-3 rounded">
                            <div class="textMainColor text-center fw-bold">
                                <span class="fs-2" id="hours"></span> <br />
                                ساعه
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="inner border border-2 p-2 rounded-3 rounded">
                            <div class="textMainColor text-center fw-bold">
                                <span class="fs-2" id="minutes"></span> <br />
                                دقيقه
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="inner border border-2 p-2 rounded-3 rounded">
                            <div class="textMainColor text-center fw-bold">
                                <span class="fs-2" id="seconds"></span> <br />
                                ثانيه
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="startDate" value="{{ \Carbon\Carbon::parse(auth()->user()->election?->start_date)->format('Y-m-d') }}">
                <input type="hidden" id="startTime" value="{{ \Carbon\Carbon::parse(auth()->user()->election?->start_time)->format('H:i:s') }}">
                <input type="hidden" id="endDate" value="{{ \Carbon\Carbon::parse(auth()->user()->election?->end_date)->format('Y-m-d') }}">
                <input type="hidden" id="endTime" value="{{ \Carbon\Carbon::parse(auth()->user()->election?->end_time)->format('H:i:s') }}">
                <div id="election_start">
                    <p class="text-danger">
                        حتى تاريخ {{ \Carbon\Carbon::parse(auth()->user()->election?->start_date)->format('d/m/Y') }} , الساعة
                        {{ \Carbon\Carbon::parse(auth()->user()->election?->start_time)->format('h:i') }}
                        {{ \Carbon\Carbon::parse(auth()->user()->election?->start_time)->format('A') === 'AM' ? 'ص' : 'م' }}
                    </p>
                </div>
                <div id="election_end" class="d-none">
                    <p class="text-danger">
                        حتى تاريخ {{ \Carbon\Carbon::parse(auth()->user()->election?->end_date)->format('d/m/Y') }} , الساعة
                        {{ \Carbon\Carbon::parse(auth()->user()->election?->end_time)->format('h:i') }}
                        {{ \Carbon\Carbon::parse(auth()->user()->election?->end_time)->format('A') === 'AM' ? 'ص' : 'م' }}
                    </p>
                </div>



            </div>

        </div>
    </section>
    <!-- main -->
    <main>

        @if(auth()->user()->can('statement.show') ||
        auth()->user()->can('madameen') ||
        auth()->user()->can('contractors.list') ||
        auth()->user()->can('statement') ||
        auth()->user()->can('statement.search'))
        <div>
            <h1 class="bg-dark text-white py-2 text-center h2 mb-2">المتعهدين والمضامين</h1>
            <div class="container">
                <div class="row g-3 mb-4">
                    @can('statement.show')
                    <div class="col-lg-2 col-md-3 col-sm-6 flex-grow-1">
                        <div class="w-100">
                            <a href="{{route('dashboard.statement.show')}}">
                                <button class="btn w-100 btn-dark">
                                    <i class="fa fs-5 fa-table-cells d-block my-1"></i>
                                    <h6>العرض الشامل</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('madameen')
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.madameen')}}">
                                <button class="btn w-100 btn-info">
                                    <i class="fa fs-5 fa-list-check d-block my-1"></i>
                                    <h6>المضامين</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('contractors.list')
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.contractors.index')}}">
                                <button class="btn w-100 btn-primary">
                                    <i class="fa fs-5 fa-user-check d-block my-1"></i>
                                    <h6>المتعهدين</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('statement')
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.statement')}}">
                                <button class="btn w-100 btn-warning">
                                    <i class="fa fs-5 fa-clipboard-list d-block my-1"></i>
                                    <h6>كشوف</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('statement.search')

                    <div class="col-lg-3 col-md-3 col-sm-6 flex-grow-1">
                        <div class="w-100">
                            <a href="{{route('dashboard.statement.search')}}">
                                <button class="btn w-100 btn-secondary">
                                    <i class="fa fs-5 fa-magnifying-glass d-block my-1"></i>
                                    <h6>البحث عن الكشوف</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @endif

        <div>
            <h1 class="bg-dark text-white py-2 text-center h2 mb-2">تحضير وفرز (لجان الانتخابات)</h1>
            <div class="container">
                <div class="row g-3 mb-4">
                    @can('candidates.list')

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.rep-home')}}">
                                <button class="btn w-100 btn-success">
                                    <i class="fa fs-5 fa-user-shield d-block my-1"></i>
                                    <h6>المندوبين</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('committee.home')

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.committee.home')}}">
                                <button class="btn w-100 btn-danger">
                                    <i class="fa fs-5 fa-receipt d-block my-1"></i>
                                    <h6>اللجان</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can ('sorting')
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.sorting')}}">
                                <button class="btn w-100 btn-info">
                                    <i class="fa fs-5 fa-rectangle-list d-block my-1"></i>
                                    <h6>الفرز</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                    @can('attending')

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.attending')}}">
                                <button class="btn w-100 btn-primary">
                                    <i class="fa fs-5 fa-clipboard-check d-block my-1"></i>
                                    <h6>التحضير</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan

                </div>
                
                <div class="row g-3 mb-4">
                    <?php if(isset($show_all_result) && $show_all_result==true){?>
                        <div class="col-lg-4 col-md-3 col-sm-6">
                            <div class="w-100">
                                <a href="{{route('all.results')}}">
                                    <button class="btn w-100 btn-secondary">
                                        <i class="fa fs-5 fa-chart-simple d-block my-1"></i>
                                        <h6>النتائج العامة</h6>
                                    </button>
                                </a>
                            </div>
                        </div>
                    <?php }?>
                    @can ('candidates.index')
                    <div class="col-lg-4 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.results')}}">
                                <button class="btn w-100 btn-dark">
                                    <i class="fa fs-5 fa-chart-simple d-block my-1"></i>
                                    <h6>النتائج</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan

                    @can('statistics')
                    <div class="col-lg-4 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.statistics')}}">
                                <button class="btn w-100 btn-warning">
                                    <i class="fa fs-5 fa-list-check d-block my-1"></i>
                                    <h6>احصائيات</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan

                    @can('reports.create')
                    <div class="col-lg-4 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.reports.create')}}">
                                <button class="btn w-100 btn-primary">
                                    <i class="fa fs-5 fa-receipt d-block my-1"></i>
                                    <h6>التقارير</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @if ( auth()->user()->hasRole('Administrator'))
        <div>
            <h1 class="bg-dark text-white py-2 text-center h2 mb-2">أدوات الموقع</h1>
            <div class="container">
                <div class="row g-3 mb-4 align-items-center justify-content-center">
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.delete')}}">
                                <button class="btn w-100 btn-outline-danger">
                                    <i class="fa fs-5 fa-receipt d-block my-1"></i>
                                    <h6>المحذوفين</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.history')}}">
                                <button class="btn w-100 btn-secondary">
                                    <i class="fa fs-5 fa-list-check d-block my-1"></i>
                                    <h6>سجل الموقع</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="w-100">
                            <a href="">
                                <button class="btn w-100 btn-success">
                                    <i class="fa-brands fs-5 fa-whatsapp d-block my-1"></i>
                                    <h6>whatsapp</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="w-100">
                            <a href="{{route('dashboard.cards',$data=null)}}">
                                <button class="btn w-100 btn-dark">
                                    <i class="fa fs-5 fa-gear d-block my-1"></i>
                                    <h6>الكوادر واعضاء اللجان</h6>
                                </button>
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div>
            <h1 class="bg-dark text-white py-2 text-center h2 mb-2">المتواجدين الأن</h1>

            <div class="table-responsive mb-5">
                <table class="table table-striped w-100  text-center rtl overflow-scroll">
                    <thead class="table-primary  border-0 border-dark border-bottom border-2">
                        <tr class="py-4 ">
                            <th>الاسم</th>
                            <th>أخر ظهور</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider" id="user-list">
                        @php
                        $users=App\Models\User::where('creator_id', auth()->user()->id)->take(10)->get();
                        @endphp
                        @foreach ( $users as $user )
                        @if ($user->isOnline() || $user->isOffline())
                        <tr>
                            <td>
                                <i class="fa fa-circle rounded-circle @if ($user->isOnline())
                                            online text-success
                                            @else
                                            text-danger
                                        @endif ms-1"></i>
                                {{$user->name}}
                            </td>
                            <td>
                                @if ($user->isOffline())
                                {{$user->LoginTime($user->last_active_at)}}
                                @endif
                            </td>
                        </tr>

                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <button id="load-more" data-page="1" class="btn btn-primary">المزيد</button>
            </div>
        </div>
        @endif

        @can('import-voters')
        <div class="container">
            <div class="import-card p-0">
                <div class="import-header p-4">
                    <h2 class="h4 mb-2">استيراد الناخبين</h2>
                    <p class="import-help mb-0">استخدم هذا النموذج لرفع ملف الناخبين حسب القالب المعتمد. جميع الحقول موضحة بالأسفل.</p>
                </div>
                <div class="p-4">
                    <x-dashboard.partials.message-alert />

                    <div class="import-warning mb-4" role="alert">
                        <strong>تنبيه:</strong> خيار <strong>استبدال البيانات</strong> يحذف البيانات القديمة قبل الاستيراد. استخدمه فقط عند الحاجة.
                    </div>

                    <form id="voters-import-form" action="{{ route('dashboard.import-voters') }}" class="row g-4" enctype="multipart/form-data" method="POST" novalidate>
                        @csrf
                        <div class="col-12 col-lg-6">
                            <label for="election" class="form-label">الانتخابات</label>
                            @php
                            $elections=App\Models\Election::select('id','name')->get();
                            @endphp
                            <select name="election" id="election" class="form-select import-field" required aria-describedby="electionHelp">
                                <option value="" selected disabled>اختر الانتخابات</option>
                                @foreach ($elections as $election )
                                <option value="{{$election->id}}"> {{$election->name . "(".$election->id .")" }} </option>
                                @endforeach
                            </select>
                            <div id="electionHelp" class="import-help">اختر الانتخابات المرتبطة بالملف الذي سترفعه.</div>
                            <div class="import-error d-none" id="electionError">يرجى اختيار الانتخابات.</div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="import" class="form-label">ملف الاستيراد</label>
                            <input type="file" class="form-control import-field" id="import" name="import" accept=".xlsx,.xls,.csv" required aria-describedby="fileHelp">
                            <div id="fileHelp" class="import-help">الصيغ المقبولة: .xlsx, .xls, .csv</div>
                            <div class="import-error d-none" id="fileError">يرجى اختيار ملف صالح.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">طريقة الاستيراد</label>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="import-option d-block" for="dublicate">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="option-title">إضافة بيانات جديدة فقط</div>
                                                <div class="option-desc">يضيف السجلات الجديدة دون حذف البيانات الحالية.</div>
                                            </div>
                                            <input type="radio" id="dublicate" name="check" value="dublicate" aria-describedby="importModeHelp">
                                        </div>
                                    </label>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="import-option option-danger d-block" for="replace">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="option-title">استبدال البيانات</div>
                                                <div class="option-desc">يحذف البيانات القديمة أولاً ثم يستورد الملف الجديد.</div>
                                            </div>
                                            <input type="radio" id="replace" name="check" value="replace" aria-describedby="replaceHelp">
                                        </div>
                                    </label>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="import-option d-block" for="status">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="option-title">تحديث الحالة</div>
                                                <div class="option-desc">يحدّث حالة الحضور حسب الملف دون استيراد كامل البيانات.</div>
                                            </div>
                                            <input type="radio" id="status" name="check" value="status" aria-describedby="importModeHelp">
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div id="importModeHelp" class="import-help mt-2">اختر طريقة الاستيراد المناسبة لملفك.</div>
                            <div id="replaceHelp" class="import-help mt-1">تحذير: هذا الخيار يمسح البيانات الحالية.</div>
                            <div class="import-error d-none" id="modeError">يرجى اختيار طريقة الاستيراد.</div>
                        </div>

                        <div class="col-12 d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center">
                            <div class="import-help">تأكد من توافق الملف مع القالب قبل الإرسال.</div>
                            <button type="submit" class="btn btn-custom px-4">بدء الاستيراد</button>
                        </div>
                    </form>
                </div>
            </div>
</br>
        <div class="modal fade" id="replaceConfirmModal" tabindex="-1" aria-labelledby="replaceConfirmLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="replaceConfirmLabel">تأكيد استبدال البيانات</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        هذا الخيار سيحذف البيانات القديمة قبل استيراد الملف الجديد. هل تريد المتابعة؟
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-danger" id="confirmReplace">نعم، استبدل البيانات</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
        @endcan
    </main>
</div>
@endsection
@push('js')
<script>
    $('#load-more').on('click', function() {
        var page = $(this).data('page');
        page++;

        $.ajax({
            url: '/get-users',
            method: 'GET',
            data: {
                page: page
            },
            success: function(response) {
                if (response.data.length > 0) {
                    response.data.forEach(function(user) {
                        if (user.is_online || user.is_offline) {
                            var row = `
                        <tr>
                            <td>
                                <i class="fa fa-circle rounded-circle ${user.is_online ? 'online text-success' : 'text-danger'} ms-1"></i>
                                ${user.name}
                            </td>
                            <td>
                                ${user.is_offline ? user.last_active_at : ''}
                            </td>
                        </tr>
                    `;

                            $('#user-list').append(row);

                            setTimeout(function() {
                                $('#user-list tr:last-child').css({
                                    'opacity': 1,
                                    'transform': 'translateY(0)',
                                    'transition': 'all 0.5s ease'
                                });
                            }, 100);
                        }
                    });

                    $('#load-more').data('page', page);
                } else {
                    $('#load-more').hide();
                }
            }
        });
    });

    setInterval(function() {
        $.ajax({
            url: '/users/online',
            method: 'GET',
            data: {
                page: $('#load-more').data('page')
            },
            success: function(response) {
                console.log(response);

                $('#user-list tr').css({
                    'opacity': 0,
                    'transform': 'translateY(20px)',
                    'transition': 'all 0.5s ease'
                });
                $('#user-list').html('');


                response.data.forEach(function(user) {
                    if (user.is_online || user.is_offline) {
                        var row = `
                        <tr>
                            <td>
                                <i class="fa fa-circle rounded-circle ${user.is_online ? 'online text-success' : 'text-danger'} ms-1"></i>
                                ${user.name}
                            </td>
                            <td>
                                ${user.is_offline ? user.last_active_at : ''}
                            </td>
                        </tr>
                    `;

                        $('#user-list').append(row);
                        $('#load-more').data('page', 1);

                        setTimeout(function() {
                            $('#user-list tr:last-child').css({
                                'opacity': 1,
                                'transform': 'translateY(0)',
                                'transition': 'all 0.5s ease'
                            });
                        }, 100);
                    }
                });
            }
        });
    }, 120000);

    const importForm = document.getElementById('voters-import-form');
    if (importForm) {
        const electionField = document.getElementById('election');
        const fileField = document.getElementById('import');
        const modeFields = importForm.querySelectorAll('input[name="check"]');
        const replaceField = document.getElementById('replace');
        const replaceModalElement = document.getElementById('replaceConfirmModal');
        const confirmReplaceButton = document.getElementById('confirmReplace');
        const replaceModal = replaceModalElement ? new bootstrap.Modal(replaceModalElement) : null;
        let replaceConfirmed = false;

        const electionError = document.getElementById('electionError');
        const fileError = document.getElementById('fileError');
        const modeError = document.getElementById('modeError');

        const setFieldState = (field, isValid) => {
            field.classList.remove('is-valid', 'is-invalid');
            field.classList.add(isValid ? 'is-valid' : 'is-invalid');
        };

        const toggleError = (node, show) => {
            if (!node) return;
            node.classList.toggle('d-none', !show);
        };

        const validateImportForm = () => {
            let valid = true;

            if (!electionField.value) {
                setFieldState(electionField, false);
                toggleError(electionError, true);
                valid = false;
            } else {
                setFieldState(electionField, true);
                toggleError(electionError, false);
            }

            if (!fileField.files.length) {
                setFieldState(fileField, false);
                toggleError(fileError, true);
                valid = false;
            } else {
                const validExt = /\.(xlsx|xls|csv)$/i.test(fileField.files[0].name);
                setFieldState(fileField, validExt);
                toggleError(fileError, !validExt);
                valid = valid && validExt;
            }

            const modeSelected = Array.from(modeFields).some((field) => field.checked);
            toggleError(modeError, !modeSelected);
            if (!modeSelected) {
                valid = false;
            }

            return valid;
        };

        importForm.addEventListener('submit', function(event) {
            if (!validateImportForm()) {
                event.preventDefault();
                return;
            }

            if (replaceField && replaceField.checked && !replaceConfirmed && replaceModal) {
                event.preventDefault();
                replaceModal.show();
            }
        });

        if (confirmReplaceButton) {
            confirmReplaceButton.addEventListener('click', function() {
                replaceConfirmed = true;
                if (replaceModal) {
                    replaceModal.hide();
                }
                importForm.submit();
            });
        }

        electionField.addEventListener('change', validateImportForm);
        fileField.addEventListener('change', validateImportForm);
        modeFields.forEach((field) => field.addEventListener('change', validateImportForm));
    }
</script>


@endpush