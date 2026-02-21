@extends('layouts.dashboard.app')

@section('content')
<link rel="stylesheet" href="/assets/css/export-modal.css?v=20260221a">
<style>
    .stmt-modern {
        direction: rtl;
        max-width: 1400px;
        margin: 0 auto;
        padding: 12px;
        position: relative;
        font-weight: 600;
        --sm-accent-1: #7f1d1d;
        --sm-accent-2: #0f766e;
        --sm-accent-3: #b45309;
        background:
            radial-gradient(circle at 6% 2%, rgba(127,29,29,.26), transparent 36%),
            radial-gradient(circle at 95% 8%, rgba(15,118,110,.22), transparent 40%),
            radial-gradient(circle at 52% 98%, rgba(180,83,9,.20), transparent 40%),
            linear-gradient(212deg, #ffe7dc, #ffe2d5 34%, #dbf4ee 100%);
        border-radius: 20px;
    }

    .stmt-modern .sm-card {
        background: linear-gradient(336deg, rgba(255,236,229,.92), rgba(221,245,239,.90));
        border: 1px solid rgba(127,29,29,.20);
        border-radius: 18px;
        box-shadow: 0 16px 34px rgba(127,29,29,.10), 0 8px 20px rgba(15,118,110,.08);
    }

    .sm-hero {
        padding: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
        background: linear-gradient(24deg, rgba(127,29,29,.26), rgba(15,118,110,.18) 52%, rgba(180,83,9,.22));
        border: 1px solid rgba(127,29,29,.18);
        border-radius: 16px;
    }

    .sm-hero-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 950;
        color: var(--ui-ink, #0f172a);
    }

    .sm-hero-sub {
        margin: 4px 0 0;
        color: #475569;
        font-size: .92rem;
        font-weight: 700;
    }

    .sm-hero-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .sm-filter-wrap {
        padding: 16px;
        margin-bottom: 12px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(127,29,29,.24);
        background:
            radial-gradient(circle at 8% 12%, rgba(127,29,29,.24), transparent 40%),
            radial-gradient(circle at 88% 18%, rgba(15,118,110,.22), transparent 42%),
            radial-gradient(circle at 44% 100%, rgba(180,83,9,.20), transparent 42%),
            linear-gradient(298deg, #ffe5d9, #ffe9d9 40%, #d8f3ec 100%);
        box-shadow: 0 14px 28px rgba(127,29,29,.10);
    }

    .sm-filter-wrap::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(70deg, rgba(255,247,240,.34), transparent 30%, transparent 68%, rgba(221,245,239,.24));
        opacity: .78;
    }

    .sm-filter-wrap::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(244deg, rgba(127,29,29,.16), transparent 38%);
    }

    .sm-filter-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 10px;
    }

    .sm-col-12 { grid-column: span 12; }
    .sm-col-6 { grid-column: span 6; }
    .sm-col-4 { grid-column: span 4; }
    .sm-col-3 { grid-column: span 3; }

    .sm-field label {
        display: block;
        margin-bottom: 5px;
        color: #334155;
        font-size: .82rem;
        font-weight: 900;
        letter-spacing: .1px;
    }

    .sm-field {
        background: linear-gradient(22deg, rgba(255,228,218,.82), rgba(216,243,235,.78));
        border: 1px solid rgba(148,163,184,.35);
        border-radius: 14px;
        padding: 8px 9px;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        backdrop-filter: saturate(120%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.85), 0 3px 10px rgba(15,23,42,.05);
    }

    .sm-field:hover {
        border-color: rgba(127,29,29,.44);
        box-shadow: 0 10px 20px rgba(127,29,29,.13), 0 0 0 1px rgba(15,118,110,.12);
        transform: translateY(-1px);
    }

    .sm-field .form-control,
    .sm-field .form-select {
        border-radius: 12px;
        min-height: 42px;
        border-color: rgba(148,163,184,.36);
        background: rgba(255,247,241,.92);
        color: var(--ui-ink, #0f172a);
        font-weight: 700;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .sm-field .form-control:focus,
    .sm-field .form-select:focus {
        border-color: rgba(127,29,29,.72);
        box-shadow: 0 0 0 .22rem rgba(127,29,29,.20), 0 10px 18px rgba(15,118,110,.14);
        background: #fff3ec;
    }

    .sm-field .form-select {
        direction: rtl;
        background-position: left .75rem center;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .sm-field .form-select::-ms-expand {
        display: none;
    }

    .sm-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-top: 12px;
        border-top: 1px solid rgba(127,29,29,.24);
        padding-top: 12px;
    }

    .sm-actions .btn {
        border-radius: 12px;
        font-weight: 900;
    }

    .sm-advanced-fields {
        grid-column: span 12;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height .26s ease, opacity .2s ease;
        border-radius: 14px;
    }

    .sm-advanced-fields.is-open {
        max-height: 1200px;
        opacity: 1;
        margin-top: 2px;
    }

    .sm-advanced-grid {
        padding-top: 8px;
        background: linear-gradient(210deg, rgba(254,226,226,.86), rgba(204,251,241,.82));
        border: 1px dashed rgba(127,29,29,.34);
        border-radius: 14px;
        padding: 10px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
    }

    .sm-result-head {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(127,29,29,.20);
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        background: linear-gradient(320deg, rgba(254,226,226,.92), rgba(204,251,241,.84));
    }

    .sm-result-meta {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .sm-pill {
        background: linear-gradient(58deg, rgba(254,226,226,.92), rgba(209,250,229,.86));
        color: #7f1d1d;
        border: 1px solid rgba(127,29,29,.30);
        border-radius: 999px;
        padding: 4px 10px;
        font-size: .78rem;
        font-weight: 900;
        box-shadow: 0 4px 10px rgba(127,29,29,.10);
    }

    .sm-result-body {
        padding: 10px;
    }

    .sm-table {
        margin: 0;
    }

    .sm-table thead th {
        font-size: .82rem;
        color: #7f1d1d;
        font-weight: 900;
        border-bottom-width: 1px;
        white-space: nowrap;
        background: linear-gradient(36deg, rgba(254,202,202,.70), rgba(153,246,228,.52));
    }

    .sm-table td {
        vertical-align: middle;
        font-size: .9rem;
        font-weight: 700;
        border-color: rgba(148,163,184,.18);
    }

    .sm-table tbody tr:nth-child(odd) {
        background: rgba(254,226,226,.52);
    }

    .sm-table tbody tr:nth-child(even) {
        background: rgba(204,251,241,.48);
    }

    .sm-table tbody tr:hover {
        background: linear-gradient(246deg, rgba(127,29,29,.26), rgba(15,118,110,.20));
    }

    .sm-empty {
        text-align: center;
        color: var(--ui-muted, #64748b);
        font-weight: 800;
        padding: 20px;
    }

    .sm-pagination {
        padding: 10px 14px 14px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .sm-export-bar {
        margin-bottom: 12px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .22s ease, transform .22s ease;
        background: linear-gradient(300deg, rgba(254,226,226,.90), rgba(254,215,170,.78));
        border: 1px solid rgba(180,83,9,.24);
    }

    .sm-export-bar.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .sm-loading {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .28);
        z-index: 1100;
        align-items: center;
        justify-content: center;
    }

    .sm-loading.show { display: flex; }

    .sm-loader {
        width: 42px;
        height: 42px;
        border: 3px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: smSpin .8s linear infinite;
    }

    @keyframes smSpin { to { transform: rotate(360deg); } }

    @media (max-width: 992px) {
        .sm-col-6,
        .sm-col-4,
        .sm-col-3 { grid-column: span 6; }
    }

    @media (max-width: 640px) {
        .stmt-modern {
            padding: 8px;
        }

        .sm-filter-wrap {
            padding: 12px;
        }

        .sm-filter-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 8px;
        }

        .sm-col-6,
        .sm-col-4,
        .sm-col-3,
        .sm-col-12 { grid-column: span 2; }

        .sm-mobile-full { grid-column: 1 / -1; }
        .sm-mobile-two { grid-column: span 3; }

        .sm-mobile-emphasis {
            background: linear-gradient(286deg, rgba(254,226,226,.86), rgba(204,251,241,.78));
            border: 1px solid rgba(127,29,29,.24);
            border-radius: 12px;
            padding: 8px;
        }

        .sm-advanced-fields {
            grid-column: 1 / -1;
        }

        .sm-order-name { order: 1; }
        .sm-order-first { order: 2; }
        .sm-order-civil { order: 3; }
        .sm-order-box { order: 4; }
        .sm-order-family { order: 5; }
        .sm-order-btn { order: 6; }
        .sm-order-fakhd { order: 7; }
        .sm-order-faraa { order: 8; }
        .sm-order-code1 { order: 9; }
        .sm-order-code2 { order: 10; }
        .sm-order-code3 { order: 11; }
        .sm-order-committee { order: 12; }
        .sm-order-restricted { order: 13; }
        .sm-order-type { order: 14; }
        .sm-order-status { order: 15; }
        .sm-order-agefrom { order: 16; }
        .sm-order-ageto { order: 17; }
        .sm-order-more { order: 18; }
        .sm-order-advanced { order: 19; }
        .sm-order-perpage { order: 20; }
        .sm-order-bigsearch { order: 21; }

        .sm-field label {
            font-size: .76rem;
            margin-bottom: 3px;
        }

        .sm-field .form-control,
        .sm-field .form-select {
            min-height: 38px;
            font-size: .85rem;
            padding-top: .3rem;
            padding-bottom: .3rem;
        }

        .sm-hero {
            padding: 14px;
        }

        .sm-actions {
            gap: 6px;
        }

        .sm-actions .d-flex {
            width: 100%;
        }

        .sm-actions .btn {
            flex: 1 1 auto;
            padding: 8px 10px;
            font-size: .85rem;
        }
    }

    @media (min-width: 641px) and (max-width: 820px) {
        .sm-col-3 { grid-column: span 4; }
        .sm-col-4 { grid-column: span 6; }
    }
</style>

<div class="stmt-modern">
    <div class="sm-card sm-hero">
        <div>
            <h2 class="sm-hero-title">بحث الكشوف - النسخة الحديثة</h2>
            <p class="sm-hero-sub">واجهة سريعة بفلترة مرنة وتحميل نتائج فوري عبر Ajax.</p>
        </div>
    </div>

    <div class="sm-card sm-filter-wrap">
        <form id="modernSearchForm" action="{{ route('dashboard.statement.query') }}" method="GET" autocomplete="off">
            <div class="sm-filter-grid">
                <div class="sm-field sm-col-3 sm-mobile-full sm-order-name">
                    <label for="smName">الاسم</label>
                    <input id="smName" name="name" type="text" class="form-control" placeholder="أي جزء من الاسم">
                </div>
                <div class="sm-field sm-col-3 sm-order-first">
                    <label for="smFirstName">الاسم الأول</label>
                    <input id="smFirstName" name="first_name" type="text" class="form-control" placeholder="مثال: أحمد">
                </div>
                <div class="sm-field sm-col-3 sm-order-civil">
                    <label for="smCivilId">الرقم المدني</label>
                    <input id="smCivilId" name="id" type="text" class="form-control" placeholder="الرقم المدني">
                </div>
                <div class="sm-field sm-col-3 sm-order-box">
                    <label for="smBox">الصندوق</label>
                    <input id="smBox" name="box" type="text" class="form-control" placeholder="الصندوق">
                </div>

                <div class="sm-field sm-col-4 sm-mobile-full sm-order-family">
                    <label for="smFamily">العائلة</label>
                    <select id="smFamily" name="family" class="form-select sm-dynamic-select">
                        <option value="" hidden>العائلة...</option>
                        <option value="">--</option>
                        @foreach ($relations['families'] as $family)
                            <option value="{{ $family->id }}">{{ $family->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-4 sm-mobile-two sm-order-committee">
                    <label for="smCommittee">اللجنة</label>
                    <select id="smCommittee" name="committee" class="form-select">
                        <option value="">كل اللجان</option>
                        @foreach ($relations['committees'] as $com)
                            <option value="{{ $com->id }}">{{ $com->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-4 sm-mobile-two sm-order-status">
                    <label for="smStatus">الحالة</label>
                    <select id="smStatus" name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="1">حضر الانتخابات</option>
                        <option value="0">لم يحضر الانتخابات</option>
                    </select>
                </div>

                <div class="sm-field sm-col-3 sm-order-fakhd">
                    <label for="smFakhd">الفخذ</label>
                    <select id="smFakhd" name="elfa5z" class="form-select sm-dynamic-select">
                        <option value="" hidden>الفخذ...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['alfkhd'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3 sm-order-faraa">
                    <label for="smFaraa">الفرع</label>
                    <select id="smFaraa" name="elfar3" class="form-select sm-dynamic-select">
                        <option value="" hidden>الفرع...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['alfraa'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3 sm-order-btn">
                    <label for="smBtn">البطن</label>
                    <select id="smBtn" name="elbtn" class="form-select sm-dynamic-select">
                        <option value="" hidden>البطن...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['albtn'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3 sm-order-code1">
                    <label for="smCode1">Code 1</label>
                    <select id="smCode1" name="cod1" class="form-select sm-dynamic-select">
                        <option value="" hidden>Code 1...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['cod1'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3 sm-order-code2">
                    <label for="smCode2">Code 2</label>
                    <select id="smCode2" name="cod2" class="form-select sm-dynamic-select">
                        <option value="" hidden>Code 2...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['cod2'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3 sm-order-code3">
                    <label for="smCode3">Code 3</label>
                    <select id="smCode3" name="cod3" class="form-select sm-dynamic-select">
                        <option value="" hidden>Code 3...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['cod3'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3 sm-mobile-two sm-order-type">
                    <label for="smType">النوع</label>
                    <select id="smType" name="type" class="form-select">
                        <option value="all">الكل</option>
                        <option value="ذكر">ذكر</option>
                        <option value="أنثى">أنثى</option>
                    </select>
                </div>

                <div class="sm-field sm-col-3 sm-mobile-two sm-order-restricted">
                    <label for="smRestricted">حالة القيد</label>
                    <select id="smRestricted" name="restricted" class="form-select">
                        <option value="">الكل</option>
                        <option value="فعال">مقيد</option>
                        <option value="غير مقيد">غير مقيد</option>
                    </select>
                </div>
                <div class="sm-field sm-col-3 sm-mobile-two sm-order-agefrom">
                    <label for="smAgeFrom">العمر من</label>
                    <input id="smAgeFrom" name="age[from]" type="number" class="form-control" placeholder="18">
                </div>
                <div class="sm-field sm-col-3 sm-mobile-two sm-order-ageto">
                    <label for="smAgeTo">العمر إلى</label>
                    <input id="smAgeTo" name="age[to]" type="number" class="form-control" placeholder="80">
                </div>
                <div class="sm-field sm-col-3 sm-mobile-two sm-mobile-emphasis sm-order-bigsearch">
                    <label for="smBigSearch">بحث موسع</label>
                    <select id="smBigSearch" name="search" class="form-select">
                        <option value="">بحث موسع</option>
                        <option value="1">فقط المضامين</option>
                        <option value="0">من غير المضامين</option>
                    </select>
                </div>

                <div class="sm-col-12 sm-mobile-full sm-order-more mt-2">
                    <button type="button" id="smMoreFiltersBtn" class="btn btn-outline-info w-100" aria-expanded="false">خيارات بحث أكثر</button>
                </div>

                <div id="smAdvancedFields" class="sm-advanced-fields sm-order-advanced">
                    <div class="sm-filter-grid sm-advanced-grid">
                        <div class="sm-field sm-col-3">
                            <label for="smPhone">الهاتف</label>
                            <input id="smPhone" name="phone" type="text" class="form-control" placeholder="الهاتف">
                        </div>
                        <div class="sm-field sm-col-3">
                            <label for="smThirdName">الاسم الثالث</label>
                            <input id="smThirdName" name="third_name" type="text" class="form-control" placeholder="الاسم الثالث">
                        </div>
                        <div class="sm-field sm-col-3">
                            <label for="smFourthName">الاسم الرابع</label>
                            <input id="smFourthName" name="fourth_name" type="text" class="form-control" placeholder="الاسم الرابع">
                        </div>
                        <div class="sm-field sm-col-3">
                            <label for="smAlktaa">القطعة</label>
                            <select id="smAlktaa" name="alktaa" class="form-select sm-dynamic-select">
                                <option value="" hidden>القطعة...</option>
                                <option value="">--</option>
                                @foreach ($selectionData['alktaa'] as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm-field sm-col-3">
                            <label for="smStreet">الشارع</label>
                            <select id="smStreet" name="street" class="form-select sm-dynamic-select">
                                <option value="" hidden>الشارع...</option>
                                <option value="">--</option>
                                @foreach (App\Models\Selection::select('street')->whereNotNull('street')->distinct()->get() as $item)
                                    <option value="{{ $item->street }}">{{ $item->street }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm-field sm-col-3">
                            <label for="smAlharaa">الجادة</label>
                            <select id="smAlharaa" name="alharaa" class="form-select sm-dynamic-select">
                                <option value="" hidden>الجادة...</option>
                                <option value="">--</option>
                                @foreach (App\Models\Selection::select('alharaa')->whereNotNull('alharaa')->distinct()->get() as $item)
                                    <option value="{{ $item->alharaa }}">{{ $item->alharaa }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm-field sm-col-3">
                            <label for="smHome">المنزل</label>
                            <select id="smHome" name="home" class="form-select sm-dynamic-select">
                                <option value="" hidden>المنزل...</option>
                                <option value="">--</option>
                                @foreach (App\Models\Selection::select('home')->whereNotNull('home')->distinct()->get() as $item)
                                    <option value="{{ $item->home }}">{{ $item->home }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sm-actions">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">بحث الآن</button>
                    <button type="button" id="smResetBtn" class="btn btn-outline-secondary">إعادة تعيين</button>
                </div>
                <small class="text-muted">كل النتائج تُحمّل بسرعة عبر Ajax بدون إعادة تحميل الصفحة.</small>
            </div>
        </form>
    </div>

    <div class="sm-card sm-export-bar d-none" id="smExportBar">
        <div class="sm-export-meta">اختر الناخبين من النتائج ثم استخرج كشفًا بنفس إعدادات النسخة الكلاسيكية.</div>
        <button type="button" class="btn btn-info" id="smOpenExport" data-bs-toggle="modal" data-bs-target="#smExportModal">
            استخراج كشوف
        </button>
    </div>

    <div class="sm-card">
        <div class="sm-result-head">
            <div class="sm-result-meta">
                <span class="sm-pill">إجمالي النتائج: <span id="smTotalCount">0</span></span>
                <span class="sm-pill">الصفحة: <span id="smCurrentPage">1</span></span>
            </div>
            <small class="text-muted">الترتيب: أبجديًا حسب الاسم</small>
        </div>
        <div class="sm-result-body table-responsive" id="smResultScroll">
            <table class="table sm-table text-center align-middle">
                <thead>
                <tr>
                    <th style="width:42px;">
                        <input type="checkbox" id="smCheckAll" aria-label="تحديد الكل">
                    </th>
                    <th>الاسم</th>
                    <th>العائلة</th>
                    <th>العمر</th>
                    <th>الهاتف</th>
                    <th>الحالة</th>
                    <th>خيارات الأقارب</th>
                </tr>
                </thead>
                <tbody id="smResultsBody">
                <tr><td colspan="7" class="sm-empty">ابدأ البحث لعرض النتائج.</td></tr>
                </tbody>
            </table>
        </div>
        <div id="smPagination" class="sm-pagination"></div>
    </div>
</div>

@include('dashboard.partials.sm-export-modal')

<div class="sm-loading" id="smLoading">
    <div class="sm-loader"></div>
</div>
@endsection

@push('js')
<script>
    (function () {
        const form = document.getElementById('modernSearchForm');
        const loading = document.getElementById('smLoading');
        const resultsBody = document.getElementById('smResultsBody');
        const pagination = document.getElementById('smPagination');
        const resultScroll = document.getElementById('smResultScroll');
        const totalCount = document.getElementById('smTotalCount');
        const currentPage = document.getElementById('smCurrentPage');
        const resetBtn = document.getElementById('smResetBtn');
        const moreFiltersBtn = document.getElementById('smMoreFiltersBtn');
        const advancedFields = document.getElementById('smAdvancedFields');
        const exportBar = document.getElementById('smExportBar');
        const checkAll = document.getElementById('smCheckAll');
        const exportForm = document.getElementById('smExportForm');
        const exportType = document.getElementById('smExportType');
        const exportModalElement = document.getElementById('smExportModal');
        const exportCloseBtn = document.getElementById('smExportCloseBtn');
        const exportWhatsappInput = document.getElementById('smExportWhatsappTo');
        const selectedVoterIds = new Set();
        const exportAsyncUrl = '{{ route('dashboard.statement.export-async') }}';

        let lastParams = null;
        let currentRequestId = 0;
        let hasMorePages = false;
        let nextPageToLoad = 2;
        let isAppending = false;
        let hasActiveSearch = false;

        const dynamicSelectMap = {
            '#smFakhd': 'alfkhd',
            '#smFaraa': 'alfraa',
            '#smBtn': 'albtn',
            '#smAlktaa': 'alktaa',
            '#smCode1': 'cod1',
            '#smCode2': 'cod2',
            '#smCode3': 'cod3',
            '#smFamily': 'family_id',
        };

        const dynamicSelectors = Object.keys(dynamicSelectMap);
        const locationSelectors = ['#smStreet', '#smAlharaa', '#smHome'];
        const allDynamicSelectors = dynamicSelectors.concat(locationSelectors);

        function showLoading() {
            loading.classList.add('show');
        }

        function forceModalCleanup() {
            if (!exportModalElement) return;

            exportModalElement.classList.remove('show');
            exportModalElement.style.display = 'none';
            exportModalElement.setAttribute('aria-hidden', 'true');
            exportModalElement.removeAttribute('aria-modal');
            exportModalElement.removeAttribute('role');

            if (document.querySelector('.modal.show')) {
                return;
            }

            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
            document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
        }

        function closeExportModal() {
            if (!exportModalElement) return;

            if (window.bootstrap && window.bootstrap.Modal) {
                const modalInstance = window.bootstrap.Modal.getInstance
                    ? window.bootstrap.Modal.getInstance(exportModalElement)
                    : (window.bootstrap.Modal.getOrCreateInstance
                        ? window.bootstrap.Modal.getOrCreateInstance(exportModalElement)
                        : null);

                if (modalInstance && typeof modalInstance.hide === 'function') {
                    modalInstance.hide();
                    setTimeout(forceModalCleanup, 50);
                    setTimeout(forceModalCleanup, 220);
                    return;
                }
            }

            if (window.jQuery && typeof window.jQuery(exportModalElement).modal === 'function') {
                window.jQuery(exportModalElement).modal('hide');
                setTimeout(forceModalCleanup, 50);
                setTimeout(forceModalCleanup, 220);
                return;
            }

            forceModalCleanup();
        }

        function setAdvancedOpen(open) {
            if (!advancedFields || !moreFiltersBtn) return;

            advancedFields.classList.toggle('is-open', !!open);
            moreFiltersBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            moreFiltersBtn.textContent = open ? 'إخفاء الخيارات الإضافية' : 'خيارات بحث أكثر';
        }

        function hideLoading() {
            loading.classList.remove('show');
        }

        function toParams(page) {
            const fd = new FormData(form);
            const params = {};
            fd.forEach((value, key) => {
                params[key] = value;
            });

            params.elfa5z = document.querySelector('#smFakhd')?.value || params.elfa5z || '';
            params.elfar3 = document.querySelector('#smFaraa')?.value || params.elfar3 || '';
            params.elbtn = document.querySelector('#smBtn')?.value || params.elbtn || '';
            params.alktaa = document.querySelector('#smAlktaa')?.value || params.alktaa || '';
            params.cod1 = document.querySelector('#smCode1')?.value || params.cod1 || '';
            params.cod2 = document.querySelector('#smCode2')?.value || params.cod2 || '';
            params.cod3 = document.querySelector('#smCode3')?.value || params.cod3 || '';
            params.street = document.querySelector('#smStreet')?.value || params.street || '';
            params.alharaa = document.querySelector('#smAlharaa')?.value || params.alharaa || '';
            params.home = document.querySelector('#smHome')?.value || params.home || '';

            params.page = page || 1;
            params.per_page = '20';
            return params;
        }

        function setEmpty(text) {
            resultsBody.innerHTML = `<tr><td colspan="7" class="sm-empty">${text}</td></tr>`;
            if (checkAll) {
                checkAll.checked = false;
            }
        }

        function updateExportBarVisibility(total) {
            const shouldShow = Number(total || 0) > 0;
            if (!exportBar) return;

            if (!shouldShow) {
                exportBar.classList.remove('is-visible');
                exportBar.classList.add('d-none');
                return;
            }

            if (exportBar.classList.contains('d-none')) {
                exportBar.classList.remove('d-none');
                requestAnimationFrame(() => exportBar.classList.add('is-visible'));
            }
        }

        function getSelectedVoterIdsForExport() {
            const checkedNow = Array.from(resultsBody.querySelectorAll('.sm-check:checked'))
                .map((item) => String(item.value || '').trim())
                .filter((id) => id !== '');

            checkedNow.forEach((id) => selectedVoterIds.add(id));

            return Array.from(selectedVoterIds);
        }

        function renderRows(items, append = false) {
            if (!Array.isArray(items) || items.length === 0) {
                if (!append) {
                    setEmpty('لا توجد نتائج مطابقة.');
                }
                return;
            }

            const rowsHtml = items.map((voter) => {
                const familyName = voter?.family?.name || '--';
                const statusText = Number(voter?.status) === 1 ? 'حضر' : 'لم يحضر';
                const statusClass = Number(voter?.status) === 1 ? 'text-success' : 'text-muted';
                const father = voter?.father ? String(voter.father) : '';
                const grand = voter?.grand ? String(voter.grand) : '';
                const firstDegree = father ? encodeURIComponent(JSON.stringify({ father })) : '';
                const secondDegree = grand ? encodeURIComponent(JSON.stringify({ grand })) : '';

                return `
                    <tr>
                        <td>
                            <input type="checkbox" class="sm-check" value="${voter?.id || ''}" ${selectedVoterIds.has(String(voter?.id || '')) ? 'checked' : ''}>
                        </td>
                        <td class="fw-bold text-dark">${voter?.name || '--'}</td>
                        <td>${familyName}</td>
                        <td>${voter?.age || '--'}</td>
                        <td>${voter?.phone1 || '--'}</td>
                        <td class="${statusClass}">${statusText}</td>
                        <td>
                            <select class="form-control form-control-sm sm-siblings" data-voter-id="${voter?.id || ''}" data-first='${firstDegree}' data-second='${secondDegree}'>
                                <option value="" selected>بحث</option>
                                <option value="first" ${firstDegree ? '' : 'disabled'}>أقارب من الدرجة الاولى</option>
                                <option value="second" ${secondDegree ? '' : 'disabled'}>أقارب من الدرجة التانية</option>
                                <option value="expanded">بحث موسع</option>
                            </select>
                        </td>
                    </tr>
                `;
            }).join('');

            if (append) {
                resultsBody.insertAdjacentHTML('beforeend', rowsHtml);
                return;
            }

            resultsBody.innerHTML = rowsHtml;
        }

        function renderPagination(meta) {
            pagination.innerHTML = '';
        }

        function updateMeta(meta) {
            totalCount.textContent = String(meta?.total || 0);
            currentPage.textContent = String(meta?.current_page || 1);
            updateExportBarVisibility(meta?.total || 0);
        }

        function runSearch(page, options = {}) {
            const append = !!options.append;

            if (append) {
                if (isAppending || !hasMorePages) return;
                isAppending = true;
            }

            const requestId = append ? currentRequestId : ++currentRequestId;
            const params = lastParams ? { ...lastParams, page: page || 1 } : toParams(page || 1);
            if (!append) {
                lastParams = params;
            }

            if (!append) {
                showLoading();
            }

            axios.get(form.action, { params })
                .then((response) => {
                    if (requestId !== currentRequestId) return;

                    const payload = response?.data || {};
                    renderRows(payload.voters || [], append);
                    renderPagination(payload.pagination || {});
                    updateMeta(payload.pagination || {});

                    const currentPage = Number(payload?.pagination?.current_page || 1);
                    const lastPage = Number(payload?.pagination?.last_page || 1);
                    hasMorePages = currentPage < lastPage;
                    nextPageToLoad = currentPage + 1;

                    if (checkAll) {
                        checkAll.checked = false;
                    }
                })
                .catch((error) => {
                    if (requestId !== currentRequestId) return;
                    console.error(error);
                    if (!append) {
                        setEmpty('حدث خطأ أثناء جلب النتائج.');
                    }
                })
                .finally(() => {
                    if (append) {
                        isAppending = false;
                    }
                    if (requestId === currentRequestId && !append) hideLoading();
                });
        }

        function maybeLoadNextPage() {
            if (!hasActiveSearch || !hasMorePages || isAppending) return;

            const scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
            const viewport = window.innerHeight || document.documentElement.clientHeight || 0;
            const fullHeight = document.documentElement.scrollHeight || 0;

            if (scrollTop + viewport >= fullHeight - 220) {
                runSearch(nextPageToLoad, { append: true });
            }
        }

        function buildDynamicFilterParams() {
            return {
                alfkhd: document.querySelector('#smFakhd')?.value || '',
                alfraa: document.querySelector('#smFaraa')?.value || '',
                albtn: document.querySelector('#smBtn')?.value || '',
                alktaa: document.querySelector('#smAlktaa')?.value || '',
                cod1: document.querySelector('#smCode1')?.value || '',
                cod2: document.querySelector('#smCode2')?.value || '',
                cod3: document.querySelector('#smCode3')?.value || '',
                family_id: document.querySelector('#smFamily')?.value || '',
                street: document.querySelector('#smStreet')?.value || '',
                alharaa: document.querySelector('#smAlharaa')?.value || '',
                home: document.querySelector('#smHome')?.value || '',
            };
        }

        function updateDynamicSelect(selector, options) {
            const select = $(selector);
            if (!select.length) return;
            const normalizedSource = options && typeof options === 'object' ? options : {};
            if (Object.keys(normalizedSource).length === 0) return;

            if ((select.val() || '') !== '') {
                return;
            }

            const placeholder = select.data('placeholder-text') || '';
            const normalized = Object.entries(normalizedSource).map(([key, value]) => {
                const optionValue = selector === '#smFamily' ? key : value;
                return [String(optionValue), String(value)];
            });

            select.empty();
            if (placeholder) select.append(`<option value="" hidden>${placeholder}</option>`);
            select.append('<option value="">--</option>');

            normalized.forEach(([optionValue, label]) => {
                select.append(`<option value="${optionValue}">${label}</option>`);
            });

        }

        function refreshDynamicFilters() {
            $.ajax({
                url: '{{ route('filter.selections') }}',
                type: 'GET',
                data: buildDynamicFilterParams(),
                success: function (response) {
                    const map = response?.selectionIds || {};
                    updateDynamicSelect('#smFakhd', map.alfkhd || {});
                    updateDynamicSelect('#smFaraa', map.alfraa || {});
                    updateDynamicSelect('#smBtn', map.albtn || {});
                    updateDynamicSelect('#smAlktaa', map.alktaa || {});
                    updateDynamicSelect('#smCode1', map.cod1 || {});
                    updateDynamicSelect('#smCode2', map.cod2 || {});
                    updateDynamicSelect('#smCode3', map.cod3 || {});
                    updateDynamicSelect('#smFamily', map.family_id || {});

                    const locationMap = response?.locationOptions || {};
                    updateDynamicSelect('#smStreet', locationMap.street || {});
                    updateDynamicSelect('#smAlharaa', locationMap.alharaa || {});
                    updateDynamicSelect('#smHome', locationMap.home || {});
                }
            });
        }

        allDynamicSelectors.forEach(function (selector) {
            const select = $(selector);
            if (!select.length) return;

            const placeholderOption = select.find('option[hidden]').first();
            const placeholderText = placeholderOption.length ? placeholderOption.text() : '';
            select.data('placeholder-text', placeholderText);
        });

        $(allDynamicSelectors.join(',')).on('change', function () {
            refreshDynamicFilters();
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            lastParams = toParams(1);
            hasMorePages = false;
            nextPageToLoad = 2;
            isAppending = false;
            hasActiveSearch = true;
            runSearch(1, { append: false });
        });

        resultsBody.addEventListener('change', function (event) {
            const select = event.target.closest('.sm-siblings');
            if (!select) return;

            const mode = select.value;
            if (!mode) return;

            const params = toParams(1);
            delete params.siblings;

            if (mode === 'first' && select.dataset.first) {
                params.siblings = decodeURIComponent(select.dataset.first);
            } else if (mode === 'second' && select.dataset.second) {
                params.siblings = decodeURIComponent(select.dataset.second);
            } else if (mode === 'expanded') {
                params.search = '1';
            }

            lastParams = params;
            hasMorePages = false;
            nextPageToLoad = 2;
            isAppending = false;
            hasActiveSearch = true;
            runSearch(1, { append: false });
        });

        resultsBody.addEventListener('change', function (event) {
            const checkbox = event.target.closest('.sm-check');
            if (!checkbox) return;

            const id = String(checkbox.value || '');
            if (!id) return;

            if (checkbox.checked) {
                selectedVoterIds.add(id);
            } else {
                selectedVoterIds.delete(id);
            }
        });

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                const checked = !!checkAll.checked;
                resultsBody.querySelectorAll('.sm-check').forEach((item) => {
                    item.checked = checked;
                    const id = String(item.value || '');
                    if (!id) return;
                    if (checked) {
                        selectedVoterIds.add(id);
                    } else {
                        selectedVoterIds.delete(id);
                    }
                });
            });
        }

        document.querySelectorAll('.sm-export-action').forEach((button) => {
            button.addEventListener('click', function () {
                const actionType = button.value;
                const selectedIds = getSelectedVoterIdsForExport();

                if (selectedIds.length === 0) {
                    toastr.warning('اختر ناخبًا واحدًا على الأقل قبل استخراج الكشوف.');
                    return;
                }

                exportType.value = actionType;

                exportForm.querySelectorAll('input[name="voter[]"]').forEach((input) => input.remove());
                selectedIds.forEach((id) => {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'voter[]';
                    hidden.value = id;
                    exportForm.appendChild(hidden);
                });

                const submitBtn = button;
                submitBtn.disabled = true;

                const formData = new FormData(exportForm);
                const queryData = {};
                formData.forEach((value, key) => {
                    if (Object.prototype.hasOwnProperty.call(queryData, key)) {
                        if (!Array.isArray(queryData[key])) {
                            queryData[key] = [queryData[key]];
                        }
                        queryData[key].push(value || '');
                    } else {
                        queryData[key] = value || '';
                    }
                });

                if (actionType === 'Excel' || actionType === 'PDF') {
                    closeExportModal();

                    axios.post(exportAsyncUrl, formData, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then((res) => {
                            const message = res?.data?.message || 'بدأ تجهيز الملف في الخلفية. سيتم إرسال إشعار عند الانتهاء.';
                            toastr.success(message);

                        })
                        .catch((error) => {
                            console.error(error);
                            toastr.error(error?.response?.data?.message || 'تعذر بدء تجهيز الملف. حاول مرة أخرى.');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                        });

                    return;
                }

                axios.get(exportForm.action, {
                    params: queryData,
                    responseType: actionType === 'Excel' || actionType === 'PDF' ? 'blob' : 'json',
                })
                    .then(async (res) => {
                        if (actionType === 'Excel' || actionType === 'PDF') {
                            const contentType = String(res?.headers?.['content-type'] || '').toLowerCase();
                            if (contentType.includes('text/html') || contentType.includes('application/json')) {
                                const errorText = await res.data.text();
                                toastr.error('تعذر استخراج الملف. تأكد من تحديد ناخبين صالحين ثم حاول مرة أخرى.');
                                console.error('Export unexpected payload:', errorText);
                                return;
                            }

                            const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
                            const link = document.createElement('a');
                            link.href = fileUrl;
                            link.setAttribute('download', actionType === 'Excel' ? 'Voters.xlsx' : 'Voters.pdf');
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                            window.URL.revokeObjectURL(fileUrl);
                            return;
                        }

                        if (actionType === 'Send' && res.data?.Redirect_Url) {
                            const newTab = window.open();
                            newTab.document.open();
                            newTab.location.href = res.data.Redirect_Url;
                            newTab.document.close();
                            return;
                        }

                        const newTab = window.open();
                        newTab.document.open();
                        newTab.document.write(res.data);
                        newTab.document.close();
                    })
                    .catch((error) => {
                        console.error(error);
                        toastr.error(error.response?.data?.error || 'حدث خطأ غير متوقع');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                    });
            });
        });

        resetBtn.addEventListener('click', function () {
            form.reset();
            lastParams = null;
            selectedVoterIds.clear();
            setEmpty('تمت إعادة التعيين. ابدأ البحث لعرض النتائج.');
            totalCount.textContent = '0';
            currentPage.textContent = '1';
            pagination.innerHTML = '';
            updateExportBarVisibility(0);
            setAdvancedOpen(false);
            hasMorePages = false;
            nextPageToLoad = 2;
            isAppending = false;
            hasActiveSearch = false;
            refreshDynamicFilters();
        });

        window.addEventListener('scroll', maybeLoadNextPage, { passive: true });

        if (moreFiltersBtn) {
            moreFiltersBtn.addEventListener('click', function () {
                const isOpen = advancedFields?.classList.contains('is-open');
                setAdvancedOpen(!isOpen);
            });
        }

        if (exportCloseBtn) {
            exportCloseBtn.setAttribute('type', 'button');
            exportCloseBtn.addEventListener('click', function (event) {
                event.preventDefault();
                closeExportModal();
            });
        }

        if (exportModalElement) {
            exportModalElement.addEventListener('shown.bs.modal', function () {
                if (!exportWhatsappInput) return;

                exportWhatsappInput.disabled = false;
                exportWhatsappInput.readOnly = false;
                exportWhatsappInput.style.pointerEvents = 'auto';

                setTimeout(function () {
                    try {
                        exportWhatsappInput.focus();
                        exportWhatsappInput.select();
                    } catch (error) {
                    }
                }, 0);
            });

            exportModalElement.addEventListener('hidden.bs.modal', function () {
                forceModalCleanup();
            });
        }

        if (exportForm) {
            exportForm.addEventListener('submit', function (event) {
                event.preventDefault();
            });
        }

        setAdvancedOpen(false);

        refreshDynamicFilters();
    })();
</script>
@endpush
