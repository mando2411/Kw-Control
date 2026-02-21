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

                $listManagementContractorCountsByCreator = [];
                if (!empty($isListManagementContext) && isset($listManagementCandidates) && $listManagementCandidates->count()) {
                    $candidateUserIdsForCounts = $listManagementCandidates
                        ->pluck('user_id')
                        ->filter()
                        ->map(fn($value) => (int) $value)
                        ->unique()
                        ->values();

                    $listManagementContractorCountsByCreator = \App\Models\Contractor::withoutGlobalScopes()
                        ->whereNotNull('parent_id')
                        ->whereIn('creator_id', $candidateUserIdsForCounts->all())
                        ->selectRaw('creator_id, COUNT(*) as total')
                        ->groupBy('creator_id')
                        ->pluck('total', 'creator_id')
                        ->map(fn($value) => (int) $value)
                        ->toArray();
                }
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

                @media (max-width: 767.98px) {
                    #list-management-voters-section.is-mobile-compact .lm-col-extra {
                        display: none !important;
                    }

                    #list-management-add-voters-section.is-mobile-compact .lm-add-col-extra {
                        display: none !important;
                    }
                }

                .list-management-voters-table thead th {
                    font-size: 1rem;
                    font-weight: 800;
                    color: #0f172a;
                }

                .list-management-voters-table tbody td {
                    font-size: .98rem;
                    font-weight: 700;
                    color: #1e293b;
                }

                #lm-add-voters-table thead th {
                    font-size: 1rem;
                    font-weight: 800;
                    color: #0f172a;
                }

                #lm-add-voters-table tbody td {
                    font-size: .98rem;
                    font-weight: 700;
                    color: #1e293b;
                }

                @media (max-width: 767.98px) {
                    .list-management-voters-table thead th {
                        font-size: .95rem;
                    }

                    .list-management-voters-table tbody td {
                        font-size: .93rem;
                    }

                    #lm-add-voters-table thead th {
                        font-size: .95rem;
                    }

                    #lm-add-voters-table tbody td {
                        font-size: .93rem;
                    }
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
                                    $candidateContractorsCount = (int) ($listManagementContractorCountsByCreator[$candidateUserId] ?? 0);
                                @endphp
                                <label class="list-candidate-pill {{ $isSelected ? 'is-selected' : '' }}" style="cursor:pointer;">
                                    <input
                                        type="checkbox"
                                        name="candidate_users[]"
                                        value="{{ $candidateUserId }}"
                                        class="candidate-filter-checkbox"
                                        data-candidate-name="{{ $candidateUser?->name ?? 'مرشح' }}"
                                        data-contractor-count="{{ $candidateContractorsCount }}"
                                        {{ $isSelected ? 'checked' : '' }}
                                        hidden
                                    >
                                    <div class="list-candidate-pill__avatar" style="background-image:url('{{ $avatar }}');"></div>
                                    <div class="list-candidate-pill__meta">
                                        <div class="name">{{ $candidateUser?->name ?? 'مرشح' }}</div>
                                        <small>
                                            {{ $candidateFilterItem->id == ($currentListLeaderCandidate->id ?? 0) ? 'رئيس القائمة' : 'عضو قائمة' }}
                                            • متعهدون: {{ $candidateContractorsCount }}
                                        </small>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            @endif

            @if(!empty($isListManagementContext))
                <div class="d-flex justify-content-center align-items-center flex-wrap gap-2 mb-3 text-center">
                    <button
                        type="button"
                        class="btn btn-primary"
                        id="list-management-contractors-btn"
                    >
                        إدارة المتعهدين
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        id="list-management-mode-toggle"
                        data-voters-url="{{ route('dashboard.candidates.list-management.voters') }}"
                        data-voter-details-url-template="{{ route('dashboard.candidates.list-management.voters.details', ['voter' => '__VOTER__']) }}"
                        data-voter-delete-url-template="{{ route('dashboard.candidates.list-management.voters.delete-assignment', ['voter' => '__VOTER__']) }}"
                        data-voter-transfer-url-template="{{ route('dashboard.candidates.list-management.voters.transfer-assignment', ['voter' => '__VOTER__']) }}"
                        data-contractors-by-candidate-url="{{ route('dashboard.candidates.list-management.contractors-by-candidate') }}"
                    >
                        إدارة المضامين
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-success"
                        id="list-management-add-voters-btn"
                        data-add-contractors-url="{{ route('dashboard.candidates.list-management.add-voters.contractors') }}"
                        data-add-source-voters-url="{{ route('dashboard.candidates.list-management.add-voters.source-voters') }}"
                        data-add-voter-url-template="{{ route('dashboard.candidates.list-management.add-voters.attach', ['voter' => '__VOTER__']) }}"
                    >
                        إضافة مضامين
                    </button>
                    <span id="list-management-voters-state" class="small text-muted w-100" aria-live="polite"></span>
                </div>

                <div id="list-management-voters-section" class="card border-0 shadow-sm mb-3 d-none" style="border-radius:14px; overflow:hidden;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2 d-none d-md-flex">
                            <h6 class="mb-0" style="font-weight:900;">إدارة المضامين حسب السيليكشن</h6>
                            <div class="mx-3 flex-grow-1" style="max-width: 360px;">
                                <input
                                    type="search"
                                    id="list-management-voters-search-desktop"
                                    class="form-control form-control-sm"
                                    placeholder="بحث داخل المضامين..."
                                    aria-label="بحث داخل المضامين"
                                >
                            </div>
                            <span id="list-management-voters-count" class="badge bg-primary">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 d-md-none">
                            <h6 class="mb-0" style="font-weight:900;">إدارة المضامين حسب السيليكشن</h6>
                            <span id="list-management-voters-count-mobile" class="badge bg-primary">0</span>
                        </div>
                        <div id="list-management-voters-content">
                            <div class="text-center text-muted py-3">اضغط "إدارة المضامين" لعرض البيانات.</div>
                        </div>
                    </div>
                </div>

                <div id="list-management-add-voters-section" class="card border-0 shadow-sm mb-3 d-none" style="border-radius:14px; overflow:hidden;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <h6 class="mb-0" style="font-weight:900;">إضافة مضامين بدون ريلود</h6>
                            <span id="list-management-add-voters-state" class="small text-muted" aria-live="polite"></span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="lm-add-target-contractor" class="form-label mb-1">اختر المتعهد</label>
                                <select id="lm-add-target-contractor" class="form-select form-select-sm">
                                    <option value="">اختر متعهداً</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-2">
                            <input
                                type="search"
                                id="lm-add-voters-search"
                                class="form-control form-control-sm"
                                placeholder="بحث في كل الناخبين (اسم / رقم مدني / هاتف)"
                                aria-label="بحث في كل الناخبين"
                            >
                        </div>

                        <div class="d-md-none mb-2 text-start">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="lm-add-voters-mobile-toggle" data-expanded="0">
                                إظهار تفاصيل أكثر
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle text-center mb-0" id="lm-add-voters-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th class="lm-add-col-extra">الرقم المدني</th>
                                        <th class="lm-add-col-extra">الهاتف</th>
                                        <th>الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody id="lm-add-voters-body">
                                    <tr>
                                        <td colspan="4" class="text-muted py-3">اختر متعهدًا أولاً لعرض مضامينه.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="listManagementVoterModal" tabindex="-1" aria-labelledby="listManagementVoterModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="listManagementVoterModalLabel">تفاصيل المضمون</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="list-management-voter-modal-state" class="small text-muted mb-2" aria-live="polite"></div>
                                <div class="mb-3">
                                    <strong id="list-management-voter-name">—</strong>
                                    <span class="text-muted ms-2" id="list-management-voter-civil">—</span>
                                </div>

                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-striped align-middle text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>المتعهد</th>
                                                <th>المرشح</th>
                                                <th>تاريخ الإضافة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="list-management-voter-assignments-body">
                                            <tr>
                                                <td colspan="4" class="text-muted py-3">اختر مضمونًا لعرض التفاصيل.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="list-management-transfer-panel" class="border rounded p-3 d-none">
                                    <h6 class="mb-3">نقل المضمون</h6>
                                    <input type="hidden" id="list-management-transfer-source-contractor-id" value="">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label">اختر المرشح</label>
                                            <select id="list-management-transfer-candidate" class="form-select">
                                                <option value="">اختر المرشح</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">اختر المتعهد</label>
                                            <select id="list-management-transfer-contractor" class="form-select" disabled>
                                                <option value="">اختر المتعهد</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-outline-secondary" id="list-management-transfer-cancel">إلغاء</button>
                                        <button type="button" class="btn btn-primary" id="list-management-transfer-submit" style="background-color:#2563eb !important;border-color:#2563eb !important;color:#fff !important;">تأكيد النقل</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form id="mota3ahdeenControlForm" class="mota3ahdeenControl cm-anim cm-anim-delay-2 list-management-contractors-only" action="{{ route('dashboard.contractors.store') }}" method="POST">
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


            <input type="search" id="searchBox"  class="form-control py-1 mb-3 list-management-contractors-only"
                placeholder="البحث بجدول المتعهدين الفرعيين">

            <div class="list-management-contractors-only">
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
                                <label class="labelStyle" for="percentageTrusted">نسبة الالتزام</label>
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
            <div id="list-management-contractors-table-wrap" class="table-responsive mt-4 cm-anim cm-anim-delay-3 list-management-contractors-only">
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
                            <th>نسبة الالتزام</th>
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
                                                                <input type="text" inputmode="numeric" dir="ltr" class="form-control" name="to" placeholder="رقم الهاتف لإرسال WhatsApp">
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
            var restoreEnforceFocus = null;

            function relaxParentModalFocusTrap() {
                if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getInstance) {
                    var parentInstance = window.bootstrap.Modal.getInstance(modalEl);
                    if (parentInstance && parentInstance._focustrap && typeof parentInstance._focustrap.deactivate === 'function') {
                        parentInstance._focustrap.deactivate();
                    }
                }

                if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal && window.jQuery.fn.modal.Constructor) {
                    var constructor = window.jQuery.fn.modal.Constructor;
                    if (constructor.prototype && typeof constructor.prototype.enforceFocus === 'function' && !restoreEnforceFocus) {
                        var originalEnforceFocus = constructor.prototype.enforceFocus;
                        constructor.prototype.enforceFocus = function () {};
                        restoreEnforceFocus = function () {
                            constructor.prototype.enforceFocus = originalEnforceFocus;
                            restoreEnforceFocus = null;
                        };
                    }
                }
            }

            function restoreParentModalFocusTrap() {
                if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getInstance) {
                    var parentInstance = window.bootstrap.Modal.getInstance(modalEl);
                    if (parentInstance && parentInstance._focustrap && typeof parentInstance._focustrap.activate === 'function') {
                        parentInstance._focustrap.activate();
                    }
                }

                if (typeof restoreEnforceFocus === 'function') {
                    restoreEnforceFocus();
                }
            }

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

            function cleanupStaleModalUiState() {
                var hasVisibleModal = document.querySelector('.modal.show') !== null;
                if (hasVisibleModal) return;

                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                    backdrop.remove();
                });

                if (document.body) {
                    document.body.classList.remove('modal-open');
                    document.body.classList.remove('contractor-export-modal-layered');
                    document.body.style.removeProperty('padding-right');
                    document.body.style.removeProperty('overflow');
                }

                if (pageRoot) {
                    pageRoot.classList.remove('export-modal-layered');
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

                        setTimeout(cleanupStaleModalUiState, 250);
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
                setTimeout(cleanupStaleModalUiState, 0);
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
                    relaxParentModalFocusTrap();
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
                    restoreParentModalFocusTrap();

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
                var sendPendingTab = null;

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

                if (actionType === 'Send') {
                    var rawPhone = String(queryData.to || '');
                    var normalizedPhone = rawPhone
                        .replace(/[\u0660-\u0669]/g, function (char) { return String(char.charCodeAt(0) - 1632); })
                        .replace(/[\u06F0-\u06F9]/g, function (char) { return String(char.charCodeAt(0) - 1776); })
                        .replace(/\D+/g, '');

                    queryData.to = normalizedPhone;

                    if (!normalizedPhone) {
                        showExportStatus('يرجى إدخال رقم WhatsApp صحيح', 'error');
                        return;
                    }
                }

                submitBtn.disabled = true;
                showExportStatus('جاري تنفيذ العملية...', 'info');

                if (actionType === 'Send') {
                    sendPendingTab = window.open('about:blank', '_blank');
                }

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
                            if (sendPendingTab) {
                                sendPendingTab.location.href = res.data.Redirect_Url;
                            } else {
                                window.location.href = res.data.Redirect_Url;
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
                        if (sendPendingTab && !sendPendingTab.closed) {
                            sendPendingTab.close();
                        }
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

                    if (typeof window.__listManagementOnSelectionUpdated === 'function') {
                        window.__listManagementOnSelectionUpdated();
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

    (function bindListManagementModeSwitch() {
        var votersBtn = document.getElementById('list-management-mode-toggle');
        if (!votersBtn) return;

        var contractorsBtn = document.getElementById('list-management-contractors-btn');
        var addVotersBtn = document.getElementById('list-management-add-voters-btn');

        var votersSection = document.getElementById('list-management-voters-section');
        var addVotersSection = document.getElementById('list-management-add-voters-section');
        var votersContent = document.getElementById('list-management-voters-content');
        var votersCount = document.getElementById('list-management-voters-count');
        var votersCountMobile = document.getElementById('list-management-voters-count-mobile');
        var votersSearchDesktop = document.getElementById('list-management-voters-search-desktop');
        var stateEl = document.getElementById('list-management-voters-state');
        var filterForm = document.getElementById('list-management-candidates-filter');
        var contractorsOnlyBlocks = Array.prototype.slice.call(document.querySelectorAll('.list-management-contractors-only'));
        var activeController = null;
        var mobileVotersExpanded = false;
        var votersSearchKeyword = '';
        var currentMode = 'contractors';

        function setState(message, isError) {
            if (!stateEl) return;
            stateEl.textContent = message || '';
            stateEl.classList.toggle('text-danger', !!isError);
            stateEl.classList.toggle('text-muted', !isError);
        }

        function getSelectedCandidateIds() {
            if (!filterForm) return [];

            return Array.prototype.slice.call(filterForm.querySelectorAll('.candidate-filter-checkbox:checked'))
                .map(function (el) { return String(el.value); });
        }

        function setMode(mode) {
            var nextMode = (mode === 'voters' || mode === 'add-voters') ? mode : 'contractors';
            var isVotersMode = nextMode === 'voters';
            var isAddVotersMode = nextMode === 'add-voters';
            currentMode = nextMode;

            if (votersSection) {
                votersSection.classList.toggle('d-none', !isVotersMode);
            }

            if (addVotersSection) {
                addVotersSection.classList.toggle('d-none', !isAddVotersMode);
            }

            contractorsOnlyBlocks.forEach(function (block) {
                block.classList.toggle('d-none', isVotersMode || isAddVotersMode);
            });

            if (contractorsBtn) {
                contractorsBtn.classList.toggle('btn-primary', currentMode === 'contractors');
                contractorsBtn.classList.toggle('btn-outline-primary', currentMode !== 'contractors');
            }

            if (votersBtn) {
                votersBtn.classList.toggle('btn-primary', isVotersMode);
                votersBtn.classList.toggle('btn-outline-primary', !isVotersMode);
            }

            if (addVotersBtn) {
                addVotersBtn.classList.toggle('btn-success', isAddVotersMode);
                addVotersBtn.classList.toggle('btn-outline-success', !isAddVotersMode);
            }

            if (typeof window.__listManagementModeChanged === 'function') {
                window.__listManagementModeChanged(currentMode);
            }
        }

        function applyMobileVotersLayout() {
            if (!votersSection) return;

            var compact = !mobileVotersExpanded;
            votersSection.classList.toggle('is-mobile-compact', compact);

            var toggleMobileBtn = votersSection.querySelector('.js-lm-voters-mobile-toggle');
            if (toggleMobileBtn) {
                toggleMobileBtn.setAttribute('data-expanded', mobileVotersExpanded ? '1' : '0');
                toggleMobileBtn.textContent = mobileVotersExpanded ? 'إخفاء التفاصيل' : 'إظهار تفاصيل أكثر';
            }
        }

        function syncVotersSearchInputs() {
            var mobileSearchInput = votersContent ? votersContent.querySelector('.js-lm-voters-search-mobile') : null;

            if (votersSearchDesktop && votersSearchDesktop.value !== votersSearchKeyword) {
                votersSearchDesktop.value = votersSearchKeyword;
            }

            if (mobileSearchInput && mobileSearchInput.value !== votersSearchKeyword) {
                mobileSearchInput.value = votersSearchKeyword;
            }
        }

        function applyVotersSearchFilter() {
            if (!votersContent) return;

            var rows = Array.prototype.slice.call(votersContent.querySelectorAll('table.list-management-voters-table tbody tr'));
            if (!rows.length) {
                if (votersCount) votersCount.textContent = '0';
                if (votersCountMobile) votersCountMobile.textContent = '0';
                return;
            }

            var keyword = String(votersSearchKeyword || '').toLowerCase().trim();
            var visibleRows = 0;

            rows.forEach(function (row) {
                var noDataCell = row.querySelector('td[colspan]');
                if (noDataCell) {
                    row.style.display = '';
                    return;
                }

                var rowText = String(row.textContent || '').toLowerCase();
                var isMatch = !keyword || rowText.indexOf(keyword) !== -1;
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) visibleRows += 1;
            });

            if (votersCount) votersCount.textContent = String(visibleRows);
            if (votersCountMobile) votersCountMobile.textContent = String(visibleRows);
        }

        function updateVotersSearch(value) {
            votersSearchKeyword = String(value || '');
            syncVotersSearchInputs();
            applyVotersSearchFilter();
        }

        function renderVoters() {
            var endpoint = String(votersBtn.dataset.votersUrl || '');
            if (!endpoint) return;

            if (activeController) {
                activeController.abort();
            }

            activeController = new AbortController();
            votersBtn.disabled = true;
            setState('جاري تحميل المضامين...');

            if (votersContent) {
                votersContent.innerHTML = '<div class="text-center text-muted py-3">جاري التحميل...</div>';
            }

            var url = new URL(endpoint, window.location.origin);
            getSelectedCandidateIds().forEach(function (id) {
                url.searchParams.append('candidate_users[]', id);
            });

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: activeController.signal
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function (payload) {
                    if (votersContent) {
                        votersContent.innerHTML = String(payload?.html || '<div class="text-center text-muted py-3">لا توجد بيانات.</div>');
                    }

                    mobileVotersExpanded = false;
                    applyMobileVotersLayout();
                    syncVotersSearchInputs();

                    if (votersCount) {
                        votersCount.textContent = String(payload?.total ?? 0);
                    }
                    if (votersCountMobile) {
                        votersCountMobile.textContent = String(payload?.total ?? 0);
                    }

                    applyVotersSearchFilter();

                    setState('تم تحديث المضامين');
                    setTimeout(function () { setState(''); }, 1000);
                })
                .catch(function (error) {
                    if (error && error.name === 'AbortError') return;
                    console.error(error);
                    if (votersContent) {
                        votersContent.innerHTML = '<div class="text-center text-danger py-3">تعذر تحميل المضامين.</div>';
                    }
                    if (votersCount) votersCount.textContent = '0';
                    if (votersCountMobile) votersCountMobile.textContent = '0';
                    setState('تعذر تحميل المضامين', true);
                })
                .finally(function () {
                    votersBtn.disabled = false;
                });
        }

        votersBtn.addEventListener('click', function () {
            if (currentMode !== 'voters') {
                setMode('voters');
                renderVoters();
            }
        });

        if (contractorsBtn) {
            contractorsBtn.addEventListener('click', function () {
                setMode('contractors');
                setState('');
            });
        }

        if (addVotersBtn) {
            addVotersBtn.addEventListener('click', function () {
                setMode('add-voters');
                setState('');
            });
        }

        window.__listManagementOnSelectionUpdated = function () {
            if (currentMode === 'voters') {
                renderVoters();
            }

            if (currentMode === 'add-voters' && typeof window.__listManagementReloadAddVoters === 'function') {
                window.__listManagementReloadAddVoters();
            }
        };

        if (votersContent) {
            votersContent.addEventListener('click', function (event) {
                var mobileToggleBtn = event.target.closest('.js-lm-voters-mobile-toggle');
                if (!mobileToggleBtn) return;

                event.preventDefault();
                mobileVotersExpanded = !mobileVotersExpanded;
                applyMobileVotersLayout();
            });

            votersContent.addEventListener('input', function (event) {
                var mobileSearchInput = event.target.closest('.js-lm-voters-search-mobile');
                if (!mobileSearchInput) return;
                updateVotersSearch(mobileSearchInput.value);
            });
        }

        if (votersSearchDesktop) {
            votersSearchDesktop.addEventListener('input', function () {
                updateVotersSearch(votersSearchDesktop.value);
            });
        }

        setMode('contractors');

        window.__listManagementRefreshVoters = renderVoters;
        window.__listManagementGetSelectedCandidateIds = getSelectedCandidateIds;
        window.__listManagementSetMode = setMode;
    })();

    (function bindListManagementAddVotersMode() {
        var addBtn = document.getElementById('list-management-add-voters-btn');
        var section = document.getElementById('list-management-add-voters-section');
        if (!addBtn || !section) return;

        var targetSelect = document.getElementById('lm-add-target-contractor');
        var searchInput = document.getElementById('lm-add-voters-search');
        var tableBody = document.getElementById('lm-add-voters-body');
        var mobileToggleBtn = document.getElementById('lm-add-voters-mobile-toggle');
        var stateEl = document.getElementById('list-management-add-voters-state');
        var activeRowsAbort = null;
        var searchDebounceTimer = null;
        var contractorsLoaded = false;
        var mobileExpanded = false;

        function setState(message, isError) {
            if (!stateEl) return;
            stateEl.textContent = message || '';
            stateEl.classList.toggle('text-danger', !!isError);
            stateEl.classList.toggle('text-muted', !isError);
        }

        function csrfToken() {
            var tokenEl = document.querySelector('meta[name="csrf-token"]');
            return tokenEl ? tokenEl.getAttribute('content') : '';
        }

        function selectedCandidateIds() {
            if (typeof window.__listManagementGetSelectedCandidateIds === 'function') {
                return window.__listManagementGetSelectedCandidateIds();
            }
            return [];
        }

        function buildUrl(baseUrl, paramsBuilder) {
            var url = new URL(String(baseUrl || ''), window.location.origin);
            selectedCandidateIds().forEach(function (id) {
                url.searchParams.append('candidate_users[]', id);
            });

            if (typeof paramsBuilder === 'function') {
                paramsBuilder(url.searchParams);
            }

            return url.toString();
        }

        function renderRows(rows) {
            if (!tableBody) return;

            if (!Array.isArray(rows) || rows.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-muted py-3">لا توجد نتائج.</td></tr>';
                return;
            }

            var html = rows.map(function (row) {
                var voterId = Number(row?.voter_id || 0);
                var voterName = String(row?.voter_name || '—');
                var civilId = String(row?.civil_id || '—');
                var phone = String(row?.phone || '—');
                var attached = !!row?.is_attached;

                return '<tr>' +
                    '<td>' + voterName + '</td>' +
                    '<td class="lm-add-col-extra">' + civilId + '</td>' +
                    '<td class="lm-add-col-extra">' + phone + '</td>' +
                    '<td>' +
                        (attached
                            ? '<span class="badge bg-success">مضاف</span>'
                            : '<button type="button" class="btn btn-sm btn-primary js-lm-add-voter" data-voter-id="' + voterId + '">إضافة</button>') +
                    '</td>' +
                '</tr>';
            }).join('');

            tableBody.innerHTML = html;
            applyMobileAddVotersLayout();
        }

        function applyMobileAddVotersLayout() {
            if (!section) return;
            var compact = !mobileExpanded;
            section.classList.toggle('is-mobile-compact', compact);

            if (mobileToggleBtn) {
                mobileToggleBtn.setAttribute('data-expanded', mobileExpanded ? '1' : '0');
                mobileToggleBtn.textContent = mobileExpanded ? 'إخفاء التفاصيل' : 'إظهار تفاصيل أكثر';
            }
        }

        function loadVoters() {
            var endpoint = String(addBtn.dataset.addSourceVotersUrl || '');
            if (!endpoint || !targetSelect || !tableBody) return;

            var contractorId = Number(targetSelect.value || 0);
            if (!contractorId) {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-muted py-3">اختر متعهدًا أولاً لعرض مضامينه.</td></tr>';
                applyMobileAddVotersLayout();
                return;
            }

            if (activeRowsAbort) {
                activeRowsAbort.abort();
            }

            activeRowsAbort = new AbortController();
            setState('جاري تحميل المضامين...');
            tableBody.innerHTML = '<tr><td colspan="4" class="text-muted py-3">جاري التحميل...</td></tr>';

            var url = buildUrl(endpoint, function (params) {
                params.set('contractor_id', String(contractorId));

                var search = searchInput ? String(searchInput.value || '').trim() : '';
                if (search) {
                    params.set('search', search);
                }
            });

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: activeRowsAbort.signal
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok) {
                            throw new Error(String(payload?.message || ('HTTP ' + response.status)));
                        }
                        return payload;
                    });
                })
                .then(function (payload) {
                    renderRows(Array.isArray(payload?.rows) ? payload.rows : []);
                    setState('تم تحديث النتائج');
                    setTimeout(function () { setState(''); }, 800);
                })
                .catch(function (error) {
                    if (error && error.name === 'AbortError') return;
                    console.error(error);
                    tableBody.innerHTML = '<tr><td colspan="4" class="text-danger py-3">' + String(error?.message || 'تعذر تحميل المضامين.') + '</td></tr>';
                    setState(String(error?.message || 'تعذر تحميل المضامين.'), true);
                });
        }

        function fillContractorsSelects(contractors) {
            if (!targetSelect) return;

            var targetValue = String(targetSelect.value || '');
            var options = ['<option value="">اختر المتعهد</option>'];

            (Array.isArray(contractors) ? contractors : []).forEach(function (item) {
                var contractorId = Number(item?.id || 0);
                if (!contractorId) return;
                var contractorName = String(item?.name || 'متعهد');
                var candidateName = String(item?.candidate_name || 'مرشح');
                options.push('<option value="' + contractorId + '">' + contractorName + ' - ' + candidateName + '</option>');
            });

            var html = options.join('');
            targetSelect.innerHTML = html;

            if (targetValue && targetSelect.querySelector('option[value="' + targetValue + '"]')) {
                targetSelect.value = targetValue;
            }
        }

        function loadContractors() {
            var endpoint = String(addBtn.dataset.addContractorsUrl || '');
            if (!endpoint) return Promise.resolve();

            setState('جاري تحميل المتعهدين...');

            var url = buildUrl(endpoint);
            return fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok) {
                            throw new Error(String(payload?.message || ('HTTP ' + response.status)));
                        }
                        return payload;
                    });
                })
                .then(function (payload) {
                    fillContractorsSelects(payload?.contractors || []);
                    contractorsLoaded = true;
                    setState('');
                })
                .catch(function (error) {
                    console.error(error);
                    setState(String(error?.message || 'تعذر تحميل المتعهدين.'), true);
                });
        }

        function addVoter(voterId) {
            var endpointTemplate = String(addBtn.dataset.addVoterUrlTemplate || '');
            var contractorId = Number(targetSelect ? targetSelect.value : 0);

            if (!contractorId) {
                setState('يجب تحديد متعهد واحد فقط قبل الإضافة.', true);
                if (window.toastr) toastr.error('يجب تحديد متعهد واحد فقط قبل الإضافة.');
                return;
            }

            var endpoint = endpointTemplate.replace('__VOTER__', String(voterId || 0));
            if (!endpoint) return;

            setState('جاري الإضافة...');

            var body = new URLSearchParams();
            body.append('_token', csrfToken());
            body.append('contractor_id', String(contractorId));
            selectedCandidateIds().forEach(function (id) {
                body.append('candidate_users[]', id);
            });

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: body.toString()
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok) {
                            throw new Error(String(payload?.message || ('HTTP ' + response.status)));
                        }
                        return payload;
                    });
                })
                .then(function (payload) {
                    var message = String(payload?.message || 'تمت الإضافة بنجاح.');
                    setState(message, false);
                    if (window.toastr) toastr.success(message);
                    loadVoters();
                })
                .catch(function (error) {
                    console.error(error);
                    setState(String(error?.message || 'تعذر الإضافة.'), true);
                    if (window.toastr) toastr.error(String(error?.message || 'تعذر الإضافة.'));
                });
        }

        if (targetSelect) {
            targetSelect.addEventListener('change', function () {
                loadVoters();
            });
        }

        if (mobileToggleBtn) {
            mobileToggleBtn.addEventListener('click', function (event) {
                event.preventDefault();
                mobileExpanded = !mobileExpanded;
                applyMobileAddVotersLayout();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                if (searchDebounceTimer) {
                    clearTimeout(searchDebounceTimer);
                }

                searchDebounceTimer = setTimeout(function () {
                    loadVoters();
                }, 280);
            });
        }

        if (tableBody) {
            tableBody.addEventListener('click', function (event) {
                var addRowBtn = event.target.closest('.js-lm-add-voter');
                if (!addRowBtn) return;
                event.preventDefault();
                addVoter(Number(addRowBtn.getAttribute('data-voter-id') || 0));
            });
        }

        window.__listManagementModeChanged = function (mode) {
            if (mode !== 'add-voters') return;

            mobileExpanded = false;
            applyMobileAddVotersLayout();

            loadContractors().then(function () {
                if (contractorsLoaded) {
                    loadVoters();
                }
            });
        };

        window.__listManagementReloadAddVoters = function () {
            contractorsLoaded = false;
            mobileExpanded = false;
            applyMobileAddVotersLayout();
            loadContractors().then(function () {
                loadVoters();
            });
        };

        applyMobileAddVotersLayout();
    })();

    (function bindListManagementVoterModal() {
        var toggleBtn = document.getElementById('list-management-mode-toggle');
        var modalEl = document.getElementById('listManagementVoterModal');
        var filterForm = document.getElementById('list-management-candidates-filter');
        if (!toggleBtn || !modalEl) return;

        var modal = null;
        if (window.bootstrap && window.bootstrap.Modal) {
            if (typeof window.bootstrap.Modal.getOrCreateInstance === 'function') {
                modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            } else {
                modal = new window.bootstrap.Modal(modalEl);
            }
        }

        var stateEl = document.getElementById('list-management-voter-modal-state');
        var voterNameEl = document.getElementById('list-management-voter-name');
        var voterCivilEl = document.getElementById('list-management-voter-civil');
        var assignmentsBody = document.getElementById('list-management-voter-assignments-body');
        var transferPanel = document.getElementById('list-management-transfer-panel');
        var transferSourceInput = document.getElementById('list-management-transfer-source-contractor-id');
        var transferCandidateSelect = document.getElementById('list-management-transfer-candidate');
        var transferContractorSelect = document.getElementById('list-management-transfer-contractor');
        var transferSubmitBtn = document.getElementById('list-management-transfer-submit');
        var transferCancelBtn = document.getElementById('list-management-transfer-cancel');

        var currentVoterId = null;

        function csrfToken() {
            var tokenEl = document.querySelector('meta[name="csrf-token"]');
            return tokenEl ? tokenEl.getAttribute('content') : '';
        }

        function selectedCandidateIds() {
            if (typeof window.__listManagementGetSelectedCandidateIds === 'function') {
                return window.__listManagementGetSelectedCandidateIds();
            }
            return [];
        }

        function buildUrl(template, voterId) {
            return String(template || '').replace('__VOTER__', String(voterId || '0'));
        }

        function setModalState(message, isError) {
            if (!stateEl) return;
            stateEl.textContent = message || '';
            stateEl.classList.toggle('text-danger', !!isError);
            stateEl.classList.toggle('text-muted', !isError);
        }

        function getAllListManagementCandidates() {
            if (!filterForm) return [];

            return Array.prototype.slice.call(filterForm.querySelectorAll('.candidate-filter-checkbox'))
                .map(function (input) {
                    var candidateId = String(input.value || '').trim();
                    if (!candidateId) return null;

                    var candidateName = String(input.getAttribute('data-candidate-name') || '').trim();
                    var contractorCount = Number(input.getAttribute('data-contractor-count') || 0);

                    if (!candidateName) {
                        var label = input.closest('label.list-candidate-pill');
                        var nameEl = label ? label.querySelector('.list-candidate-pill__meta .name') : null;
                        candidateName = nameEl ? String(nameEl.textContent || '').trim() : '';
                    }

                    return {
                        id: candidateId,
                        name: candidateName || ('مرشح #' + candidateId),
                        contractorsCount: Number.isFinite(contractorCount) ? contractorCount : 0
                    };
                })
                .filter(function (item) { return !!item; });
        }

        function populateCandidateOptions(assignments) {
            if (!transferCandidateSelect) return;

            var seen = {};
            var options = '<option value="">اختر المرشح</option>';

            getAllListManagementCandidates().forEach(function (candidate) {
                if (!candidate?.id || seen[candidate.id]) return;
                seen[candidate.id] = true;
                options += '<option value="' + candidate.id + '">' + candidate.name + ' (' + (candidate.contractorsCount || 0) + ')</option>';
            });

            (assignments || []).forEach(function (item) {
                var candidateId = String(item?.candidate_user_id || '').trim();
                var candidateName = String(item?.candidate_name || '').trim();
                if (!candidateId || seen[candidateId]) return;
                seen[candidateId] = true;
                options += '<option value="' + candidateId + '">' + candidateName + '</option>';
            });

            transferCandidateSelect.innerHTML = options;
            transferCandidateSelect.value = '';
        }

        function setAssignmentsRows(assignments) {
            if (!assignmentsBody) return;

            if (!Array.isArray(assignments) || assignments.length === 0) {
                assignmentsBody.innerHTML = '<tr><td colspan="4" class="text-muted py-3">لا توجد بيانات ربط لهذا المضمون ضمن السيليكشن الحالي.</td></tr>';
                return;
            }

            var rowsHtml = assignments.map(function (row) {
                var contractorId = Number(row?.contractor_id || 0);
                var contractorName = String(row?.contractor_name || '—');
                var candidateName = String(row?.candidate_name || '—');
                var attachedAt = String(row?.attached_at || '—');

                return '<tr>' +
                    '<td>' + contractorName + '</td>' +
                    '<td>' + candidateName + '</td>' +
                    '<td>' + attachedAt + '</td>' +
                    '<td>' +
                        '<div class="d-flex justify-content-center gap-2 flex-wrap">' +
                            '<button type="button" class="btn btn-sm btn-danger js-list-management-delete-assignment" style="background-color:#dc3545 !important;border-color:#dc3545 !important;color:#fff !important;" data-source-contractor-id="' + contractorId + '">حذف</button>' +
                            '<button type="button" class="btn btn-sm btn-primary js-list-management-open-transfer" style="background-color:#2563eb !important;border-color:#2563eb !important;color:#fff !important;" data-source-contractor-id="' + contractorId + '">نقل</button>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
            }).join('');

            assignmentsBody.innerHTML = rowsHtml;
        }

        function hideTransferPanel() {
            if (transferPanel) {
                transferPanel.classList.add('d-none');
            }
            if (transferSourceInput) {
                transferSourceInput.value = '';
            }
            if (transferContractorSelect) {
                transferContractorSelect.innerHTML = '<option value="">اختر المتعهد</option>';
                transferContractorSelect.value = '';
                transferContractorSelect.disabled = true;
            }
            if (transferCandidateSelect) {
                transferCandidateSelect.value = '';
            }
        }

        function refreshVotersTableIfNeeded() {
            if (typeof window.__listManagementRefreshVoters === 'function') {
                window.__listManagementRefreshVoters();
            }
        }

        function fetchDetails(voterId) {
            var detailsTemplate = toggleBtn.dataset.voterDetailsUrlTemplate;
            var endpoint = buildUrl(detailsTemplate, voterId);
            if (!endpoint) return;

            var url = new URL(endpoint, window.location.origin);
            selectedCandidateIds().forEach(function (id) {
                url.searchParams.append('candidate_users[]', id);
            });

            setModalState('جاري تحميل البيانات...');
            hideTransferPanel();

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok) {
                            throw new Error(String(payload?.message || ('HTTP ' + response.status)));
                        }

                        return payload;
                    });
                })
                .then(function (payload) {
                    var voter = payload?.voter || {};
                    var assignments = Array.isArray(payload?.assignments) ? payload.assignments : [];

                    currentVoterId = Number(voter?.id || voterId || 0);
                    if (voterNameEl) voterNameEl.textContent = String(voter?.name || '—');
                    if (voterCivilEl) voterCivilEl.textContent = String(voter?.civil_id || '—');

                    setAssignmentsRows(assignments);
                    populateCandidateOptions(assignments);
                    setModalState('');
                })
                .catch(function (error) {
                    console.error(error);
                    setModalState(String(error?.message || 'تعذر تحميل تفاصيل المضمون'), true);
                    if (assignmentsBody) {
                        assignmentsBody.innerHTML = '<tr><td colspan="4" class="text-danger py-3">' + String(error?.message || 'تعذر تحميل البيانات.') + '</td></tr>';
                    }
                });
        }

        function openModal(voterId) {
            currentVoterId = Number(voterId || 0);
            if (!currentVoterId) return;

            if (modal && typeof modal.show === 'function') {
                modal.show();
            } else if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                window.jQuery(modalEl).modal('show');
            }

            fetchDetails(currentVoterId);
        }

        function loadContractorsForCandidate(candidateUserId) {
            var endpoint = String(toggleBtn.dataset.contractorsByCandidateUrl || '');
            if (!endpoint || !candidateUserId) return;

            var url = new URL(endpoint, window.location.origin);
            url.searchParams.append('candidate_user_id', String(candidateUserId));
            selectedCandidateIds().forEach(function (id) {
                url.searchParams.append('candidate_users[]', id);
            });

            if (transferContractorSelect) {
                transferContractorSelect.disabled = true;
                transferContractorSelect.innerHTML = '<option value="">جاري تحميل المتعهدين...</option>';
            }

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(function (payload) {
                    var contractors = Array.isArray(payload?.contractors) ? payload.contractors : [];
                    var options = '<option value="">اختر المتعهد</option>';
                    contractors.forEach(function (item) {
                        options += '<option value="' + Number(item.id) + '">' + String(item.name || 'متعهد') + '</option>';
                    });

                    if (transferContractorSelect) {
                        transferContractorSelect.innerHTML = options;
                        transferContractorSelect.disabled = false;
                    }
                })
                .catch(function (error) {
                    console.error(error);
                    if (transferContractorSelect) {
                        transferContractorSelect.innerHTML = '<option value="">تعذر تحميل المتعهدين</option>';
                        transferContractorSelect.disabled = true;
                    }
                });
        }

        function performDelete(sourceContractorId) {
            if (!currentVoterId || !sourceContractorId) return;

            var endpoint = buildUrl(toggleBtn.dataset.voterDeleteUrlTemplate, currentVoterId);
            if (!endpoint) return;

            setModalState('جاري حذف المضمون...');

            var formData = new FormData();
            formData.append('source_contractor_id', String(sourceContractorId));
            selectedCandidateIds().forEach(function (id) {
                formData.append('candidate_users[]', id);
            });

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: formData
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok) {
                            throw new Error(String(payload?.message || 'تعذر حذف المضمون'));
                        }
                        return payload;
                    });
                })
                .then(function (payload) {
                    setModalState(String(payload?.message || 'تم حذف المضمون بنجاح'));
                    fetchDetails(currentVoterId);
                    refreshVotersTableIfNeeded();
                })
                .catch(function (error) {
                    setModalState(String(error?.message || 'تعذر حذف المضمون'), true);
                });
        }

        function performTransfer() {
            if (!currentVoterId || !transferSourceInput || !transferCandidateSelect || !transferContractorSelect) return;

            var sourceContractorId = Number(transferSourceInput.value || 0);
            var targetCandidateUserId = Number(transferCandidateSelect.value || 0);
            var targetContractorId = Number(transferContractorSelect.value || 0);

            if (!sourceContractorId || !targetCandidateUserId || !targetContractorId) {
                setModalState('اختر المرشح والمتعهد قبل التأكيد.', true);
                return;
            }

            var endpoint = buildUrl(toggleBtn.dataset.voterTransferUrlTemplate, currentVoterId);
            if (!endpoint) return;

            if (transferSubmitBtn) transferSubmitBtn.disabled = true;
            setModalState('جاري نقل المضمون...');

            var formData = new FormData();
            formData.append('source_contractor_id', String(sourceContractorId));
            formData.append('target_candidate_user_id', String(targetCandidateUserId));
            formData.append('target_contractor_id', String(targetContractorId));
            selectedCandidateIds().forEach(function (id) {
                formData.append('candidate_users[]', id);
            });

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: formData
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok) {
                            throw new Error(String(payload?.message || 'تعذر نقل المضمون'));
                        }
                        return payload;
                    });
                })
                .then(function (payload) {
                    setModalState(String(payload?.message || 'تم النقل بنجاح'));
                    hideTransferPanel();
                    fetchDetails(currentVoterId);
                    refreshVotersTableIfNeeded();
                })
                .catch(function (error) {
                    setModalState(String(error?.message || 'تعذر نقل المضمون'), true);
                })
                .finally(function () {
                    if (transferSubmitBtn) transferSubmitBtn.disabled = false;
                });
        }

        document.addEventListener('click', function (event) {
            var openBtn = event.target.closest('.js-list-management-voter-open');
            if (openBtn) {
                event.preventDefault();
                openModal(Number(openBtn.getAttribute('data-voter-id') || 0));
                return;
            }

            var deleteBtn = event.target.closest('.js-list-management-delete-assignment');
            if (deleteBtn) {
                event.preventDefault();
                var sourceId = Number(deleteBtn.getAttribute('data-source-contractor-id') || 0);
                if (!sourceId) return;
                performDelete(sourceId);
                return;
            }

            var openTransferBtn = event.target.closest('.js-list-management-open-transfer');
            if (openTransferBtn) {
                event.preventDefault();
                var transferSourceId = Number(openTransferBtn.getAttribute('data-source-contractor-id') || 0);
                if (!transferSourceId) return;

                if (transferSourceInput) transferSourceInput.value = String(transferSourceId);
                if (transferPanel) transferPanel.classList.remove('d-none');
                if (transferCandidateSelect) transferCandidateSelect.value = '';
                if (transferContractorSelect) {
                    transferContractorSelect.innerHTML = '<option value="">اختر المتعهد</option>';
                    transferContractorSelect.disabled = true;
                }

                setModalState('');
            }
        });

        if (transferCandidateSelect) {
            transferCandidateSelect.addEventListener('change', function () {
                var candidateUserId = Number(transferCandidateSelect.value || 0);
                if (!candidateUserId) {
                    if (transferContractorSelect) {
                        transferContractorSelect.innerHTML = '<option value="">اختر المتعهد</option>';
                        transferContractorSelect.disabled = true;
                    }
                    return;
                }

                loadContractorsForCandidate(candidateUserId);
            });
        }

        if (transferCancelBtn) {
            transferCancelBtn.addEventListener('click', function () {
                hideTransferPanel();
                setModalState('');
            });
        }

        if (transferSubmitBtn) {
            transferSubmitBtn.addEventListener('click', function () {
                performTransfer();
            });
        }
    })();

	 
	
	 
    </script>



@endpush
