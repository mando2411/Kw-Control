@extends('layouts.dashboard.app')

@section('content')
    <link rel="stylesheet" href="/assets/css/contractors-modern.css?v=20260215e">
    <link rel="stylesheet" href="/assets/css/export-modal.css?v=20260215b">

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">


    <section class="py-1 my-1 rtl">
        <div class="container mt-3 contractors-modern-page">
            <div class="cm-head cm-anim cm-anim-delay-1">
                <h2 class="cm-title">إدارة المتعهدين</h2>
                <div class="cm-sub">نسخة حديثة بواجهة أنعم وتجربة أسرع مع الحفاظ على نفس الوظائف الحالية.</div>
            </div>
            <x-dashboard.partials.message-alert />

            @php
                $listManagementSelectedCandidateUserIds = collect($selectedCandidateUserIds ?? [])->map(fn($v) => (int) $v)->filter()->values()->all();
                $isSingleListManagementSelection = empty($isListManagementContext) || count($listManagementSelectedCandidateUserIds) === 1;
                $singleListManagementCandidateUserId = (!empty($isListManagementContext) && $isSingleListManagementSelection)
                    ? (int) ($listManagementSelectedCandidateUserIds[0] ?? 0)
                    : null;
            @endphp

            <style>
                .list-candidate-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: .55rem;
                    border: 1px solid rgba(148, 163, 184, .36);
                    border-radius: 999px;
                    padding: .35rem .6rem;
                    background: #fff;
                    min-height: 56px;
                    transition: all .2s ease;
                }

                .list-candidate-pill:hover {
                    border-color: #3b82f6;
                    box-shadow: 0 8px 20px rgba(37, 99, 235, .14);
                }

                .list-candidate-pill.is-selected {
                    border-color: #2563eb;
                    background: rgba(37, 99, 235, .08);
                }

                .list-candidate-pill__avatar {
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    background-size: cover;
                    background-position: center;
                    background-color: #e2e8f0;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    color: #334155;
                    font-size: 14px;
                }

                .list-candidate-pill__meta .name {
                    font-size: .85rem;
                    font-weight: 800;
                    color: #0f172a;
                    line-height: 1.1;
                }

                .list-candidate-pill__meta small {
                    font-size: .73rem;
                    color: #64748b;
                    font-weight: 700;
                }
            </style>

            @if(!empty($isListManagementContext) && isset($listManagementCandidates) && $listManagementCandidates->count())
                @php
                    $allSelected = count($listManagementSelectedCandidateUserIds) === $listManagementCandidates->pluck('user_id')->filter()->unique()->count();
                @endphp
                <form action="{{ route('dashboard.list-management') }}" method="GET" id="list-management-candidates-filter" class="card border-0 shadow-sm mb-3" style="border-radius:14px; overflow:hidden;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <h6 class="mb-0" style="font-weight:900;">مرشحو القائمة (فلتر متعدد)</h6>
                            <span id="list-management-filter-state" class="small text-muted" aria-live="polite"></span>
                        </div>

                        <div class="d-flex flex-wrap gap-2 align-items-stretch">
                            <label class="list-candidate-pill {{ $allSelected ? 'is-selected' : '' }}" style="cursor:pointer;">
                                <input type="checkbox" value="all" id="candidate-select-all" {{ $allSelected ? 'checked' : '' }} hidden>
                                <div class="list-candidate-pill__avatar">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="list-candidate-pill__meta">
                                    <div class="name">الكل</div>
                                    <small>عرض كل مرشحي القائمة</small>
                                </div>
                            </label>

                            @foreach($listManagementCandidates as $candidateFilterItem)
                                @php
                                    $candidateUser = $candidateFilterItem->user;
                                    $candidateUserId = (int) ($candidateFilterItem->user_id ?? 0);
                                    $isSelected = in_array($candidateUserId, $listManagementSelectedCandidateUserIds, true);
                                    $avatar = $candidateUser?->image ?: ('https://ui-avatars.com/api/?name=' . urlencode($candidateUser?->name ?? 'Candidate') . '&background=2563eb&color=fff&size=180');
                                @endphp
                                <label class="list-candidate-pill {{ $isSelected ? 'is-selected' : '' }}" style="cursor:pointer;">
                                    <input type="checkbox" name="candidate_users[]" value="{{ $candidateUserId }}" class="candidate-filter-checkbox" {{ $isSelected ? 'checked' : '' }} hidden>
                                    <div class="list-candidate-pill__avatar" style="background-image:url('{{ $avatar }}');"></div>
                                    <div class="list-candidate-pill__meta">
                                        <div class="name">{{ $candidateUser?->name ?? 'مرشح' }}</div>
                                        <small>{{ $candidateFilterItem->id == ($currentListLeaderCandidate->id ?? 0) ? 'رئيس القائمة' : 'عضو قائمة' }}</small>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            @endif

            @if(!empty($isListManagementContext))
                <div id="list-management-single-candidate-warning" class="alert alert-warning mb-3 {{ $isSingleListManagementSelection ? 'd-none' : '' }}">
                    يجب اختيار مرشح واحد فقط
                </div>
            @endif

            <form id="mota3ahdeenControlForm" class="mota3ahdeenControl cm-anim cm-anim-delay-2" action="{{ route('dashboard.contractors.store') }}" method="POST">
                @csrf
                @if(!empty($isListManagementContext))
                    <input type="hidden" name="candidate_user_id" id="list-management-target-candidate-user" value="{{ $singleListManagementCandidateUserId ?? '' }}">
                @endif
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
                                    value="{{ App\Models\Role::findByName('بحث في الكشوف')?->name ?? '' }}" checked>
                                <label class="labelStyle" class="w-100 me-3" for="canSearch">تفعيل امكانية البحث</label>
                            </div>
                            <div class=" d-flex align-items-center">
                                <input type="checkbox" name="roles[]" id="canDelet"
                                    value="{{ App\Models\Role::findByName('حذف المضامين')?->name ?? '' }}" checked>
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
				
    <button id="exportExcelBtn" class="btn btn-primary">
   استخداج اكسيل
   </button>

				
				
				
            </div>

            <!-- Modal foundedNow-->
            <div class="modal modal-md rtl" id="foundedNow" tabindex="-1" aria-labelledby="foundedNowLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="foundedNowLabel">
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
            <div class="modal modal-md rtl" id="Mota3ahdeenList" tabindex="-1" aria-labelledby="mota3ahdeenListLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="mota3ahdeenListLabel">
                                بيانات المتعهد
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                                <input type="checkbox" class="" name="printMota3ahedData" id="mota3aheedName2">
                                <label class="labelStyle" for="mota3aheedName2">اسم المتعهد</label>
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
            <div id="list-management-contractors-table-wrap" class="table-responsive mt-4 cm-anim cm-anim-delay-3">
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
                                "
                                data-contractor-id="{{ $i->id }}"
                                data-contractor-url="{{ route('con-profile', $i->token) }}">
                                <td>
                                    <input type="checkbox" class="check" name="madameenNameChecked" />
                                </td>
                                <input type="hidden" class="js-contractor-url" data-url="{{ route('con-profile', $i->token) }}">
                                <td class="d-none js-contractor-id">{{ $i->id }}</td>
                                <td data-bs-toggle="modal" data-bs-target="#mota3ahdeenDataModern" class="contractor-open-cell">
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
            <div class="modal modal-lg rtl" id="mota3ahdeenDataModern" tabindex="-1" aria-labelledby="mota3ahdeenDataModernLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="mota3ahdeenDataModernLabel">
                                بيانات المتعهد
                            </h1>
                            <div id="contractorModalStatus" class="cm-modal-status" aria-live="polite"></div>
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
                              <!-- ش <span class="bg-body-secondary p-1 px-3 rounded-pill me-2"
                                    id="con-url">md-kw.com/./?q=NTEy</span> -->
                            </div>
                            <div class="text-center">
                                <a href="#" id="RedirectLink" class="btn btn-secondary mb-1" target="_blank" rel="noopener noreferrer">
                                    <i class="fa fa-book"></i>
                                    <span>الدخول للرابط</span>
                                </a>

                                <button type="button" class="btn btn-secondary mb-1" id="copyConUrlBtn">
                                    <i class="fa fa-book"></i>
                                    <span id="copyConUrlText">نسخ الرابط</span>
                                </button>
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
                                <div class="cm-modal-panels text-end">
                                    <div class="Delete_names d-none">
                                        <h5 class="cm-panel-toggle cPointer">
                                            <span class="cm-label"><i class="fa fa-trash"></i> أسماء محذوفة</span>
                                            <span class="cm-count" id="deletes_count">3</span>
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
                                        <h5 class="cm-panel-toggle cPointer">
                                            <span class="cm-label"><i class="fa fa-clock-rotate-left"></i> سجل عمليات البحث</span>
                                            <span class="cm-count" id="log_count">0</span>
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
                                        <button class="btn btn-primary px-3" id="all_voters_modern">تنفيذ</button>

                                    </div>
                                </form>

                                <button type="button" id="smOpenExport" class="my-2 btn btn-dark"> استخراج الكشوف</button>


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

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>

                <div class="modal fade rtl sm-export-modal" id="smExportModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                        <div class="modal-header">
                                                <div>
                                                        <h5 class="sm-export-title">استخراج الكشوف</h5>
                                                        <p class="sm-export-sub">حدد الأعمدة ونوع الإخراج ثم صدّر النتائج المحددة.</p>
                                                </div>
                                                <button type="button" id="smExportCloseBtn" class="btn-close" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                                <form action="{{ route('export') }}" method="GET" id="smExportForm">
                                                    <input type="hidden" name="search_id" id="smExportSearchId" value="">
                                                        <input type="hidden" name="source" value="contractors">

                                                        <div class="sm-export-section">
                                                            <h6 class="sm-export-section-title">أعمدة الكشف</h6>
                                                                <div class="row g-2">
                                                                <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" checked disabled type="checkbox" value="name"><span class="sm-chip-pill">اسم الناخب</span></label></div>
                                                                <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" checked type="checkbox" name="columns[]" value="family"><span class="sm-chip-pill">العائلة</span></label></div>
                                                                <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="age"><span class="sm-chip-pill">العمر</span></label></div>
                                                                <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="phone"><span class="sm-chip-pill">الهاتف</span></label></div>
                                                                <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="type"><span class="sm-chip-pill">الجنس</span></label></div>
                                                                <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="madrasa"><span class="sm-chip-pill">مدرسة الانتخاب</span></label></div>
                                                                <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" checked type="checkbox" name="columns[]" value="restricted"><span class="sm-chip-pill">حالة القيد</span></label></div>
                                                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="created_at"><span class="sm-chip-pill">تاريخ القيد</span></label></div>
                                                            <div class="col-6"><label class="sm-chip-option"><input class="sm-chip-input" type="checkbox" name="columns[]" value="checked_time"><span class="sm-chip-pill">وقت التصويت</span></label></div>
                                                                </div>
                                                        </div>

                                                        <div class="sm-export-section">
                                                            <h6 class="sm-export-section-title">ترتيب النتائج</h6>
                                                                <select name="sorted" class="form-select">
                                                                        <option value="asc">أبجدي</option>
                                                                        <option value="phone">الهاتف</option>
                                                                        <option value="commitment">الالتزام</option>
                                                                </select>
                                                        </div>

                                                        <input type="hidden" name="type" id="smExportType">

                                                        <div class="sm-export-section">
                                                            <h6 class="sm-export-section-title">إجراء الإخراج</h6>
                                                            <div class="sm-export-actions">
                                                                <button type="button" class="btn btn-primary sm-export-action" value="PDF">PDF</button>
                                                                <button type="button" class="btn btn-success sm-export-action" value="Excel">Excel</button>
                                                                <button type="button" class="btn btn-secondary sm-export-action" value="print">طباعة</button>
                                                                <button type="button" class="btn btn-secondary sm-export-action" value="show">عرض</button>
                                                                </div>
                                                        </div>

                                                        <p class="text-danger small mb-2">* ملاحظة: لا يمكن استخراج البيانات الضخمة عبر ملف PDF</p>

                                                        <div class="sm-export-section mb-0">
                                                            <h6 class="sm-export-section-title">إرسال PDF عبر WhatsApp</h6>
                                                                <div class="d-flex gap-2 align-items-center">
                                                                        <input type="number" class="form-control" name="to" placeholder="رقم الهاتف لإرسال WhatsApp">
                                                                <button type="button" class="btn btn-outline-primary sm-export-action" value="Send">إرسال</button>
                                                                </div>
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
        (function () {
            if (!window.jQuery || !window.axios) return;
            if (window.__contractorsModernPageInit) return;
            window.__contractorsModernPageInit = true;

            var $ = window.jQuery;
            var pageRoot = document.querySelector('.contractors-modern-page');
            if (!pageRoot) return;
            var modalEl = document.getElementById('mota3ahdeenDataModern');
            if (!modalEl) return;

            var modalStatus = document.getElementById('contractorModalStatus');
            var currentContractorId = null;
            var currentContractorUrl = '';
            var openConUrlBtn = document.getElementById('RedirectLink');
            var copyConUrlBtn = document.getElementById('copyConUrlBtn');
            var copyConUrlText = document.getElementById('copyConUrlText');
            var copyConUrlResetTimer = null;
            var trustDebounce = null;
            var rowCache = {};
            var selectedVoterIdsCache = [];
            var exportModalElement = document.getElementById('smExportModal');
            var exportForm = document.getElementById('smExportForm');
            var exportType = document.getElementById('smExportType');
            var exportSearchId = document.getElementById('smExportSearchId');
            var exportCloseBtn = document.getElementById('smExportCloseBtn');
            var exportOpenBtn = document.getElementById('smOpenExport');
            var exportAsyncUrl = '{{ route('dashboard.statement.export-async') }}';
            var isOpeningExportModal = false;
            var shouldCloseParentAfterExport = false;

            function getListManagementSelectedCandidateIds() {
                return Array.from(document.querySelectorAll('#list-management-candidates-filter .candidate-filter-checkbox:checked'))
                    .map(function (el) { return String(el.value || '').trim(); })
                    .filter(function (value) { return value !== ''; });
            }

            function ensureSingleListManagementSelection() {
                var filterForm = document.getElementById('list-management-candidates-filter');
                if (!filterForm) return true;

                var selectedIds = getListManagementSelectedCandidateIds();
                if (selectedIds.length === 1) {
                    return true;
                }

                showStatus('يجب اختيار مرشح واحد فقط', 'error');
                if (window.toastr) toastr.error('يجب اختيار مرشح واحد فقط');
                return false;
            }

            function isModalVisible(element) {
                if (!element) return false;
                return element.classList.contains('show') || element.style.display === 'block';
            }

            function getModalInstance(element, options) {
                if (!element) return null;

                if (window.bootstrap && window.bootstrap.Modal) {
                    if (window.bootstrap.Modal.getOrCreateInstance) {
                        return window.bootstrap.Modal.getOrCreateInstance(element, options || {});
                    }

                    return new window.bootstrap.Modal(element, options || {});
                }

                return null;
            }

            function hideParentModal() {
                if (!modalEl) return;

                var modal = getModalInstance(modalEl);
                if (modal && typeof modal.hide === 'function') {
                    modal.hide();
                    return;
                }

                if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                    window.jQuery(modalEl).modal('hide');
                }
            }

            function setExportModalLayeredState(enabled) {
                if (!document.body) return;

                document.body.classList.toggle('contractor-export-modal-layered', !!enabled);

                if (!pageRoot) return;

                pageRoot.classList.toggle('export-modal-layered', !!enabled);

                if (!enabled) {
                    document.querySelectorAll('.modal-backdrop.sm-export-backdrop').forEach(function (backdrop) {
                        backdrop.classList.remove('sm-export-backdrop');
                    });
                }
            }

            function showExportStatus(message, tone) {
                if (!window.toastr) return;
                if (tone === 'error') {
                    toastr.error(message || 'حدث خطأ غير متوقع');
                    return;
                }
                if (tone === 'success') {
                    toastr.success(message || 'تم التنفيذ بنجاح');
                    return;
                }
                toastr.info(message || 'جارٍ التنفيذ...');
            }

            function clearExportStatus(delay) {}

            function setCopyButtonState(text, tone) {
                if (!copyConUrlBtn || !copyConUrlText) return;

                if (copyConUrlResetTimer) {
                    clearTimeout(copyConUrlResetTimer);
                    copyConUrlResetTimer = null;
                }

                copyConUrlBtn.classList.remove('btn-secondary', 'btn-success', 'btn-danger');
                copyConUrlBtn.classList.add(tone === 'success' ? 'btn-success' : (tone === 'error' ? 'btn-danger' : 'btn-secondary'));
                copyConUrlText.textContent = text;

                if (tone === 'success' || tone === 'error') {
                    copyConUrlResetTimer = setTimeout(function () {
                        if (!copyConUrlBtn || !copyConUrlText) return;
                        copyConUrlBtn.classList.remove('btn-success', 'btn-danger');
                        copyConUrlBtn.classList.add('btn-secondary');
                        copyConUrlText.textContent = 'نسخ الرابط';
                    }, 1200);
                }
            }

            async function copyTextToClipboard(text) {
                if (!text) return false;

                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                    return true;
                }

                var tempInput = document.createElement('textarea');
                tempInput.value = text;
                tempInput.setAttribute('readonly', '');
                tempInput.style.position = 'absolute';
                tempInput.style.left = '-9999px';
                document.body.appendChild(tempInput);
                tempInput.select();
                tempInput.setSelectionRange(0, tempInput.value.length);
                var copied = false;

                try {
                    copied = document.execCommand('copy');
                } finally {
                    document.body.removeChild(tempInput);
                }

                return copied;
            }

            function closeExportModal() {
                if (!exportModalElement) return;

                if (window.bootstrap && window.bootstrap.Modal) {
                    var modal = window.bootstrap.Modal.getInstance
                        ? window.bootstrap.Modal.getInstance(exportModalElement)
                        : getModalInstance(exportModalElement, { backdrop: true, focus: true, keyboard: true });

                    if (!modal) {
                        modal = getModalInstance(exportModalElement, { backdrop: true, focus: true, keyboard: true });
                    }

                    if (modal && typeof modal.hide === 'function') {
                        modal.hide();
                        return;
                    }
                }

                if (window.jQuery && typeof window.jQuery(exportModalElement).modal === 'function') {
                    window.jQuery(exportModalElement).modal('hide');
                }
            }

            function openExportModal() {
                if (!exportModalElement) return;

                if (window.bootstrap && window.bootstrap.Modal) {
                    var modal = getModalInstance(exportModalElement, {
                        backdrop: true,
                        focus: true,
                        keyboard: true
                    });

                    if (modal && typeof modal.show === 'function') {
                        modal.show();
                        return;
                    }
                }

                if (window.jQuery && typeof window.jQuery(exportModalElement).modal === 'function') {
                    window.jQuery(exportModalElement).modal('show');
                }
            }

            if (openConUrlBtn) {
                openConUrlBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    if (!currentContractorUrl) return;
                    window.open(currentContractorUrl, '_blank', 'noopener,noreferrer');
                });
            }

            if (copyConUrlBtn) {
                copyConUrlBtn.addEventListener('click', async function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    if (!currentContractorUrl) {
                        setCopyButtonState('لا يوجد رابط', 'error');
                        return;
                    }

                    setCopyButtonState('جاري النسخ...', 'idle');

                    try {
                        var isCopied = await copyTextToClipboard(currentContractorUrl);
                        setCopyButtonState(isCopied ? 'تم النسخ ✓' : 'فشل النسخ', isCopied ? 'success' : 'error');
                    } catch (error) {
                        setCopyButtonState('فشل النسخ', 'error');
                    }
                });
            }

            function showStatus(message, tone) {
                if (!modalStatus) return;
                modalStatus.textContent = message || '';
                modalStatus.classList.add('is-visible');

                modalStatus.style.background = tone === 'error'
                    ? 'rgba(239, 68, 68, 0.12)'
                    : tone === 'success'
                        ? 'rgba(16, 185, 129, 0.12)'
                        : 'rgba(14, 165, 233, 0.10)';

                modalStatus.style.borderColor = tone === 'error'
                    ? 'rgba(239, 68, 68, 0.30)'
                    : tone === 'success'
                        ? 'rgba(16, 185, 129, 0.30)'
                        : 'rgba(14, 165, 233, 0.24)';

                modalStatus.style.color = tone === 'error'
                    ? 'rgba(153, 27, 27, 0.95)'
                    : tone === 'success'
                        ? 'rgba(6, 95, 70, 0.95)'
                        : 'rgba(3, 105, 161, 0.95)';
            }

            function clearStatus(delay) {
                setTimeout(function () {
                    if (!modalStatus) return;
                    modalStatus.classList.remove('is-visible');
                }, delay || 900);
            }

            function getSelectedVoterIdsForExport() {
                var ids = [];
                document.querySelectorAll('#voters_con input[type="checkbox"]:checked').forEach(function (element) {
                    var value = String(element && element.value ? element.value : '').trim();
                    if (!value || value === 'on') return;
                    ids.push(value);
                });

                var unique = Array.from(new Set(ids));
                if (unique.length) {
                    selectedVoterIdsCache = unique.slice();
                    return unique;
                }

                return selectedVoterIdsCache.slice();
            }

            function renderModal(response) {
                var user = response.data.user;
                var voters = user.voters || [];
                var softDelete = user.softDelete || [];
                var logs = user.logs || [];

                $('#last_id').val(user.id);
                $('#nameMota3ahed').val(user.name || '');
                $('#phoneMota3ahed').val(user.phone || '');
                $('#trustingRate').val(user.trust || 0);
                $('#trustText').text(user.trust || 0);
                $('#noteMota3ahed').val(user.note || '');
                $('#changparent_id').val(user.parent || '');
                $('#delete-con').val(user.id || '');
                $('#voters_numberss').text(' ' + voters.length + ' ');
                $('#deletes_count').text(' ' + softDelete.length + ' ');
                $('#log_count').text(' ' + logs.length + ' ');

                $('.Delete_names').toggleClass('d-none', softDelete.length === 0);
                $('.Search_logs').toggleClass('d-none', logs.length === 0);

                $('#moltazem').prop('checked', user.status == 1);
                $('#follow').prop('checked', user.status != 1);

                $('#moltazem-l').toggleClass('btn-success', user.status == 1).toggleClass('btn-outline-success', user.status != 1);
                $('#follow-l').toggleClass('btn-warning', user.status != 1).toggleClass('btn-outline-warning', user.status == 1);

                $('#settingCanSearch').prop('checked', !!user.can_search);
                $('#settingCanDelet').prop('checked', !!user.can_delete);

                $('#con-url').text(currentContractorUrl || '');
                $('#RedirectLink').attr('href', currentContractorUrl || '#');

                var phoneNumber = Number(String(user.phone || '').replace(/\s+/g, '')) || '';
                $('#phone_wa').val(phoneNumber);
                var candidateLine = (user.creator || '') + ': اخوكم المرشح';
                var message = ($('#message').val() || '') + candidateLine;

                function bindWhatsapp(phone) {
                    document.getElementById('whatsapp-link').href = 'https://wa.me/965' + phone + '?text=' + encodeURIComponent(message) + '%0A%0A' + encodeURIComponent(currentContractorUrl || '');
                }
                bindWhatsapp(phoneNumber);

                var votersHtml = '';
                voters.forEach(function (voter, idx) {
                    var status = '';
                    if (voter.status === true || voter.status === 1) {
                        var updatedAt = new Date(voter.updated_at);
                        var formattedDate = updatedAt.toLocaleDateString('en-GB') + ' ' + updatedAt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                        status = '<span><i class="fa fa-check-square text-success ms-1"></i>تم التصويت </span><span>' + formattedDate + '</span>';
                    }

                    votersHtml += '<tr>' +
                        '<td><input type="checkbox" class="check" name="voter[]" value="' + voter.id + '" /></td>' +
                        '<td>' + (idx + 1) + '</td>' +
                        '<td>' + (voter.name || '') + '</td>' +
                        '<td>' + ((voter.pivot && voter.pivot.percentage) ? voter.pivot.percentage : 0) + '%</td>' +
                        '<td>' + (voter.phone1 || '') + '</td>' +
                        '<td>' + status + '</td>' +
                        '</tr>';
                });
                document.getElementById('voters_con').innerHTML = votersHtml;
                selectedVoterIdsCache = [];

                var deletedHtml = '';
                softDelete.forEach(function (voter, idx) {
                    deletedHtml += '<tr><td>' + (idx + 1) + '</td><td>' + (voter.name || '') + '</td><td>' + (voter.phone1 || '') + '</td><td></td></tr>';
                });
                document.getElementById('deletes_data').innerHTML = deletedHtml;

                var logsHtml = '';
                logs.forEach(function (log) {
                    logsHtml += '<tr><td>' + (log.description || '') + '</td><td>' + (log.created_at || '') + '</td></tr>';
                });
                document.getElementById('log_data').innerHTML = logsHtml;

                if (exportSearchId) {
                    exportSearchId.value = String(user.id || '');
                }
            }

            function loadContractorData(contractorId, contractorUrl) {
                if (!contractorId) return;
                currentContractorId = contractorId;
                currentContractorUrl = contractorUrl || '';
                showStatus('جاري تحميل البيانات...', 'info');

                return axios.get('/con/' + contractorId)
                    .then(function (response) {
                        renderModal(response);
                        showStatus('تم تحميل البيانات', 'success');
                        clearStatus(700);
                    })
                    .catch(function (error) {
                        console.error(error);
                        showStatus('تعذر تحميل بيانات المتعهد', 'error');
                    });
            }

            function saveField(fieldName, fieldValue) {
                if (!currentContractorId) return Promise.resolve();
                if (!ensureSingleListManagementSelection()) return Promise.resolve();
                showStatus('جاري الحفظ...', 'info');

                return axios.post('/dashboard/mot-up/' + currentContractorId, {
                    name: fieldName,
                    value: fieldValue
                }).then(function () {
                    showStatus('تم الحفظ', 'success');
                    clearStatus(700);
                }).catch(function (error) {
                    console.error(error);
                    showStatus('فشل الحفظ', 'error');
                });
            }

            $(document).off('click.contractorOpen', 'td[data-bs-target="#mota3ahdeenDataModern"]');
            $(document).on('click.contractorOpen', 'td[data-bs-target="#mota3ahdeenDataModern"]', function () {
                var row = $(this).closest('tr');
                var contractorId = row.data('contractor-id') || $.trim(row.find('.js-contractor-id').text());
                var contractorUrl = row.data('contractor-url') || (row.find('.js-contractor-url').data('url') || '');

                if (contractorId) {
                    rowCache[String(contractorId)] = row;
                }

                loadContractorData(contractorId, contractorUrl);
            });

            $('#phone_wa').off('change.contractorWa').on('change.contractorWa', function () {
                var phone = $(this).val();
                var candidateLine = ($('#message').val() || '') + '';
                document.getElementById('whatsapp-link').href = 'https://wa.me/965' + phone + '?text=' + encodeURIComponent(candidateLine) + '%0A%0A' + encodeURIComponent(currentContractorUrl || '');
            });

            $('[data-bs-target="edit-mot"]').off('change.contractorEdit').on('change.contractorEdit', function () {
                var fieldName = $(this).attr('name') || this.tagName.toLowerCase();
                var fieldValue = $(this).val();
                saveField(fieldName, fieldValue);
            });

            $('#settingCanSearch, #settingCanDelet')
                .off('change.contractorPermissions')
                .on('change.contractorPermissions', function () {
                    var fieldName = this.id;
                    var fieldValue = $(this).is(':checked') ? 1 : 0;
                    saveField(fieldName, fieldValue);
                });

            $('#trustingRate').off('input.contractorTrust').on('input.contractorTrust', function () {
                $('#trustText').text($(this).val());
            });

            $('#trustingRate').off('change.contractorTrustSave').on('change.contractorTrustSave', function () {
                var value = $(this).val();
                if (trustDebounce) clearTimeout(trustDebounce);
                trustDebounce = setTimeout(function () {
                    saveField('trust', value);
                }, 160);
            });

            $('#delete-con').off('click.contractorDelete').on('click.contractorDelete', function (event) {
                event.preventDefault();
                if (!currentContractorId) return;
                if (!ensureSingleListManagementSelection()) return;
                showStatus('جاري حذف المتعهد...', 'info');

                axios.delete('/dashboard/contractors/' + currentContractorId)
                    .then(function () {
                        var key = String(currentContractorId);
                        if (rowCache[key] && rowCache[key].length) {
                            rowCache[key].remove();
                        }

                        if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getOrCreateInstance) {
                            var modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.hide();
                        } else if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                            window.jQuery(modalEl).modal('hide');
                        }
                        showStatus('تم الحذف بنجاح', 'success');
                        if (window.toastr) toastr.success('تم حذف المتعهد بنجاح');
                    })
                    .catch(function (error) {
                        console.error(error);
                        showStatus('تعذر حذف المتعهد', 'error');
                    });
            });

            $('#all_voters_modern').off('click.contractorTransfer').on('click.contractorTransfer', function (event) {
                event.preventDefault();
                if (!currentContractorId) return;
                if (!ensureSingleListManagementSelection()) return;

                var selected = $('#addMota3ahed').val();
                var voters = getSelectedVoterIdsForExport();

                if (!selected || !voters.length) {
                    showStatus('اختر إجراء ومحددات أولاً', 'error');
                    return;
                }

                var endpoint = selected === 'delete' ? '/delete/mad' : '/ass/' + selected;
                var payload = selected === 'delete'
                    ? { id: currentContractorId, voter: voters }
                    : { id: currentContractorId, voter: voters };

                showStatus('جاري تنفيذ العملية...', 'info');

                axios.post(endpoint, payload)
                    .then(function () {
                        if (window.toastr) toastr.success('تم تنفيذ العملية بنجاح');
                        return loadContractorData(currentContractorId, currentContractorUrl);
                    })
                    .catch(function (error) {
                        console.error(error);
                        showStatus('فشلت العملية', 'error');
                    });
            });

            $(document)
                .off('click.contractorLogsToggle', '#mota3ahdeenDataModern .Search_logs h5')
                .on('click.contractorLogsToggle', '#mota3ahdeenDataModern .Search_logs h5', function () {
                    $(this).siblings('table').toggleClass('d-none');
                });

            $(document)
                .off('click.contractorDeletedToggle', '#mota3ahdeenDataModern .Delete_names h5')
                .on('click.contractorDeletedToggle', '#mota3ahdeenDataModern .Delete_names h5', function () {
                    $(this).siblings('table').toggleClass('d-none');
                });

            modalEl.addEventListener('hidden.bs.modal', function () {
                if (isOpeningExportModal) {
                    return;
                }

                if (exportModalElement && (exportModalElement.classList.contains('show') || exportModalElement.style.display === 'block')) {
                    return;
                }

                currentContractorId = null;
                currentContractorUrl = '';
                document.getElementById('voters_con').innerHTML = '';
                document.getElementById('deletes_data').innerHTML = '';
                document.getElementById('log_data').innerHTML = '';
                selectedVoterIdsCache = [];
                if (modalStatus) modalStatus.classList.remove('is-visible');
            });

            modalEl.addEventListener('hide.bs.modal', function (event) {
                if (!isModalVisible(exportModalElement)) {
                    return;
                }

                event.preventDefault();
                closeExportModal();
            });

            if (exportOpenBtn) {
                exportOpenBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    shouldCloseParentAfterExport = false;

                    selectedVoterIdsCache = getSelectedVoterIdsForExport();

                    if (!selectedVoterIdsCache.length) {
                        showExportStatus('اختر ناخبًا واحدًا على الأقل قبل فتح خيارات الاستخراج', 'error');
                        return;
                    }

                    isOpeningExportModal = true;
                    openExportModal();
                    setTimeout(function () {
                        isOpeningExportModal = false;
                    }, 250);
                });
            }

            if (exportCloseBtn) {
                exportCloseBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    shouldCloseParentAfterExport = false;
                    closeExportModal();
                });
            }

            var parentCloseBtn = modalEl.querySelector('.modal-header .btn-close[data-bs-dismiss="modal"]');
            if (parentCloseBtn) {
                parentCloseBtn.addEventListener('click', function () {
                    shouldCloseParentAfterExport = isModalVisible(exportModalElement);
                });
            }

            if (exportModalElement) {
                exportModalElement.addEventListener('show.bs.modal', function () {
                    setExportModalLayeredState(true);

                    if (exportSearchId) {
                        exportSearchId.value = currentContractorId ? String(currentContractorId) : '';
                    }

                    selectedVoterIdsCache = getSelectedVoterIdsForExport();
                });

                exportModalElement.addEventListener('shown.bs.modal', function () {
                    var backdrops = document.querySelectorAll('.modal-backdrop');
                    var latestBackdrop = backdrops.length ? backdrops[backdrops.length - 1] : null;
                    if (latestBackdrop) {
                        latestBackdrop.classList.add('sm-export-backdrop');
                    }
                });

                exportModalElement.addEventListener('hide.bs.modal', function () {
                    setExportModalLayeredState(false);
                });

                exportModalElement.addEventListener('hidden.bs.modal', function () {
                    isOpeningExportModal = false;
                    setExportModalLayeredState(false);

                    if (shouldCloseParentAfterExport) {
                        shouldCloseParentAfterExport = false;
                        hideParentModal();
                    }
                });
            }

            $(document).off('click.contractorExportAction', '.sm-export-action').on('click.contractorExportAction', '.sm-export-action', function () {
                if (!exportForm || !exportType) return;

                var actionType = this.value;
                var submitBtn = this;
                var selectedIds = getSelectedVoterIdsForExport();

                if (!selectedIds.length) {
                    showExportStatus('اختر ناخبًا واحدًا على الأقل', 'error');
                    return;
                }

                exportType.value = actionType;
                exportForm.querySelectorAll('input[name="voter[]"]').forEach(function (input) { input.remove(); });

                selectedIds.forEach(function (id) {
                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'voter[]';
                    hidden.value = id;
                    exportForm.appendChild(hidden);
                });

                if (exportSearchId && !exportSearchId.value && currentContractorId) {
                    exportSearchId.value = String(currentContractorId);
                }

                var formData = new FormData(exportForm);
                var queryData = {};
                formData.forEach(function (value, key) {
                    if (Object.prototype.hasOwnProperty.call(queryData, key)) {
                        if (!Array.isArray(queryData[key])) {
                            queryData[key] = [queryData[key]];
                        }
                        queryData[key].push(value || '');
                    } else {
                        queryData[key] = value || '';
                    }
                });

                submitBtn.disabled = true;
                showExportStatus('جاري تنفيذ العملية...', 'info');

                if (actionType === 'Excel' || actionType === 'PDF') {
                    closeExportModal();
                    axios.post(exportAsyncUrl, formData, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(function (res) {
                            showExportStatus('بدأ تجهيز ملف كشوف المتعهدين في الخلفية. ستصلك إشعارات مميزة لكشوف المتعهدين عند الانتهاء.', 'success');
                        })
                        .catch(function (error) {
                            console.error(error);
                            showExportStatus(error?.response?.data?.message || 'تعذر بدء تجهيز الملف. حاول مرة أخرى.', 'error');
                        })
                        .finally(function () {
                            submitBtn.disabled = false;
                            clearExportStatus(1000);
                        });
                    return;
                }

                axios.get(exportForm.action, {
                    params: queryData,
                    responseType: actionType === 'Send' ? 'json' : 'text'
                })
                    .then(function (res) {
                        showExportStatus('تم تنفيذ العملية', 'success');

                        if (actionType === 'Send' && res.data?.Redirect_Url) {
                            var waTab = window.open();
                            if (waTab) {
                                waTab.location.href = res.data.Redirect_Url;
                            }
                            return;
                        }

                        var newTab = window.open();
                        if (newTab) {
                            if (typeof res.data === 'string') {
                                newTab.document.open();
                                newTab.document.write(res.data);
                                newTab.document.close();
                            } else if (res.data?.Redirect_Url) {
                                newTab.location.href = res.data.Redirect_Url;
                            }
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                        showExportStatus(error?.response?.data?.error || 'حدث خطأ غير متوقع', 'error');
                    })
                    .finally(function () {
                        submitBtn.disabled = false;
                        clearExportStatus(1000);
                    });
            });
        })();
    </script>
    <script>
        $(document)
            .off('click.contractorsSortButtons', '.Sort_Btn')
            .on('click.contractorsSortButtons', '.Sort_Btn', function () {
                var selectedType = $(this).find('input').val();
                $('#myTable tbody tr.all').addClass('d-none');

                if (selectedType === 'all') {
                    $('#myTable tbody tr.all').removeClass('d-none');
                    return;
                }

                $('#myTable tbody tr.' + selectedType).removeClass('d-none');
            });
    </script>


<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

 <script>
        function bindMyTableHeaderSort() {
        const table = document.getElementById("myTable");
        if (!table) return;

        if (!table.dataset.headerSortBound) {
            const headers = table.querySelectorAll("thead th");
            headers.forEach((header, columnIndex) => {
                header.addEventListener("click", function () {
                    sortTable(table, columnIndex);
                });
            });
            table.dataset.headerSortBound = "1";
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        bindMyTableHeaderSort();
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
        const searchBox = document.getElementById("searchBox");
        if (searchBox) {
            searchBox.oninput = function () {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll("#myTable tbody tr");

                rows.forEach(row => {
                    const cells = Array.from(row.cells);
                    const matches = cells.some(cell =>
                        cell.innerText.toLowerCase().includes(query)
                    );

                    row.style.display = matches ? "" : "none";
                });
            };
        }






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
	 
	 
	 
	 function exportTableToExcel(tableId, filename = "export.xls") {
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

    // Create a complete HTML structure for the Excel file
    const htmlTemplate = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <style>
                .rtl { direction: rtl; }
                .text-center { text-align: center; }
            </style>
        </head>
        <body>
            <table class="rtl text-center">
                ${tableHTML}
            </table>
        </body>
        </html>
    `;

    // Create a Blob object with the HTML content
    const blob = new Blob([htmlTemplate], { type: "application/vnd.ms-excel;charset=utf-8;" });

    // Create a download link and trigger the download
    const downloadLink = document.createElement("a");
    const url = URL.createObjectURL(blob);
    downloadLink.href = url;
    downloadLink.download = filename;

    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

    const exportExcelBtn = document.getElementById("exportExcelBtn");
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener("click", function (event) {
            event.preventDefault();
            exportTableToExcel("myTable", "contractors.xls");
        });
    }

    (function bindListManagementCandidateFilter() {
        var filterForm = document.getElementById('list-management-candidates-filter');
        if (!filterForm) return;

        var selectAll = document.getElementById('candidate-select-all');
        var stateEl = document.getElementById('list-management-filter-state');
        var addForm = document.getElementById('mota3ahdeenControlForm');
        var targetCandidateInput = document.getElementById('list-management-target-candidate-user');
        var warningBox = document.getElementById('list-management-single-candidate-warning');
        var candidateCheckboxes = Array.prototype.slice.call(
            filterForm.querySelectorAll('.candidate-filter-checkbox')
        );
        var activeRequestController = null;
        var requestCounter = 0;

        function setFilterState(message, isError) {
            if (!stateEl) return;
            stateEl.textContent = message || '';
            stateEl.classList.toggle('text-danger', !!isError);
            stateEl.classList.toggle('text-muted', !isError);
        }

        function refreshPillState(input) {
            if (!input) return;
            var label = input.closest('label.list-candidate-pill');
            if (!label) return;
            label.classList.toggle('is-selected', !!input.checked);
        }

        function getSelectedCandidateUserIds() {
            return candidateCheckboxes
                .filter(function (el) { return el.checked; })
                .map(function (el) { return String(el.value); });
        }

        function syncSingleCandidateActionsState() {
            var selectedIds = getSelectedCandidateUserIds();
            var isSingle = selectedIds.length === 1;

            if (targetCandidateInput) {
                targetCandidateInput.value = isSingle ? selectedIds[0] : '';
            }

            if (warningBox) {
                warningBox.classList.toggle('d-none', isSingle);
            }

            if (addForm) {
                addForm.classList.toggle('opacity-50', !isSingle);
            }

            return isSingle;
        }

        function buildFilterUrl() {
            var selectedIds = getSelectedCandidateUserIds();
            var url = new URL(filterForm.action, window.location.origin);
            selectedIds.forEach(function (id) {
                url.searchParams.append('candidate_users[]', id);
            });
            return url.toString();
        }

        function syncSelectFromFetchedDoc(doc, selectId) {
            var currentSelect = document.getElementById(selectId);
            var fetchedSelect = doc.getElementById(selectId);
            if (!currentSelect || !fetchedSelect) return;
            currentSelect.innerHTML = fetchedSelect.innerHTML;
        }

        function syncTableFromFetchedDoc(doc) {
            var currentWrap = document.getElementById('list-management-contractors-table-wrap');
            var fetchedWrap = doc.getElementById('list-management-contractors-table-wrap');
            if (!currentWrap || !fetchedWrap) return;
            currentWrap.innerHTML = fetchedWrap.innerHTML;
            if (typeof bindMyTableHeaderSort === 'function') {
                bindMyTableHeaderSort();
            }
        }

        function applySelectionAjax() {
            var requestId = ++requestCounter;
            var url = buildFilterUrl();

            if (activeRequestController) {
                activeRequestController.abort();
            }

            activeRequestController = new AbortController();
            setFilterState('جاري التحديث...');

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                signal: activeRequestController.signal
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.text();
                })
                .then(function (html) {
                    if (requestId !== requestCounter) return;

                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    syncTableFromFetchedDoc(doc);
                    syncSelectFromFetchedDoc(doc, 'parent_id');
                    syncSelectFromFetchedDoc(doc, 'changparent_id');
                    syncSelectFromFetchedDoc(doc, 'addMota3ahed');

                    if (window.history && typeof window.history.replaceState === 'function') {
                        window.history.replaceState(null, '', url);
                    }

                    setFilterState('تم التحديث');
                    setTimeout(function () {
                        if (requestId === requestCounter) setFilterState('');
                    }, 1200);
                })
                .catch(function (error) {
                    if (error && error.name === 'AbortError') return;
                    console.error(error);
                    setFilterState('تعذر تحديث البيانات', true);
                });
        }

        function syncAllStateFromCandidates() {
            if (!selectAll) return;
            var checkedCount = candidateCheckboxes.filter(function (el) { return el.checked; }).length;
            selectAll.checked = candidateCheckboxes.length > 0 && checkedCount === candidateCheckboxes.length;
            refreshPillState(selectAll);
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                var checked = !!selectAll.checked;
                candidateCheckboxes.forEach(function (el) {
                    el.checked = checked;
                    refreshPillState(el);
                });
                refreshPillState(selectAll);
                syncSingleCandidateActionsState();
                applySelectionAjax();
            });
        }

        candidateCheckboxes.forEach(function (el) {
            el.addEventListener('change', function () {
                refreshPillState(el);
                syncAllStateFromCandidates();
                syncSingleCandidateActionsState();
                applySelectionAjax();
            });
            refreshPillState(el);
        });

        if (addForm) {
            addForm.addEventListener('submit', function (event) {
                if (!syncSingleCandidateActionsState()) {
                    event.preventDefault();
                    setFilterState('يجب اختيار مرشح واحد فقط', true);
                    if (window.toastr) toastr.error('يجب اختيار مرشح واحد فقط');
                }
            });
        }

        filterForm.addEventListener('submit', function (event) {
            event.preventDefault();
            applySelectionAjax();
        });

        syncAllStateFromCandidates();
        syncSingleCandidateActionsState();
    })();

	 
	
	 
    </script>



@endpush
