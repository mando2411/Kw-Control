@extends('layouts.dashboard.app')

@section('content')
    <style>
        .loader, .loader:before, .loader:after {
            border-radius: 50%;
            width: 2.5em;
            height: 2.5em;
            animation-fill-mode: both;
            animation: bblFadInOut 1.8s infinite ease-in-out;
        }
        .loader {
            color: #6a7bb4;
            font-size: 7px;
            position: relative;
            text-indent: -9999em;
            transform: translateZ(0);
            animation-delay: -0.16s;
        }
        .loader:before,
        .loader:after {
            content: '';
            position: absolute;
            top: 0;
        }
        .loader:before {
            left: -3.5em;
            animation-delay: -0.32s;
        }
        .loader:after {
            left: 3.5em;
        }

        @keyframes bblFadInOut {
            0%, 80%, 100% { box-shadow: 0 2.5em 0 -1.3em }
            40% { box-shadow: 0 2.5em 0 0 }
        }

        .yellow-background  {
            background-color: #d4d484;
        }
 

    </style>


    <x-dashboard.partials.message-alert />

    <section class="rtl">
        <div class="container">
            <div class="madameenControl">
                <form action="{{ route('dashboard.statement.query') }}" class="Query-Form" method="GET" autocomplete="off">

                    <!-- First Name Input -->
                    <div class="d-flex align-items-center">
                        <label class="labelStyle" for="firstNameSearch">الأسم الاول</label>
                        <input
                            type="text"
                            name="first_name"
                            id="firstNameSearch"
                            autocomplete="off"
                            value=""
                            class="form-control py-1"
                            placeholder="حصر النتائج حسب الاسم الاول"
                        />
                    </div>

                    <!-- Name Input -->
                    <div class="d-flex align-items-center">
                        <label class="labelStyle" for="mandoobNameSearch">الأسم</label>
                        <input
                            type="text"
                            name="name"
                            id="mandoobNameSearch"
                            autocomplete="off"
                            value=""
                            class="form-control py-1"
                            placeholder="البحث عن أي جزء من الأسم"
                        />
                    </div>

                    <!-- Family Selection -->
                    <div class="d-flex align-items-center">
                        <label class="labelStyle" for="familySearch">العائلة</label>
                        <select name="family" id="familySearch" class="form-control py-1 js-example-basic-single">
                            <option value="" hidden>العائلة...</option>
                            <option value=""> -- </option>
                            @foreach ($relations['families'] as $family)
                                <option value="{{ $family->id }}" @if (request('family')==$family->id)
                                    selected
                                @endif>{{ $family->name }} ({{ $family->voters->count() }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Contact Information Inputs -->
                    <div class="d-flex align-items-center">
                        {{-- <label class="labelStyle" for="contactInfo">معلومات الاتصال</label> --}}
                        <label class="visually-hidden" for="phoneSearch">الهاتف</label>
                        <input
                            type="text"
                            class="form-control py-1"
                            name="phone"
                            id="phoneSearch"
                            autocomplete="off"
                            value=""
                            placeholder="الهاتف"
                        />
                        <label class="visually-hidden" for="idSearch">الرقم المدني</label>
                        <input
                            type="text"
                            class="form-control py-1"
                            name="id"
                            id="idSearch"
                            autocomplete="off"
                            value=""
                            placeholder="الرقم المدني"
                        />
                        <label class="visually-hidden" for="boxSearch">الصندوق</label>
                        <input
                            type="text"
                            class="form-control py-1"
                            name="box"
                            id="boxSearch"
                            autocomplete="off"
                            value=""
                            placeholder="الصندوق"
                        />
                    </div>

                    <!-- More Search Options -->
                    <div class="moreSearch my-2">
                        <div role="button" class="btn btn-primary w-100">
                            خيارات أكثر للبحث
                        </div>
                        <div class="description">
                            <!-- Additional Name Inputs -->
                            <div class="d-flex">
                                <input
                                    type="text"
                                    class="form-control py-1"
                                    placeholder="الأسم الثاني"
                                    name="second_name"
                                    id="secondName"
                                    autocomplete="off"
                                />
                                <input
                                    type="text"
                                    class="form-control py-1"
                                    placeholder="الأسم الثالث"
                                    name="third_name"
                                    id="thirdName"
                                    autocomplete="off"
                                />
                                <input
                                    type="text"
                                    class="form-control py-1"
                                    placeholder="الأسم الرابع"
                                    name="fourth_name"
                                    id="fourthName"
                                    autocomplete="off"
                                />
                            </div>

                            <!-- Selection Inputs -->
                            <div class="d-flex">
                                <select name="elfa5z" id="alfkhd" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>الفخذ ...</option>
                                    <option value=""> -- </option>
                                    @foreach ($selectionData['alfkhd'] as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>

                                <select name="elfar3" id="alfraa" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>الفرع ...</option>
                                    <option value=""> -- </option>
                                    @foreach ($selectionData['alfraa'] as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>

                                <select name="elbtn" id="albtn" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>البطن ...</option>
                                    <option value=""> -- </option>
                                    @foreach ($selectionData['albtn'] as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Custom Codes -->
                            <div class="d-flex">
                                <label class="labelStyle mb-0">كود مخصص</label>
                                <select name="cod1" id="code1" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>code1 ...</option>
                                    <option value=""> -- </option>
                                    @foreach ($selectionData['cod1'] as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>

                                <select name="cod2" id="code2" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>code2 ...</option>
                                    <option value=""> -- </option>
                                    @foreach ($selectionData['cod2'] as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>

                                <select name="cod3" id="code3" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>code3 ...</option>
                                    <option value=""> -- </option>
                                    @foreach ($selectionData['cod3'] as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Address Details -->
                            <div class="d-flex">
                                <select name="alktaa" id="alktaa" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>القطعة ...</option>
                                    <option value=""> -- </option>
                                    @foreach ($selectionData['alktaa'] as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>

                                <select name="street" id="street" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>الشارع ...</option>
                                    <option value=""> -- </option>
                                    @foreach (App\Models\Selection::select('street')->whereNotNull('street')->distinct()->get() as $c)
                                        <option value="{{ $c->street }}">{{ $c->street }}</option>
                                    @endforeach
                                </select>

                                <select name="alharaa" id="alharaa" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>الجادة ...</option>
                                    <option value=""> -- </option>
                                    @foreach (App\Models\Selection::select('alharaa')->whereNotNull('alharaa')->distinct()->get() as $c)
                                        <option value="{{ $c->alharaa }}">{{ $c->alharaa }}</option>
                                    @endforeach
                                </select>

                                <select name="home" id="home" class="form-control py-1 js-example-basic-single">
                                    <option value="" hidden>المنزل ...</option>
                                    <option value=""> -- </option>
                                    @foreach (App\Models\Selection::select('home')->whereNotNull('home')->distinct()->get() as $c)
                                        <option value="{{ $c->home }}">{{ $c->home }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <!-- Additional Filters -->
                        <div class="d-flex">
                            <select
                                name="restricted"
                                id="restricted"
                                class="form-control py-1 ms-1 js-example-basic-single"
                            >
                                <option value="">الكل</option>
                                <option value="فعال">مقيد</option>
                                <option value="غير مقيد">غير مقيد</option>
                            </select>
                            <input
                                type="text"
                                class="form-control py-1 ms-1"
                                placeholder="العمر من"
                                name="age[from]"
                            />
                            <input
                                type="text"
                                class="form-control py-1 ms-1"
                                placeholder="العمر إلى"
                                name="age[to]"
                            />
                            <select
                                name="type"
                                id="searchByType"
                                class="form-control py-1 ms-1 js-example-basic-single"
                            >
                                <option value="all">الكل</option>
                                <option value="ذكر">ذكر</option>
                                <option value="أنثى">أنثى</option>
                            </select>
                        </div>
                    </div>

                    <!-- Search Options -->
                    <div class="row">
                        <div class="col-4 ps-0">
                            <div class="d-flex align-items-center">
                                <select name="search" id="bigSearch" class="form-control py-1 ms-1">
                                    <option value="">بحث موسع</option>
                                    <option value="1">فقط المضامين</option>
                                    <option value="0">من غير المضامين</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4 px-0">
                            <div class="d-flex align-items-center">
                                <label class="labelStyle mb-0" for="committeSearch">اللجنة</label>
                                <select
                                    name="committee"
                                    id="committeSearch"
                                    class="form-control py-1 js-example-basic-single"
                                >
                                    <option value="">كل اللجان</option>
                                    @foreach ($relations['committees'] as $com)
                                        <option value="{{ $com->id }}">{{ $com->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-4 pe-0">
                            <div class="d-flex align-items-center">
                                <label class="labelStyle mb-0" for="statusSearch">الحالة</label>
                                <select
                                    name="status"
                                    id="statusSearch"
                                    class="form-control py-1"
                                >
                                    <option value="">الكل</option>
                                    <option value="1">حضر الانتخابات</option>
                                    <option value="0">لم يحضر الانتخابات</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button and Settings Dropdown -->
                    <div class="d-flex mt-2">
                        <button type="submit" class="btn btn-secondary flex-grow-1">بحث</button>
                        <div class="dropdown me-1">
                            <select id="customSelect" name="search_limit" class="form-select dropdown-toggle ms-2 yellow-background" aria-expanded="false">
                                <option selected disabled value=""> حدود البحث</option>
                                <option value="1000">حداقصي 1000</option>
                                <option value="2000">حداقصي 2000</option>
                                <option value="3000">حداقصي 3000</option>
                                <option value="5000">حداقصي 5000</option>
                                <option value="10000">حداقصي 10000</option>
                                <option value="15000">حداقصي 15000</option>
                                <option value="30000">حداقصي 30000</option>
                                <option value="50000">حداقصي 50000</option>
                                <option value="100000">حداقصي 100000</option>
                            </select>                    </div>
                    </div>

                </form>
            </div>
        </div> <!-- Closing the container div -->
    </section>

    <!-- search name setting-->
    <div
        class="modal rtl"
        id="settingSearchName"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">بحث بالأسم</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <form class="my-3">
                        <div class="d-flex align-items-center mb-1 mb-3">
                            <label class="labelStyle" class="" for="nameMandoob">الاسم </label>
                            <input
                                type="text"
                                class="form-control py-1"
                                name="nameMandoob"
                                id="nameMandoob"
                                value=""
                            />
                        </div>
                        <div class="d-flex align-items-center mb-1 mb-3">
                            <label class="labelStyle" class="" for="phoneMandoob"> الهاتف </label>
                            <input
                                type="text"
                                class="form-control py-1"
                                name="phoneMandoob"
                                id="phoneMandoob"
                                value=""
                                min="0"
                            />
                        </div>
                        <div class="d-flex align-items-center mb-1 mb-3">
                            <label class="labelStyle" class="" for="committeMandoob">اللجنة </label>
                            <select
                                name="committeMandoob"
                                id="committeMandoob"
                                class="form-control py-1"
                            >
                                <option value="">
                                    خيطان الجديدة - ثانوية أبرق خيطان للبنات
                                </option>
                                <option value="">
                                    خيطان الجديدة - مدرسة أبرق خيطان المتوسطة للبنات
                                </option>
                                <option value="">
                                    مدرسة علي عبدالمحسن الصقلاوي الابتدائية للبنين
                                </option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100">تعديل</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning">
                        اعادة تعيين الرقم السرى
                    </button>
                    <button type="button" class="btn btn-danger">حذف المستخدم</button>
                </div>
            </div>
        </div>
    </div>




        <section class="pb-5 pt-2 rtl d-none" id="Query-Table">
            <div class="container">
                <div class="d-flex align-items-center mb-1 flex-wrap">
                    <div class="flex-grow-1 ms-2">
                        <button data-bs-toggle="modal" data-bs-target="#elkshoofDetails"  class="btn btn-info w-100">استخراج كشوف</button>
                    </div>
                    <form action="" method="POST" id="form-attach">
                        <div class="d-flex align-items-center mb-1 flex-grow-1 ">
                            @csrf
                            <label class="labelStyle" for="addMota3ahed">اضافة متعهد</label>
                            <select class="form-control py-1 ms-2" name="addMota3ahed" id="addMota3ahed">
                                <option value=""  hidden>اختر المتعهد الفرعى</option>
                                @foreach ($relations['contractors'] as $con )
                                    <option value="{{$con->id}}">{{$con->user()->name}} </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary" id="all_voters">اضافة</button>

                        </div>
                    </form>
                </div>

                <div class="mainTable table-responsive mt-4">
                    <table
                        class="table rtl overflow-hidden rounded-3 text-center"
                    >
                        <thead
                            class="table-secondary border-0 border-secondary border-bottom border-2"
                        >
                        <tr >
                            <th>
                                <button class="btn btn-secondary all">الكل</button>
                            </th>
                            <th class="">الأسماء
                                <span

                                    class="namesListCounter listNumber bg-dark text-white rounded-2 p-1 px-3 me-2"
                                    id="search_count" style="color:#fff !important" >

                                </span
                                >
                            </th>
                            <th>العائلة</th>
                            <th class=""></th>
                        </tr>
                        </thead>
                        <tbody id="voter-list">


                        </tbody>
                    </table>
                    <!-- Load More Button -->



                </div>
            </div>
        </section>

        <!-- Modal elkshoofDetails-->
        <div
            class="modal modal-md rtl"
            id="elkshoofDetails"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header py-1">
                        <h1 class="modal-title" id="exampleModalLabel">
                            بيانات الناخبين
                        </h1>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body px-4">
                        <form action="{{ route('export') }}" method="GET" id="export">
                            <input type="hidden" name="search_id" id="search_id" value="">


                            <!-- Start of Checkboxes -->
                            <div class="d-flex flex-row flex-wrap">
                                <!-- First Column -->
                                <div class="d-flex flex-column mx-4">
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            checked
                                            disabled
                                            type="checkbox"
                                            name="columns[]"
                                            value="name"
                                            id="na5ebName"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebName">اسم الناخب</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            checked
                                            type="checkbox"
                                            name="columns[]"
                                            value="family"
                                            id="na5ebFamilyName"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebFamilyName">العائلة</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            type="checkbox"
                                            name="columns[]"
                                            value="age"
                                            id="na5ebAge"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebAge">العمر</label>
                                        <select name="na5ebAgeOption" class="border-0 p-1 ms-2">
                                            <option value="الميلاد / العمر">الميلاد / العمر</option>
                                            <option value="العمر">العمر</option>
                                            <option value="سنة الميلاد">سنة الميلاد</option>
                                            <option value="تاريخ الميلاد">تاريخ الميلاد</option>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            type="checkbox"
                                            name="columns[]"
                                            value="phone"
                                            id="na5ebPhone"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebPhone">الهاتف</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            type="checkbox"
                                            name="columns[]"
                                            value="type"
                                            id="na5ebType"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebType">الجنس</label>
                                    </div>
                                </div>

                                <!-- Second Column -->
                                <div class="d-flex flex-column mx-4">
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            type="checkbox"
                                            name="columns[]"
                                            value="madrasa"
                                            id="na5ebSchool"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebSchool">مدرسة الانتخاب</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            checked
                                            type="checkbox"
                                            name="columns[]"
                                            value="restricted"
                                            id="na5ebRegisterStatus"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebRegisterStatus">حالة القيد</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            type="checkbox"
                                            name="columns[]"
                                            value="created_at"
                                            id="na5ebRegisterDate"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebRegisterDate">تاريخ القيد</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            type="checkbox"
                                            name="columns[]"
                                            value="checked_time"
                                            id="na5ebCheckedTime"
                                        >
                                        <label class="labelStyle ms-2" for="na5ebCheckedTime">وقت التصويت</label>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Checkboxes -->

                            <hr>

                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle me-2" for="sorted">ترتيب</label>
                                <select name="sorted" id="sorted" class="form-control py-1">
                                    <option value="asc">أبجدي</option>
                                    <option value="phone">الهاتف</option>
                                    <option value="commitment">الالتزام</option>
                                </select>
                            </div>
                            <input type="hidden" name="type" id="type">

                            <div class="rtl my-4 text-center">
                                <button type="button" class="btn btn-primary button" value="PDF">PDF</button>
                                <button type="button" class="btn btn-success button" value="Excel">Excel</button>
                                <button type="button" class="btn btn-secondary button" value="print">طباعة</button>
                                <button type="button" class="btn btn-secondary button" value="show">عرض</button>
                            </div>

                            <p class="text-danger">
                                * ملاحظة: لا يمكن استخراج البيانات الضخمة عبر ملف PDF
                            </p>

                            <div class="d-flex flex-column mb-1 pdfe">
                                <label class="labelStyle w-50" for="sendToNa5eb">
                                    ارسال PDF عبر WhatsApp
                                </label>
                                <div class="d-flex">
                                    <input
                                        type="number"
                                        class="form-control py-1"
                                        name="to"
                                        id="sendToNa5eb"
                                        placeholder="رقم الهاتف"
                                    >
                                    <button
                                        type="button"
                                        class="btn btn-primary me-2 button"
                                        value="Send"
                                    >
                                        ارسال
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- Modal voterData-->
        <div
            class="modal modal-md rtl"
            id="voterData"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header py-1">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            بيانات الناخب
                        </h1>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center mb-1 mb-3">
                            <label class="labelStyle" class="" for="id">الرقم المدنى</label>
                            <input
                                type="text"
                                readonly
                                class="form-control py-1"
                                name="id"
                                id="id"
                            />
                        </div>
                        <div class="d-flex align-items-center mb-1 mb-3">
                            <label class="labelStyle" class="" for="name">الاسم الكامل </label>
                            <input
                                type="text"
                                readonly
                                class="form-control py-1"
                                name="name"
                                id="name"
                                value=""
                            />
                        </div>
                        <div class="d-flex align-items-center mb-1 mb-3">

                            <div class="d-flex align-items-center mb-1 w-25 me-2">
                                <label class="labelStyle" class="" for="family">العائلة </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="family"
                                    id="family"
                                    value=""
                                />
                            </div>
                        </div>
                        <hr />
                        <div class="d-flex align-items-center mb-1 mb-3">
                            <label class="labelStyle" class="" for="phone1"> الهاتف </label>
                            <input
                                type="text"
                                readonly
                                class="form-control py-1"
                                name="phone1"
                                id="phone1"
                                value="144254435"
                                min="0"
                            />
                            <a href="" id="phone1-call" class="d-inline-block px-4 text-white bg-primary"
                            ><i class="pt-2 fs-5 fa fa-phone"></i
                                ></a>
                            <a href="" id="phone1-wa" class="d-inline-block px-4 text-white bg-success"
                            ><i class="pt-2 fs-5 fa-brands fa-whatsapp"></i
                                ></a>
                        </div>
                        <div class="d-flex align-items-center mb-1 mb-3">
                            <label class="labelStyle" class="" for="phone2"> الهاتف </label>
                            <input
                                type="text"
                                readonly
                                class="form-control py-1"
                                name="phone2"
                                id="phone2"
                            />
                            <a href="" id="phone2-call" class="d-inline-block px-4 text-white bg-primary"
                            ><i class="pt-2 fs-5 fa fa-phone"></i
                                ></a>
                            <a href="" id="phone2-wa" class="d-inline-block px-4 text-white bg-success"
                            ><i class="pt-2 fs-5 fa-brands fa-whatsapp"></i
                                ></a>
                        </div>
                        <hr />
                        <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" for="alfkhdd">فخذ </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="alfkhd"
                                    id="alfkhdd"
                                    value=""
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" for="alfraaa">فرع </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="alfraaa"
                                    id="alfraaa"
                                    value=""
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" class="" for="albtn">بطن </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="albtnn"
                                    id="albtnn"
                                    value=""
                                />
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" class="" for="codd1">كود1 </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="codd1"
                                    id="codd1"
                                    value=""
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" class="" for="codd2">كود2 </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="codd2"
                                    id="codd2"
                                    value=""
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" class="" for="codd3">كود3 </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="codd3"
                                    id="codd3"
                                    value=""
                                />
                            </div>
                        </div>
                        <hr />

                        <div class="d-flex align-items-center mb-1 mb-3">
                            <div class="d-flex align-items-center mb-1 w-50">
                                <label class="labelStyle" class="" for="type">الجنس</label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="type"
                                    id="typeee"
                                    value=""
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1 w-50">
                                <label class="labelStyle" class="" for="work"> المهنة </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="work"
                                    id="work"
                                    value=""
                                />
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-1 mb-3">
                            <div class="d-flex align-items-center mb-1 w-50">
                                <label class="labelStyle" class="" for="yearOfBirth">الميلاد</label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="yearOfBirth"
                                    id="yearOfBirth"
                                    value=""
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1 w-50">
                                <label class="labelStyle" class="" for="age"> العمر </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="age"
                                    id="age"
                                    value=""
                                />
                            </div>
                        </div>

                        <div class="d-flex mb-1 align-items-center">
                            <label class="labelStyle" class="" for="address">العنوان</label>
                            <input
                                type="text"
                                readonly
                                class="form-control py-1"
                                name="address"
                                id="address"
                                value=""
                            />
                        </div>
                        <hr />

                        <div class="d-flex align-items-center mb-1 mb-3">
                            <div class="d-flex align-items-center mb-1 w-50">
                                <label class="labelStyle" class="" for="circle">الدائرة</label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="circle"
                                    id="circle"
                                    value="6"
                                    min="1"
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1 w-50">
                                <label class="labelStyle" class="" for="almrgaa"> المرجع </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="almrgaa"
                                    id="almrgaaa"
                                    value=""
                                    min="0"
                                />
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" class="" for="character">حرف </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="character"
                                    id="character"
                                    value=""
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" class="" for="userTable">جدول </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="userTable"
                                    id="userTable"
                                    value=""
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" class="" for="registe">قيد </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="registe"
                                    id="registe"
                                    value=""
                                />
                            </div>
                        </div>
                        <div
                            class="d-flex mb-1 align-items-center justify-content-between"
                        >
                            <label class="labelStyle" class="" for="committee">لجنة</label>
                            <input
                                type="text"
                                readonly
                                class="form-control py-1 w-25 ms-2"
                                name="committee"
                                id="committee"
                                value="19"
                                min="1"
                            />
                            <input
                                type="text"
                                readonly
                                class="form-control py-1 w-75"
                                name="school"
                                id="school"
                                value="مدرسة الفردوس المتوسطة بنات"
                            />
                        </div>

                        <div
                            class="d-flex mb-1 align-items-center justify-content-between d-none"
                            id="div_attend"
                        >
                            <label class="labelStyle" class="" for="attendance">تفاصيل التصويت</label>
                            <input
                                type="text"
                                readonly
                                class="form-control py-1 w-100 ms-2"
                                name="attendance"
                                id="attendance"
                            />
                        </div>
                        <div class="d-flex align-items-center mb-1 mb-3">
                            <div class="d-flex align-items-center mb-1 w-50">
                                <label class="labelStyle" class="" for="registeNumber">تاريخ القيد</label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="registeNumber"
                                    id="registeNumber"
                                    value="6"
                                    min="1"
                                />
                            </div>
                            <div class="d-flex align-items-center mb-1 w-50">
                                <label class="labelStyle" class="" for="lastCircle"> الدائرة السابقة </label>
                                <input
                                    type="text"
                                    readonly
                                    class="form-control py-1"
                                    name="lastCircle"
                                    id="lastCircle"
                                    value="4651365463"
                                    min="0"
                                />
                            </div>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table overflow-hidden rtl">
                                <thead
                                    class="table-secondary text-center border-0 border-dark border-bottom border-2"
                                >
                                <tr>
                                    <th>المتعهدين بالناخب</th>
                                    <th>مدخلات المتعهد بالناخب</th>
                                </tr>
                                </thead>
                                <tbody class="table-group-divider" id="mot3deen_table">


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@push('js')
    <script>
        
        $(document).ready(function () {
            let voters = [];        // Will hold all voter data
            let currentChunkIndex = 0;
            const chunkSize = 2000; // Adjust based on performance
            let loadingAllComplete = false;

            // Create overlay if it doesn't exist
            if ($("#loading-overlay").length === 0) {
                $("body").append(`
          <div id="loading-overlay" style="
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.3);
            z-index: 2000;
            display: none;
          "></div>
        `);
            }

            // Delegate form submission for dynamically added forms
            $(document).on("submit", ".Query-Form", function (e) {
                e.preventDefault();

                const form = $(this);
                const actionUrl = form.attr("action");
                const formData = new FormData(this);

                // Convert FormData to object
                const params = {};
                formData.forEach((value, key) => {
                    params[key] = value;
                });

                // Show spinner and overlay immediately on form submit
                showSpinner();
                showOverlay();

                // Send AJAX request
                axios
                    .get(actionUrl, { params })
                    .then(response => {
                        const payload = response.data || {};
                        if (!Array.isArray(payload.voters)) {
                            throw new Error("Invalid data. Expected an array of voters.");
                        }

                        voters = payload.voters;
                        const $voterList = $("#voter-list");
                        $voterList.empty();
                        $("#Query-Table").removeClass("d-none");
                        $("#search_count").text(voters.length).css("color", "#fff");

                        if (!voters.length && payload.message) {
                            toastr.warning(payload.message);
                        }

                        // Reset chunk index and flags
                        currentChunkIndex = 0;
                        loadingAllComplete = false;

                        // Start chunked rendering of all voters
                        renderAllVotersInChunks(voters, $voterList);
                    })
                    .catch(error => {
                        console.error(error);
                        const message = error?.response?.data?.message || "حدث خطأ أثناء تنفيذ البحث.";
                        toastr.error(message);
                        // If an error happens, hide spinner/overlay and enable interaction
                        hideSpinner();
                        hideOverlay();
                    });
            });

            // Render all voters in chunks
            function renderAllVotersInChunks(voters, $voterList) {
                function renderChunk() {
                    if (loadingAllComplete) {
                        // All done
                        hideSpinner();
                        hideOverlay();
                        return;
                    }

                    loadNextChunk(voters, $voterList);

                    // If more chunks remain, request another frame to process next chunk
                    if ((currentChunkIndex * chunkSize) < voters.length) {
                        requestAnimationFrame(renderChunk);
                    } else {
                        // All chunks rendered
                        loadingAllComplete = true;
                        hideSpinner();
                        hideOverlay();
                    }
                }

                // Start rendering asynchronously
                requestAnimationFrame(renderChunk);
            }

            // Load next chunk of voters
            function loadNextChunk(voters, $voterList) {
                const start = currentChunkIndex * chunkSize;
                const end = start + chunkSize;
                const voterChunk = voters.slice(start, end);

                if (voterChunk.length === 0) {
                    loadingAllComplete = true;
                    return;
                }

                voterChunk.forEach((voter, i) => {
                console.log(voter);
                let icon = ""; 
if (voter.contractors.length > 0) {
    icon = `<i class="fa-solid fa-check-double text-success fs-6 mx-3">${voter.contractors.length}</i>`;
}
                
                    $voterList.append(`
                <tr class="${voter.status == 1 ? "table-success" : ""}">
                    <input type="hidden" id="voter_id" value="${voter.id}">
                    <td>
                        <input type="checkbox" class="check" name="voter[]" value="${voter.id}" />
                    </td>
                    <td>
                        <div class="row">
                        <p class="mb-0 fw-bold text-dark ${voter.restricted != 'فعال' ? "line" : ""}">${voter.name} ${icon}</p>
                <span style="color:red"> ${voter.restricted != 'فعال' ? " غير فعال" : ""} </span>

                            </div>
                        <span class="text-secondary">${voter.age} عام</span>
                        ${
                        voter.status == 1
                            ? `<p class="my-1">
                                <span><i class="fa fa-check-square text-success ms-1"></i>تم التصويت </span>
                                <span>${new Date(voter.updated_at).toLocaleDateString("en-CA")}</span>
                              </p>`
                            : ""
                    }
                    </td>
                    <td>${voter.family?.name}</td>
                    <td>
                        <div class="d-flex row justify-content-center align-items-center">
                            <form class="d-flex justify-content-center align-items-center Query-Form"
                                  id="Siblings-form-${start + i}"
                                  action="{{ route('dashboard.statement.query') }}" method="GET">
                                <i data-bs-toggle="modal" data-bs-target="#voterData"
                                   class="cPointer fa fa-info p-2 px-3 rounded-circle bg-warning"
                                   data-target-id="${voter.id}"></i>
                                <select name="siblings" id="siblings-${start + i}"
                                        class="form-control py-1 mt-1 me-2 text-center w60" onchange="Search(this)">
                                    <option value="" disabled>أختر</option>
                                    <option value="" hidden class="">بحث</option>
                                    <option value='${JSON.stringify({ father: voter.father })}'>أقارب من الدرجة الاولى</option>
                                    <option value='${JSON.stringify({ grand: voter.grand })}'>أقارب من الدرجة التانية</option>
                                    <option value="بحث موسع">بحث موسع</option>
                                </select>
                            </form>
                        </div>
                    </td>
                </tr>
            `);
                });

                currentChunkIndex++;
            }

            // Show the spinner
            function showSpinner() {
                if ($("#loading-spinner").length === 0) {
                    $("body").append('<div id="loading-spinner" class="spinner-border text-primary" role="status" style="position: absolute; bottom: 50%; right: 50%; z-index: 2050;"><span class="sr-only">Loading...</span></div>');
                }
                $("#loading-spinner").show();
            }

            // Hide the spinner
            function hideSpinner() {
                $("#loading-spinner").hide();
            }

            // Show the overlay (disables interaction)
            function showOverlay() {
                $("#loading-overlay").show();
                $("body").css("pointer-events", "none");
            }

            // Hide the overlay (enables interaction)
            function hideOverlay() {
                $("#loading-overlay").hide();
                $("body").css("pointer-events", "auto");
            }
        });

    </script>
    <script>
        function Search(value){
            $(value).parent().submit()
        }
    </script>
    <script>
        $("#voter-list").on("click", 'i[data-bs-target="#voterData"]', function () {
            console.log("");

            let id=$(this).attr("data-target-id");
            console.log(id);

            var url = "{{ url('dashboard/voter') }}/" + id;
            axios.get(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (response) {
                    console.log('Success:', response);
                    $("#id").val(response.data.voter.alrkm_almd_yn)
                    $("#name").val(response.data.voter.name)
                    $("#family").val(response.data.family)
                    $("#phone1").val(response.data.voter.phone1)
                    $("#phone2").val(response.data.voter.phone2)
                    $("#alfkhdd").val(response.data.voter.alfkhd)
                    $("#alfraaa").val(response.data.voter.alfraa)
                    $("#albtnn").val(response.data.voter.albtn)
                    $("#codd1").val(response.data.voter.cod1)
                    $("#codd2").val(response.data.voter.cod2)
                    $("#codd3").val(response.data.voter.cod3)
                    $("#typeee").val(response.data.voter.type)
                    $("#almrgaaa").val(response.data.voter.almrgaa)
                    $("#yearOfBirth").val(response.data.voter.yearOfBirth)
                    $("#committee").val(response.data.committee)
                    $("#school").val(response.data.school)
                    if (response.data.attend) {
                        $("#div_attend").removeClass('d-none');
                        let text= "تم التحضير بواسطه" +  " " +response.data.attend.name +  " " + "في" +  " " +response.data.committee;
                        $("#attendance").val(text);
                    }
                    document.getElementById('phone1-call').href = `tel:${response.data.voter.phone1}`;
                    document.getElementById('phone1-wa').href = `https://wa.me/965${response.data.voter.phone1}`;
                    document.getElementById('phone2-call').href = `tel:${response.data.voter.phone2}`;
                    document.getElementById('phone2-wa').href = `https://wa.me/965${response.data.voter.phone2}`;
                    let contractors=response.data.contractors;
                    var cartona = [];
                    for (let i = 0; i < contractors.length; i++) {
                        cartona.push(`
        <tr>
            <td rowspan="3">${contractors[i].user.name}</td>
            <td>الالتزام: ${contractors[i].pivot.percentage ? contractors[i].pivot.percentage : "0"}%</td>
        </tr>
        <tr>
            <td>الهاتف: ${contractors[i].user.phone ? contractors[i].user.phone : "لايوجد"}</td>
        </tr>
        <tr>
            <td>الملاحظات: ${contractors[i].note ? contractors[i].note : "لايوجد" } </td>
        </tr>
    `);
                    }
                    document.getElementById("mot3deen_table").innerHTML = cartona.join('');
                    console.log(contractors.length);

                })
                .catch(function (error) {
                    console.error('Error:', error);
                    alert('Failed to set votes.');
                });


        });
        $("#addMota3ahed").on('change',function(){
            let url = "/ass/"+$(this).val()
            $("#form-attach").attr("action", url )
        })
    </script>

    <script>
        $(document).ready(function () {
            $('.button').on('click', function () {
                // Get the button value and set it in the form
                var buttonValue = $(this).val();
                $('#type').val(buttonValue);

                // Get all checked values
                var checkedElements = $('.check:checked');
                var checkedValues = $.map(checkedElements, function (element) {
                    return $(element).val();
                });

                // Remove any previously added hidden inputs
                $('#export').find('input[name="voter[]"]').remove();

                // Append hidden inputs for all checked values
                checkedValues.forEach(function (value) {
                    $('<input>')
                        .attr({
                            type: 'hidden',
                            name: 'voter[]',
                            value: value,
                        })
                        .appendTo('#export');
                });

                var form = $('#export');
                var submitBtn = $(this);
                submitBtn.attr('disabled', true); // Disable button to prevent duplicate requests

                // Serialize the form data into an array
                var formDataArray = form.serializeArray();

                // Convert the array into an object
                var queryData = {};
                $.each(formDataArray, function (_, field) {
                    if (queryData[field.name]) {
                        if (!Array.isArray(queryData[field.name])) {
                            queryData[field.name] = [queryData[field.name]];
                        }
                        queryData[field.name].push(field.value || '');
                    } else {
                        queryData[field.name] = field.value || '';
                    }
                });

                // Use axios.get with the form data as params
                axios
                    .get(form.attr('action'), {
                        params: queryData, // Pass form data as an object
                        responseType: buttonValue === "Excel" || buttonValue === "PDF" ? 'blob' : 'json',
                    })
                    .then((res) => {
                        if (buttonValue === "Excel" || buttonValue === "PDF") {
                            // Handle file download
                            const url = window.URL.createObjectURL(new Blob([res.data]));
                            const link = document.createElement('a');
                            link.href = url;
                            link.setAttribute('download', buttonValue === "Excel" ? 'Voters.xlsx' : 'Voters.pdf');
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                        } else if ( buttonValue === "Send" && res.data.Redirect_Url){
                            const newTab = window.open();
                            newTab.document.open();
                            newTab.location.href = res.data.Redirect_Url; // Use href instead of url
                            newTab.document.close();

                        }else {
                            // Handle fallback: open link to the view
                            const newTab = window.open();
                            newTab.document.open();
                            newTab.document.write(res.data);
                            newTab.document.close();
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        toastr.error(
                            error.response?.data?.error ?? '{{ __('main.unexpected-error') }}'
                        );
                    })
                    .finally(() => {
                        submitBtn.attr('disabled', false); // Re-enable the button
                    });
            });
        });

    </script>
@endpush
@push('js')

    <script>
        $(document).ready(function () {
            function fetchFilteredOptions() {
                const data = {
                    alfkhd: $('#alfkhd').val(),
                    alfraa: $('#alfraa').val(),
                    albtn: $('#albtn').val(),
                    cod1: $('#code1').val(),
                    cod2: $('#code2').val(),
                    cod3: $('#code3').val(),
                    alktaa: $('#alktaa').val(),
                    street: $('#street').val(),
                    alharaa: $('#alharaa').val(),
                    home: $('#home').val(),
                    family_id: $('#familySearch').val(),
                };

                // Send AJAX request to get filtered options
                $.ajax({
                    url: '{{ route('filter.selections') }}', // Adjust route name if needed
                    type: 'GET',
                    data: data,
                    success: function (response) {
                        // Update each dropdown only if it does not already have a selected value
                        updateSelectOptions('#alfkhd', response.selectionIds.alfkhd);
                        updateSelectOptions('#familySearch', response.selectionIds.family_id);
                        updateSelectOptions('#alfraa', response.selectionIds.alfraa);
                        updateSelectOptions('#albtn', response.selectionIds.albtn);
                        updateSelectOptions('#code1', response.selectionIds.cod1);
                        updateSelectOptions('#code2', response.selectionIds.cod2);
                        updateSelectOptions('#code3', response.selectionIds.cod3);
                    },
                    error: function (error) {
                        console.error('Error fetching filtered options:', error);
                    },
                });
            }

            function updateSelectOptions(selector, options) {
                const select = $(selector);
                const normalizedOptions = options && typeof options === 'object' ? options : {};

                // Check if the select already has a selected value
                if (select.val() !== "") {
                    // Do not change the dropdown if it has a value selected
                    return;
                }

                if (Object.keys(normalizedOptions).length === 0) {
                    return;
                }

                // Clear and repopulate the dropdown if no value is selected
                select.empty().append('<option value=""> -- </option>'); // Add default empty option
                if (selector == "#familySearch"){
                    for (const [id, value] of Object.entries(normalizedOptions)) {
                        select.append(`<option value="${id}">${value}</option>`);
                    }

                }else{

                    for (const [id, value] of Object.entries(normalizedOptions)) {
                        select.append(`<option value="${value}">${value}</option>`);
                    }
                }

                if (select.hasClass('select2-hidden-accessible')) {
                    select.trigger('change.select2');
                }
            }
            $('.js-example-basic-single').on('change', fetchFilteredOptions);
        });

    </script>

@endpush
