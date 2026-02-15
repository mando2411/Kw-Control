@extends('layouts.dashboard.app')

@section('content')
    <section class=" rtl">
        <div class="container mt-3">
            {{-- <form>
                <input type="search" class="form-control" name="searchInMota3ahdeenTable" id="searchInMota3ahdeenTable"
                    oninput="getSubMota3ahdeenName()" placeholder="البحث فى جدول المتعهدين الفرعيين" />
                <div class="table-responsive mt-4">
                    <table class="table   rtl overflow-hidden rounded-3 text-center">
                        <thead class=" table-primary border-0 border-secondary border-bottom border-2">
                            <tr>
                                <th>id</th>
                                <th class="w150">المتعهدين الفرعيين</th>
                                <th>الهاتف</th>
                                <th>مضامين</th>
                                <th>الحذف</th>
                            </tr>
                        </thead>
                        <tbody class="searchSubMota3ahdeenName">
                            <tr>
                                <td>150</td>
                                <td data-bs-toggle="modal" data-bs-target="#ma7azofeenMota3ahdeenData">
                                    خالد خلف المطيرى
                                </td>
                                <td>7721654</td>
                                <td>0</td>
                                <td>
                                    <span>12-05-2024</span>
                                    <span>03:05:16</span>
                                </td>
                            </tr>
                            <tr>
                                <td>93</td>
                                <td data-bs-toggle="modal" data-bs-target="#ma7azofeenMota3ahdeenData">
                                    متعب صالح الدحيانى
                                </td>
                                <td>7721654</td>
                                <td>0</td>
                                <td>
                                    <span>12-05-2024</span>
                                    <span>03:05:16</span>
                                </td>
                            </tr>
                            <tr>
                                <td>74</td>
                                <td data-bs-toggle="modal" data-bs-target="#ma7azofeenMota3ahdeenData">
                                    احمد خلف
                                </td>
                                <td>7721654</td>
                                <td>0</td>
                                <td>
                                    <span>12-05-2024</span>
                                    <span>03:05:16</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Modal ma7azofeenMota3ahdeenData-->
            <div class="modal modal-md rtl" id="ma7azofeenMota3ahdeenData" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">
                                بيانات المتعهد
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label>تعيين المتعهد</label>
                            <form class="d-flex align-items-center justify-content-center flex-wrap">
                                <div class="checkMota3ahed mt-2 d-flex justify-content-between flex-wrap">
                                    <label class="mx-1 mb-2 btn btn-outline-success " for="moltazem">ملتزم

                                        <input type="radio" class="visually-hidden" name="mota3ahdeenTableStatus"
                                            id="moltazem" value="moltazem">
                                    </label>

                                    <label class="mx-1 mb-2 btn  btn-outline-warning" for="follow">قيد المتابعة

                                        <input type="radio" class="visually-hidden" name="mota3ahdeenTableStatus"
                                            id="follow" value="follow">
                                    </label>

                                </div>

                                <div class="checkMota3ahed mt-2 d-flex justify-content-between flex-wrap">
                                    <label class="mx-1 mb-2 btn btn-outline-secondary " for="general">عام

                                        <input type="radio" class="visually-hidden" name="mota3ahdeenTableType"
                                            id="general" value="general">
                                    </label>

                                    <label class="mx-1 mb-2 btn  btn-outline-danger" for="private">خاص

                                        <input type="radio" class="visually-hidden" name="mota3ahdeenTableType"
                                            id="private" value="private">
                                    </label>

                                </div>

                                <div class="moreSearch my-2 w-100">
                                    <div role="button" class="w-100 btn btn-secondary fw-semibold">
                                        اعدادات
                                    </div>
                                    <div class="description d-none p-2">
                                        <div class="d-flex justify-content-evenly mb-3 flex-wrap">
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" name="settingMota3ahed" id="settingCanSearch"
                                                    checked />
                                                <label class="w-100 me-3" for="settingCanSearch">تفعيل امكانية البحث</label>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" name="settingMota3ahed" id="settingCanDelet"
                                                    checked />
                                                <label class="w-100 me-3" for="settingCanDelet">يستطيع حذف مضامينة</label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <label for="changMajorMota3ahdeen">تغيير المتعهد الرئيسى</label>
                                            <select name="changMajorMota3ahdeen" id="changMajorMota3ahdeen"
                                                class="form-control">
                                                <option value="بوخزنة">بوخزنة</option>
                                                <option value="عبدالله شرار">عبدالله شرار</option>
                                                <option value="ابو باسل">ابو باسل</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary w-100">
                                            حفظ الأعدادات
                                        </button>
                                        <hr />
                                        <button class="btn btn-danger d-block me-auto">
                                            حذف المتعهد
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="d-flex align-items-center mt-3 border">
                                <label class="labelStyle" for="trustingRate">المصداقيه</label>
                                <input type="range" name="trustingRate" id="trustingRate" value="0" class="w-100" />
                                <span class="bg-body-secondary p-2 px-3 d-flex">% <span
                                        class="fw-semibold">0</span></span>
                            </div>

                            <div class="mt-3">
                                <div class="d-flex align-items-center mb-3">
                                    <label class="labelStyle" for="nameMota3ahed">الاسم </label>
                                    <input type="text" class="form-control" name="nameMota3ahed" id="nameMota3ahed"
                                        value="" min="" />
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <label class="labelStyle" for="phoneMota3ahed"> الهاتف </label>
                                    <input type="text" class="form-control" name="phoneMota3ahed" id="phoneMota3ahed"
                                        value="144254435" min="0" />
                                    <a href=""
                                        class="d-inline-block px-2 py-1 mx-1 text-white bg-primary rounded-circle"><i
                                            class="pt-1 fs-5 fa fa-phone"></i></a>
                                    <a href=""
                                        class="d-inline-block px-2 py-1 mx-1 text-white bg-success rounded-circle"><i
                                            class="pt-1 fs-5 fa-brands fa-whatsapp"></i></a>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <label class="labelStyle" for="noteMota3ahed">ملاحظات </label>
                                    <textarea class="form-control" name="noteMota3ahed" id="noteMota3ahed" value="" min="">
                  </textarea>
                                </div>
                            </div>
                            <hr />

                            <div class="text-center">
                                <span>رابط المتعهد</span>
                                <span class="bg-body-secondary p-1 px-3 rounded-pill me-2">md-kw.com/./?q=NTEy</span>
                            </div>
                            <div class="text-center mt-3">
                                <a href="" target="_blank">
                                    <button class="btn mb-1 btn-success">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <span>ارسال الرابط</span>
                                    </button>
                                </a>
                                <a href="">
                                    <button class="btn mb-1 btn-secondary copyLink" value="link">
                                        <i class="fa fa-clipboard"></i>
                                        <span>Aنسخ الرابط</span>
                                    </button>
                                </a>
                                <a href="" target="_blank">
                                    <button class="btn mb-1 btn-secondary copyLink" value="link">
                                        <i class="fa fa-link"></i>
                                        <span>الدخول للرابط</span>
                                    </button>
                                </a>
                            </div>
                            <hr />
                            <button class="btn btn-primary d-block mx-auto mb-3">
                                استخراج كشوف
                            </button>

                            <div class="text-white text-center rtl rounded-3">
                                <h3 class="bg-secondary py-1">
                                    كشف المتعهد
                                    <span class="bg-dark rounded-3 fs-6 px-2 pt-1 me-2">العدد<span
                                            class="tableNumber me-2">2</span></span>
                                </h3>
                                <div class="d-flex">
                                    <label class="labelStyle">التطبيق على المحدد</label>
                                    <select name="deleteSelected" id="deleteSelected" class="form-control">
                                        <option value="" disabled class="btn btn-danger">
                                            حذف المحددين
                                        </option>
                                        <option value="" disabled>
                                            --نقل المحددين الى متعهد اخر--
                                        </option>
                                        <option value="" hidden></option>
                                        <option value="">سمير محمد البغدادى</option>
                                        <option value="">عزة محمد الهلالى</option>
                                    </select>
                                    <button class="btn btn-primary px-4 me-2">تنفيذ</button>
                                </div>

                                <div class="table-responsive mt-4">
                                    <table class="table  rtl overflow-hidden rounded-3 text-center">
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
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="check" name="mota3ahedNameChecked" />
                                                </td>
                                                <td>1</td>
                                                <td>فيصل حسين على جديع اباالصافى</td>
                                                <td>0%</td>
                                                <td>7756664</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="check" name="mota3ahedNameChecked" />
                                                </td>
                                                <td>2</td>
                                                <td>سمير فيصل حسين على جديع اباالصافى</td>
                                                <td>0%</td>
                                                <td>15490164</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="d-flex mt-4 text-white">
                <div class="text-center w-50 rounded-3 py-2 bg-info font-sm">
                    استخراخ كشوف (<span class="listNumber">0</span>)
                </div>

                @php
                    $count=0;
                    foreach ($children as $key => $con) {
                       $count += $con->softDelete->count();
                    }
                @endphp
                <div class="text-center w-50 rounded-3 py-2 mx-2 bg-dark font-sm">
                    المضامين ( <span class="madameednumber">{{$count}}</span> )
                </div>
            </div>

            <div class="madameenTable table-responsive mt-4">
                <table class="table   rtl overflow-hidden rounded-3 text-center">
                    <thead class="table-secondary border-0 border-secondary border-bottom border-2">
                        <tr>
                            <th>
                                <button class="btn btn-secondary all">الكل</button>
                            </th>
                            <th>#</th>
                            <th class="w150">أسماء المضامين (المحذوفيين)</th>
                            <th>المتعهد</th>
                            <th>تاريخ الحذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($children as $con)
                            @foreach ($con->softDelete as $i=>$voter )
                                <tr>
                                    <td>
                                        <input type="checkbox" class="check" name="madameenNameChecked" value="{{$voter->id}}" />
                                    </td>
                                    <td>{{$i+1}}</td>
                                    <td data-bs-toggle="modal" data-bs-target="#voterData">
                                        {{$voter->name}}
                                    </td>
                                    <td>{{$con->name}}</td>
                                    <td>
                                        <span class="d-block">{{$voter->pivot->created_at->format('d-m-Y')}}</span>
                                        <span class="d-block">{{$voter->pivot->created_at->format('H:i:s')}}</span>
                                       </td>
                                </tr>
                            @endforeach

                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal madameenName-->
            {{-- <div class="modal modal-lg rtl" id="voterData" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">
                                بيانات الناخب
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="">
                                <div class="d-flex align-items-center mb-3">
                                    <label class="" for="civilNumber">الرقم المدنى</label>
                                    <input type="number" class="form-control" name="civilNumber" id="civilNumber"
                                        value="0" min="0" />
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <label class="" for="fullName">الاسم الكامل </label>
                                    <input type="text" class="form-control" name="fullName" id="fullName"
                                        value="" min="" />
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="d-flex align-items-center w-75">
                                        <label class="" for="name">الاسم </label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center w-25 me-2">
                                        <label class="" for="family">العائلة </label>
                                        <input type="text" class="form-control" name="family" id="family"
                                            value="" min="" />
                                    </div>
                                </div>
                                <hr />
                                <div class="d-flex align-items-center mb-3">
                                    <label class="" for="phone"> الهاتف </label>
                                    <input type="text" class="form-control" name="phone" id="phone"
                                        value="144254435" min="0" />
                                    <a href="" class="d-inline-block px-4 text-white bg-primary"><i
                                            class="pt-2 fs-5 fa fa-phone"></i></a>
                                    <a href="" class="d-inline-block px-4 text-white bg-success"><i
                                            class="pt-2 fs-5 fa-brands fa-whatsapp"></i></a>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <label class="" for="yourPhone"> الهاتف </label>
                                    <input type="text" class="form-control" name="yourPhone" id="yourPhone"
                                        value="0" min="0" />
                                    <a href="" class="d-inline-block px-4 text-white bg-primary"><i
                                            class="pt-2 fs-5 fa fa-phone"></i></a>
                                    <a href="" class="d-inline-block px-4 text-white bg-success"><i
                                            class="pt-2 fs-5 fa-brands fa-whatsapp"></i></a>
                                </div>
                                <hr />
                                <div class="d-flex align-items-center mb-3 widthOn3">
                                    <div class="d-flex align-items-center">
                                        <label class="" for="fa5z">فخذ </label>
                                        <input type="text" class="form-control" name="fa5z" id="fa5z"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="" for="branch">فرع </label>
                                        <input type="text" class="form-control" name="branch" id="branch"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="" for="baten">بطن </label>
                                        <input type="text" class="form-control" name="baten" id="baten"
                                            value="" min="" />
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3 widthOn3">
                                    <div class="d-flex align-items-center">
                                        <label class="" for="code1">كود1 </label>
                                        <input type="text" class="form-control" name="code1" id="code1"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="" for="code2">كود2 </label>
                                        <input type="text" class="form-control" name="code2" id="code2"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="" for="code3">كود3 </label>
                                        <input type="text" class="form-control" name="code3" id="code3"
                                            value="" min="" />
                                    </div>
                                </div>
                                <hr />

                                <div class="d-flex align-items-center mb-3">
                                    <div class="d-flex align-items-center w-50">
                                        <label class="" for="personType">الجنس</label>
                                        <input type="text" class="form-control" name="personType" id="personType"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center w-50">
                                        <label class="" for="work"> المهنة </label>
                                        <input type="text" class="form-control" name="work" id="work"
                                            value="" min="" />
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="d-flex align-items-center w-50">
                                        <label class="" for="birthday">الميلاد</label>
                                        <input type="text" class="form-control" name="birthday" id="birthday"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center w-50">
                                        <label class="" for="age"> العمر </label>
                                        <input type="text" class="form-control" name="age" id="age"
                                            value="" min="" />
                                    </div>
                                </div>

                                <div class="d-flex mb-3 align-items-center">
                                    <label class="" for="address">العنوان</label>
                                    <input type="text" class="form-control" name="address" id="address"
                                        value="الفردوس" min="" />
                                </div>
                                <hr />

                                <div class="d-flex align-items-center mb-3">
                                    <div class="d-flex align-items-center w-50">
                                        <label class="" for="circle">الدائرة</label>
                                        <input type="number" class="form-control" name="circle" id="circle"
                                            value="6" min="1" />
                                    </div>
                                    <div class="d-flex align-items-center w-50">
                                        <label class="" for="refrence"> المرجع </label>
                                        <input type="number" class="form-control" name="refrence" id="refrence"
                                            value="4651365463" min="0" />
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3 widthOn3">
                                    <div class="d-flex align-items-center">
                                        <label class="" for="character">حرف </label>
                                        <input type="text" class="form-control" name="character" id="character"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="" for="userTable">جدول </label>
                                        <input type="text" class="form-control" name="userTable" id="userTable"
                                            value="" min="" />
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="" for="registe">قيد </label>
                                        <input type="text" class="form-control" name="registe" id="registe"
                                            value="" min="" />
                                    </div>
                                </div>
                                <div class="d-flex mb-3 align-items-center justify-content-between">
                                    <label class="" for="committee">لجنة</label>
                                    <input type="number" class="form-control w-25 ms-2" name="committeeNumber"
                                        id="committeeNumber" value="19" min="1" />
                                    <input type="text" class="form-control w-75" name="committee" id="committee"
                                        value="مدرسة الفردوس المتوسطة بنات" min="" />
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="d-flex align-items-center w-50">
                                        <label class="" for="registeNumber">تاريخ القيد</label>
                                        <input type="number" class="form-control" name="registeNumber"
                                            id="registeNumber" value="6" min="1" />
                                    </div>
                                    <div class="d-flex align-items-center w-50">
                                        <label class="" for="lastCircle"> الدائرة السابقة </label>
                                        <input type="number" class="form-control" name="lastCircle" id="lastCircle"
                                            value="4651365463" min="0" />
                                    </div>
                                </div>

                                <div class="d-flex">
                                    <label for="rate">نسبة الالتزام</label>
                                    <input type="range" class="w-100" name="rate" id="rate" value=""
                                        min="" />
                                </div>
                                <button type="submit" class="btn btn-outline-success mt-3 w-100">
                                    تحديث البيانات
                                </button>
                            </form>

                            <div class="table-responsive mt-4">
                                <table class="table   overflow-hidden rtl">
                                    <thead class="table-secondary text-center border-0 border-dark border-bottom border-2">
                                        <tr>
                                            <th>المتعهدين بالناخب</th>
                                            <th>مدخلات المتعهد بالناخب</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <tr>
                                            <td rowspan="3">هابس بدر الشويب</td>
                                            <td>الالتزام:100%</td>
                                        </tr>
                                        <tr>
                                            <td>الهاتف:</td>
                                        </tr>
                                        <tr>
                                            <td>الملاحظات:</td>
                                        </tr>

                                        <!-- row2 -->
                                        <tr>
                                            <td rowspan="3">عبدالله سعد المطيرى</td>
                                            <td>الالتزام:100%</td>
                                        </tr>
                                        <tr>
                                            <td>الهاتف:</td>
                                        </tr>
                                        <tr>
                                            <td>الملاحظات:</td>
                                        </tr>

                                        <!-- row3 -->

                                        <tr>
                                            <td rowspan="3">سعد شداد هليل</td>
                                            <td>الالتزام:100%</td>
                                        </tr>
                                        <tr>
                                            <td>الهاتف:</td>
                                        </tr>
                                        <tr>
                                            <td>الملاحظات:</td>
                                        </tr>

                                        <!-- row4 -->

                                        <tr>
                                            <td rowspan="3">على فهد العباريد</td>
                                            <td>الالتزام:100%</td>
                                        </tr>
                                        <tr>
                                            <td>الهاتف:</td>
                                        </tr>
                                        <tr>
                                            <td>الملاحظات:</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>
@endsection
