@extends('layouts.dashboard.app')

@section('content')
    <style>
        th {
            font-size: 18px !important;
        }
    </style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">


    <section class="py-1 my-1 rtl">
        <div class="container mt-3">
            <x-dashboard.partials.message-alert />
            <form class="mota3ahdeenControl" action="{{ route('dashboard.contractors.store') }}" method="POST">
                @csrf
                <div class="d-flex align-items-center mb-1">
                    <label class="labelStyle" for="parent_id">المتعهد الرئيسى</label>
                    <select name="parent_id" id="parent_id" class="form-control py-1">
                        <option value="الكل" disabled>الكل</option>

                        @foreach ($parents as $item)
                            <option value="{{ $item['id'] }}"
                            @if (auth()->user()->contractor && auth()->user()->contractor->id == $item['id'])
                                selected
                            @elseif (auth()->user()->contractor && auth()->user()->contractor->id != $item['id'])
                                disabled
                            @endif
                            >{{ $item['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="moreSearch my-3">
                    <div role="button" class="btn btn-primary w-100">
                        + اضافة متعهد فرعى
                    </div>
                    <div class="description d-none p-2">
                        <div class="d-flex align-items-center mb-1">
                            <label class="labelStyle" for="name">الاسم</label>
                            <input type="text" class="form-control py-1" placeholder="Name" name="name"
                                id="name">
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <label class="labelStyle" for="phone">الهاتف</label>
                            <input type="text" class="form-control py-1 w-75" placeholder="phone" name="phone"
                                id="phone">
                            <div
                                class="w-25 bg-body-secondary py-1 d-flex justify-content-evenly fs-5 rounded-start-2 text-center">
                                <span class=" w-50 ms-2">965</span>
                                <span class=" w-50  px-1 border-end border-2 border-dark ">+</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <label class="labelStyle" for="notes">الملاحظات</label>
                            <input type="text" class="form-control py-1" placeholder="Add Note" name="note"
                                id="notes">
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class=" d-flex align-items-center">
                                <input type="checkbox" name="roles[]" id="canSearch"
                                    value="{{ App\Models\Role::findByName('بحث في الكشوف')?->name ?? '' }} " checked>
                                <label class="labelStyle" class="w-100 me-3" for="canSearch">تفعيل امكانية البحث</label>
                            </div>
                            <div class=" d-flex align-items-center">
                                <input type="checkbox" name="roles[]" id="canDelet"
                                    value="{{ App\Models\Role::findByName('حذف المضامين')?->name ?? '' }} " checked>
                                <label class="labelStyle" class="w-100 me-3" for="canDelet">يستطيع حذف مضامينة</label>
                            </div>
                        </div>

                        <button class="btn btn-success w-100 mx-auto d-block" type="submit">أضافة</button>
                    </div>
                </div>

            </form>


            <input type="search" id="searchBox"  class="form-control py-1 mb-3"
                placeholder="البحث بجدول المتعهدين الفرعيين">

            <div class="">
                <button  class="Sort_Btn mt-2 btn btn-outline-success">
                    <label for="trusted">الملتزمين</label>
                    <input type="radio" role="button" class="visually-hidden" id="trustedMota3ahdeen" name="mota3ahdeenTable"
                        value="{{App\Enums\ConType::Committed->value}}">
                </button>
                <button  class="Sort_Btn mt-2 btn btn-outline-warning">
                    <label for="trusted">قيد المتابعة</label>
                    <input type="radio" role="button" class="visually-hidden" id="registedMota3ahdeen" name="mota3ahdeenTable"
                        value="{{App\Enums\ConType::Pending->value}}">
                </button>
                <button  class="Sort_Btn mt-2 btn btn-secondary">
                    <label for="trusted">الكل</label>
                    <input type="radio" role="button" class="visually-hidden" id="allMota3ahdeen" name="mota3ahdeenTable"
                        value="all">
                </button>
                <div role="button" class="mt-2 btn btn-dark" data-bs-toggle="modal" data-bs-target="#foundedNow">
                    المتواجدين
                </div>
				
  <button onclick="exportTableToExcel('myTable', 'Arabic_Export.xlsx')" class="btn btn-primary">
   استخداج اكسيل
   </button>
				
				
				
            </div>

            <!-- Modal foundedNow-->
            <div class="modal modal-md rtl" id="foundedNow" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">
                                بيانات المتعهد
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="table-responsive mt-4">
                                <table class="table overflow-hidden rtl">
                                    <thead class="table-secondary text-center border-0 border-dark border-bottom border-2">
                                        <tr>
                                            <th>الأسم</th>
                                            <th>اخر ظهور</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider text-center">
                                        <tr>
                                            <td>هابس بدر الشويب</td>
                                            <td>03:52:11</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Mota3ahdeenList-->
            <div class="modal modal-md rtl" id="Mota3ahdeenList" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">
                               A بيانات المتعهد
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close">X</button>
                        </div>
                        <div class="modal-body px-4">

                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData" id="mota3aheedName">
                                <label class="labelStyle" for="mota3aheedName">اسم المتعهد</label>
                            </div>
                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData" id="mota3aheedPhone">
                                <label class="labelStyle" for="mota3aheedPhone">هاتف المتعهد</label>
                            </div>
                            <hr>
                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData" id="mota3aheedTrusted">
                                <label class="labelStyle" for="mota3aheedTrusted">الألتزام</label>
                            </div>
                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData" id="madameenNum">
                                <label class="labelStyle" for="madameenNum">عدد المضامين</label>
                            </div>
                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData" id="percentageTrusted">
                                <label class="labelStyle" for="percentageTrusted">نسبة الصدق</label>
                            </div>
                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData" id="trustedNumber">
                                <label class="labelStyle" for="trustedNumber">عدد صدق المضامين</label>
                            </div>
                            <hr>
                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData" id="mota3aheedName">
                                <label class="labelStyle" for="mota3aheedName">اسم المتعهد</label>
                            </div>
                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData"
                                    id="printMejorMota3ahed">
                                <label class="labelStyle" for="printMejorMota3ahed"> المتعهد الرئيسى</label>
                            </div>
                            <div>
                                <input type="checkbox" class="" name="printMota3ahedData" id="addationDate">
                                <label class="labelStyle" for="addationDate">تاريخ الاضافة</label>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center">
                                <label class="labelStyle" for="mota3aheedTrusted">ترتيب</label>
                                <select name="sorted" id="sorted" class="form-control py-1">
                                    <option value="">أبجدى</option>
                                    <option value="">الهاتف</option>
                                    <option value="">الألتزام</option>
                                </select>
                            </div>



                        </div>
                    </div>
                </div>
            </div>

			<div class="row">
			
				<div class="col-12 col-md-6">
			
			
					
			</div>
			
			</div>
            <div class="table-responsive mt-4">
                <table id="myTable" class="table rtl overflow-hidden rounded-3 text-center">
                    <thead class="table-primary border-0 border-secondary border-bottom border-2">
                        <tr style="font-size: 15px !important">
                            <th>
                                <button class="btn btn-secondary all">الكل</button>
                            </th>
                            <th class="w150"> المتعهدين ({{ $children->count() }}) </th>
                            <th>الهاتف</th>
                            <th>الحضور</th>
                            <th>مضامين</th>
                            <th>نسبة الصدق</th>
                            <th>صدق المضامين</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($children as $c=>$i)
                            <tr
                            class="all
                                @if ($i->status == 1)
                                table-info
                                    {{App\Enums\ConType::Committed->value}}
                                @else
                                table-warning
                                    {{App\Enums\ConType::Pending->value}}
                                @endif
                                ">
                                <td>
                                    <input type="checkbox" class="check" name="madameenNameChecked" />
                                </td>
                                <input type="hidden" id="conUrl" data-url="{{ route('con-profile', $i->token) }}">
                                <td class="d-none" id="user_id">{{ $i->id }}</td>
                                <td data-bs-toggle="modal" data-bs-target="#mota3ahdeenData">
                                    {{ $i->name }}
                                </td>
                                <td>{{ $i->phone }}</td>
                                <td>{{ $i->voters->filter(function ($voter) {
                                        return $voter->status ==     1;
                                    })->count() }}
                                </td>
                                <td>{{ $i->voters->count() }}</td>
                                <td>{{ $i->trust }}%</td>
                                <td>{{ $i->voters->where('status', 1)->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal madameenName-->
        <!--    <div class="modal modal-lg rtl" id="mota3ahdeenData" tabindex="-1" aria-labelledby="exampleModalLabel"

                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">
                               Z بيانات المتعهد
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close">X</button>
                        </div>
                        <div class="modal-body">
                            <label class="labelStyle">تعيين المتعهد</label>
                            <form class="d-flex align-items-center justify-content-around flex-wrap">

                                <div class="checkMota3ahed mt-2 d-flex justify-content-between flex-wrap">
                                    <label class="mx-1 mb-2 btn btn-outline-success " id="moltazem-l"
                                        for="moltazem">ملتزم

                                        <input type="radio" data-bs-target="edit-mot" class="visually-hidden"
                                            name="status" id="moltazem" value="1">
                                    </label>

                                    <label class="mx-1 mb-2 btn  btn-outline-warning" id="follow-l" for="follow">قيد
                                        المتابعة

                                        <input type="radio" data-bs-target="edit-mot" class="visually-hidden"
                                            name="status" id="follow" value="0">
                                    </label>


                                </div>

                                <div class="checkMota3ahed mt-2 d-flex justify-content-between flex-wrap">

                                    <label class="mx-1 mb-2 btn btn-outline-secondary" for="general">عام

                                        <input type="radio" class="visually-hidden" name="mota3ahdeenType"
                                            id="general" value="general">
                                    </label>

                                    <label class="mx-1 mb-2 btn btn-outline-danger" for="private">خاص

                                        <input type="radio" class="visually-hidden" name="mota3ahdeenType"
                                            id="private" value="private">
                                    </label>

                                </div>

                                <div class="moreSearch my-2 w-100 text-center">
                                    <div role="button" class="w-100 btn btn-secondary px-5 fw-semibold">
                                        اعدادات
                                    </div>
                                    <div class="description d-none p-2">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div class="d-flex align-items-center w-100">
                                                <input type="checkbox" name="settingMota3ahed" id="settingCanSearch"
                                                    checked>
                                                <label class="w-100 me-2" for="settingCanSearch">تفعيل امكانية
                                                    البحث</label>
                                            </div>
                                            <div class=" d-flex align-items-center w-100">
                                                <input type="checkbox" name="settingMota3ahed" id="settingCanDelet"
                                                    checked>
                                                <label class="w-100 me-2" for="settingCanDelet">يستطيع حذف مضامينة</label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-1">
                                            <label class="labelStyle" for="changparent_id">تغيير المتعهد الرئيسى</label>
                                            <select name="parent_id" data-bs-target="edit-mot" id="changparent_id"
                                                class="form-control py-1">

                                                <option value="الكل" disabled>الكل</option>
                                                @foreach ($parents as $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button class="btn btn-primary w-100">حفظ الأعدادات</button>
                                        <hr>
                                    </div>
                                </div>
                            </form>

                            <button class="btn btn-primary d-block me-auto" id="delete-con" value="">حذف
                                المتعهد</button>

                            <div class="d-flex align-items-center mt-3 border">
                                <label class="labelStyle" for="trustingRate">المصداقيه</label>
                                <input data-bs-target="edit-mot" type="range" name="trust" id="trustingRate"
                                    value="0" min="0" class="w-100">
                                <span class="bg-body-secondary p-2 px-3 d-flex">% <span id="trustText"
                                        class="fw-semibold">0</span></span>
                            </div>

                            <div class="mt-3">
                                <div class="d-flex align-items-center mb-1">
                                    <label class="labelStyle" class="" for="nameMota3ahed">الاسم </label>
                                    <input data-bs-target="edit-mot" type="text" class="form-control py-1"
                                        name="name" id="nameMota3ahed" value="" min="" />
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <label class="labelStyle" class="" for="phoneMota3ahed"> الهاتف </label>
                                    <input data-bs-target="edit-mot" type="text" class="form-control py-1"
                                        name="phone" id="phoneMota3ahed" value="144254435" min="0" />
                                    <a href=""
                                        class="d-inline-block px-2 py-1 mx-1 text-white bg-primary rounded-circle"><i
                                            class="pt-1 fs-5 fa fa-phone"></i></a>
                                    <a href=""
                                        class="d-inline-block px-2 py-1 mx-1 text-white bg-success rounded-circle"><i
                                            class="pt-1 fs-5 fa-brands fa-whatsapp"></i></a>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <label class="labelStyle" class="" for="noteMota3ahed">ملاحظات </label>
                                    <textarea data-bs-target="edit-mot" class="form-control py-1" name="note" id="noteMota3ahed" value=""
                                        min="">
                          </textarea>
                                </div>
                            </div>
                            <hr>

                            <div class="text-center">
                                <span>رابط المتعهد</span>
                                <span class="bg-body-secondary p-1 px-3 rounded-pill me-2"
                                    id="con-url">md-kw.com/./?q=NTEy</span>
                            </div>
                            <div class="text-center">
                                <a href="" id="RedirectLink">
                                    <button class="btn btn-secondary copyLink mb-1" value="link">
                                        <i class="fa fa-book"></i>
                                        <span>الدخول للرابط</span>
                                    </button>
                                </a>

                                <a href="">
                                    <button class="btn btn-secondary copyLink mb-1" value="link">
                                        <i class="fa fa-book"></i>
                                        <span>نسخ الرابط</span>
                                    </button>
                                </a>
                            </div>
                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <div class="form-group text-center">
                                    <input type="number" id="phone_wa" class="form-control py-1 d-inline-block bg-secondary bg-opacity-25"
                                        style="width: auto; margin-right: 10px;" placeholder="رقم الهاتف">
                                        <input type="hidden" value="{{message()}}" id="message">
                                    <a id="whatsapp-link" target="_blank">
                                        <button class="btn"
                                            style="background-color: #25D366; color: white; border: none; padding: 10px 20px; cursor: pointer;">
                                            <i class="fab fa-whatsapp"></i>
                                            <span>ارسال الرابط</span>
                                        </button>
                                    </a>
                                </div>
                            </div>
                            <div class="text-white text-center rtl rounded-3">
                                <h3 class="bg-secondary py-1">
                                    كشف المتعهد
                                    <span class="bg-dark rounded-3 fs-6 px-2 py-1 me-2">العدد<span
                                            class="tableNumber me-2" id="voters_numberss">2</span></span>
                                </h3>

                                <form action="" method="POST" id="form-attach">
                                    @csrf
                                    <div class="d-flex justify-content-between">
                                        <label class="labelStyle" class="text-black">التطبيق على المحدد</label>
                                        <input type="hidden" id="last_id" name="id" value="sda">
                                        <select name="addMota3ahed" id="addMota3ahed" class="form-control py-1">
                                            <option value="" disabled selected>--نقل المحددين الى متعهد اخر--
                                            </option>
                                            <option value="delete" class="btn btn-danger">حذف المحددين</option>
                                            <option value="" hidden></option>
                                            @foreach ($children as $i)
                                                <option value="{{ $i->id }}">{{ $i->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary px-3" id="all_voters">تنفيذ</button>

                                    </div>
                                </form>

                                <button data-bs-toggle="modal" data-bs-target="#elkshoofDetails" class="my-2 btn btn-dark"> استخراج الكشوف</button>


                                <div class="table-responsive mt-2">
                                    <table class="table rtl overflow-hidden rounded-3 text-center">
                                        <thead class="table-secondary border-0 border-secondary border-bottom border-2">
                                            <tr>
                                                <th>
                                                    <button class="btn btn-secondary all">الكل</button>
                                                </th>
                                                <th>#</th>
                                                <th class="w150">الأسم</th>
                                                <th>النسبة</th>
                                                <th>الهاتف</th>
                                                <th>أدوات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="voters_con">

                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-end">
                                    <div class="Delete_names d-none">
                                        <h5 class="bg-danger rounded-2 p-2 cPointer">
                                            أسماء محذوفة
                                            <span class="bg-dark rounded-3 fs-6 px-2 py-1 me-2">العدد<span
                                                    class="ma7zofeenNumber me-2" id="deletes_count">3</span></span>
                                        </h5>

                                        <table class="table rtl overflow-hidden rounded-3 text-center d-none">
                                            <thead class="table-secondary border-0 border-secondary border-bottom border-2">
                                                <tr>
                                                    <th>#</th>
                                                    <th class="w150">الأسم</th>
                                                    <th>الهاتف</th>
                                                </tr>
                                            </thead>
                                            <tbody id="deletes_data">

                                            </tbody>
                                        </table>

                                    </div>

                                    <div class="Search_logs d-none">
                                        <h5 class="bg-info rounded-2 p-2 cPointer">
                                            سجل عمليات البحث :
                                            <span class="bg-dark rounded-3 fs-6 px-2 py-1 me-2"><span
                                                    class="ma7zofeenNumber me-2" id="log_count">0</span></span>
                                        </h5>
                                        <table class="table rtl overflow-hidden rounded-3 text-center d-none">
                                            <thead class="table-secondary border-0 border-secondary border-bottom border-2">
                                                <tr>
                                                    <th class="w150">البحث</th>
                                                    <th>الوقت</th>
                                                </tr>
                                            </thead>
                                            <tbody id="log_data">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>
            
-->
            </div>
        </div>

        <div
        class="modal modal-md rtl"
        id="elkshoofDetails"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
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
            <div class="modal-body px-4">

              <form action="{{route('export')}}" method="GET" id="export">
                <input type="hidden" name="search_id"id="search_id" value="">
                <div class="d-flex align-items-center mb-3">
                    <label class="labelStyle pt-2 pb-1 rounded-3" for="na5ebArea">المنطقة</label>
                  <input type="text" class="form-control bg-secondary bg-opacity-25 " value="" name="region" id="na5ebArea">
                </div>

                <div class="row g-4">
                  <div class="col-6 ">
                    <div class="d-flex align-items-center ">
                        <input checked disabled type="checkbox" class="" name="columns[]" value="name" id="na5ebName">
                        <label for="na5ebName">اسم الناخب</label>
                    </div>
                    <div class="d-flex align-items-center ">
                        <input checked type="checkbox" class="" name="columns[]" value="family" id="na5ebFamilyName">
                        <label for="na5ebFamilyName"> العائلة</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <input type="checkbox" class="ms-2" name="columns[]" value="age" id="na5ebAge">
                        <!-- <label for="na5ebAge"> العائلة</label> -->
                        <select name="na5ebAge" class="border-0 p-1">
                            <option value="الميلاد / العمر">الميلاد / العمر</option>
                            <option value=" العمر"> العمر</option>
                            <option value=" سنة الميلاد  ">سنة الميلاد</option>
                            <option value="تاريخ الميلاد">تاريخ الميلاد</option>
                        </select>
                    </div>
                  </div>

                  <div class="col-6 ">
                      {{-- <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebCircle">
                      <label for="na5ebCircle"> الدائرة</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebChar">
                      <label for="na5ebChar"> الحرف</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebTableNumber">
                      <label for="na5ebTableNumber"> رقم الجدول</label>
                    </div> --}}
                    <div class="d-flex align-items-center ">
                      <input checked  type="checkbox" class="" name="columns[]" value="alsndok" id="na5ebRejestNumber">
                      <label for="na5ebRejestNumber"> رقم القيد</label>
                    </div>

                  </div>

                   <div class="col-6 ">
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="phone" id="na5ebPhone">
                      <label for="na5ebPhone"> الهاتف</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="type" id="na5ebType">
                      <label for="na5ebType"> الجنس</label>
                    </div>
                    <div class="d-flex align-items-center mb-1 ">
                        <input  type="checkbox" class="ms-2" name="columns[]" value="created_at" id="na5ebRejesterDate">
                        <label class="labelStyle" for="na5ebRejesterDate"> تاريخ القيد</label>
                      </div>
                    {{-- <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="job" id="na5ebJop">
                      <label for="na5ebJop"> المهنة</label>
                    </div>

                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebAddress">
                      <label for="na5ebAddress"> العنوان</label>
                    </div> --}}

                    <div class="d-flex align-items-center ">
                        <input  type="checkbox" class="" name="columns[]" value="region" id="na5ebRejon">
                        <label for="na5ebRejon"> المنطقة</label>
                      </div>
                  </div>

                  <div class="col-6 ">
                      <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="committee" id="na5ebCommitte">
                      <label for="na5ebCommitte"> اللجنة</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="madrasa" id="na5ebSchool">
                      <label for="na5ebSchool"> مدرسة الانتخاب</label>
                    </div>
                    <div class="d-flex align-items-center ">
                     <input checked type="checkbox" class="" name="columns[]" value="restricted" id="na5ebRejesterStatus">
                     <label for="na5ebRejesterStatus"> حالة القيد</label>
                   </div>
                    {{--
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebRejesterDate">
                      <label for="na5ebRejesterDate"> تاريخ القيد</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebReference">
                      <label for="na5ebReference"> مرجع الداخلية</label>
                    </div> --}}
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="status" id="status">
                      <label for="na5ebCheckedTime"> حاله التصويت</label>
                    </div>
                  </div>



                </div>
              <hr>
              <div class="d-flex align-items-center">
                <label class="labelStyle" for="mota3aheedTrusted">ترتيب</label>
                <select name="sorted" id="sorted" class="form-control">
                      <option value="asc">أبجدى</option>
                      <option value="phone">الهاتف</option>
                      <option value="asc">الألتزام</option>
                  </select>
              </div>
              <input type="hidden" name="type" id="type">
                <div class="rtl my-4 text-center">
                <a href="">
                  <button class="btn btn-primary button" value="PDF">PDF</button>
                </a>
                <a href="">
                  <button class="btn btn-success button" value="Excel">Excel</button>
                </a>
                <a href="">
                  <button class="btn btn-secondary button" value="print">طباعة</button>
                </a>
                <a href="">
                  <button class="btn btn-secondary button" value="show">عرض</button>
                </a>
              </div>


              <p class="text-danger">* ملاحظة : لا يم;ن استخراج البيانات الضخمة عبر ملف PDF</p>
              <div class="d-flex align-items-center mb-3 pdfe">
                  <label class="w-50 lableStyle" for="sendToNa5eb">ارسال PDF عبر whatsapp</label>
                  <input type="number" class="form-control bg-secondary bg-opacity-25" name="to" id="sendToNa5eb" placeholder="رقم الهاتف">
                  <button class="btn btn-primary me-2 button" value="Send">ارسال</button>
              </div>




              </form>



            </div>
          </div>
        </div>
      </div>

    </section>
@endsection
@push('js')
    <script>
        let url
        $("#addMota3ahed").on('change', function() {
            console.log($("#addMota3ahed").val());
            if ($("#addMota3ahed").val() == "delete") {
                url = "/delete/mad"
            } else {
                url = "/ass/" + $(this).val()
            }

            $("#form-attach").attr("action", url)
        })
    </script>
    <script>

        $(".Sort_Btn").on('click',function(){
            $('.all').addClass('d-none');
            $('.'+$(this).find('input').val()).removeClass('d-none')
        })
    </script>
    <script>
        $(document).ready(function() {
        $('.button').on('click', function() {
            var buttonValue = $(this).val();
            $('#type').val(buttonValue);

               if(!checkedValues){
        var checkedElements = $('.check:checked');

            var checkedValues = $.map(checkedElements, function(element) {
            return $(element).val();
            });

            }
            checkedValues.forEach(function(value) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'voter[]',
                    value: value
                }).appendTo('#export');
            });


            var form = $('#export')[0]; // Ensure we get the DOM element, not jQuery object
            var formData = new FormData(form);
            var submitBtn = $(this);
            axios.get(form.action, formData)
                .then((res) => {
                    console.log(res);
                    setTimeout(() => {
                        console.log(res.data);
                    }, 2000);
                    if (res.data.Redirect_Url) {
                        window.location.href = res.data.Redirect_Url;
                    } else {
                        toastr.success(res.data.success);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.log(error);
                    toastr.error(error.response.data.error ?? '{{ __('main.unexpected-error') }}');
                    submitBtn.attr('disabled', false);
                });
        });
    });
    </script>





<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

 <script>
        document.addEventListener("DOMContentLoaded", function () {
        const table = document.getElementById("myTable");
        const headers = table.querySelectorAll("thead th");

        headers.forEach((header, columnIndex) => {
            header.addEventListener("click", function () {
                sortTable(table, columnIndex);
            });
        });
    });

    function sortTable(table, columnIndex) {
        const rows = Array.from(table.querySelectorAll("tbody tr"));
        const isAscending = table.dataset.sortOrder !== "asc";
        const collator = new Intl.Collator("ar", { numeric: true });

        rows.sort((rowA, rowB) => {
            const cellA = rowA.cells[columnIndex]?.textContent.trim() || "";
            const cellB = rowB.cells[columnIndex]?.textContent.trim() || "";

            return isAscending
                ? collator.compare(cellA, cellB)
                : collator.compare(cellB, cellA);
        });

        rows.forEach(row => table.querySelector("tbody").appendChild(row));
        table.dataset.sortOrder = isAscending ? "asc" : "desc";
    }


        // Search Functionality
        document.getElementById("searchBox").addEventListener("input", function () {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll("#myTable tbody tr");

            rows.forEach(row => {
                const cells = Array.from(row.cells);
                const matches = cells.some(cell =>
                    cell.innerText.toLowerCase().includes(query)
                );

                row.style.display = matches ? "" : "none";
            });
        });

        // Export Table to CSV
        function exportTable() {
            const table = document.getElementById("myTable");
            let csvContent = "";

            Array.from(table.rows).forEach(row => {
                const rowData = Array.from(row.cells)
                    .map(cell => `"${cell.innerText}"`)
                    .join(",");
                csvContent += rowData + "\n";
            });

            const blob = new Blob([csvContent], { type: "text/csv" });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "table_data.csv";
            link.click();
        }
	 
	 
	 
	function exportTableToExcel(tableId, filename = "export.xlsx") {
    const table = document.getElementById(tableId);
    const rows = Array.from(table.rows);

    // Create an HTML string of the table
    const tableHTML = rows
        .map(row => {
            const cells = Array.from(row.cells);
            return (
                "<tr>" +
                cells
                    .map(cell => `<td>${cell.textContent.replace(/\s+/g, " ").trim()}</td>`)
                    .join("") +
                "</tr>"
            );
        })
        .join("");

    // Create an XML string for Excel
    const xmlTemplate = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" 
              xmlns:x="urn:schemas-microsoft-com:office:excel" 
              xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Sheet1</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
        </head>
        <body>
            <table>
                ${tableHTML}
            </table>
        </body>
        </html>
    `;

    // Create a Blob object with the XML content
    const blob = new Blob([xmlTemplate], { type: "application/vnd.ms-excel;charset=utf-8;" });

    // Create a download link and trigger the download
    const downloadLink = document.createElement("a");
    const url = URL.createObjectURL(blob);
    downloadLink.href = url;
    downloadLink.download = filename;

    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
 
	
	 
    </script>



@endpush
