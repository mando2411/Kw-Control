@extends('layouts.dashboard.app')

@section('content')
<style>
    .stmt-modern {
        direction: rtl;
        max-width: 1400px;
        margin: 0 auto;
        padding: 12px;
    }

    .stmt-modern .sm-card {
        background: var(--ui-surface, #fff);
        border: 1px solid var(--ui-border, rgba(148, 163, 184, .22));
        border-radius: 18px;
        box-shadow: var(--ui-shadow, 0 12px 28px rgba(2, 6, 23, .08));
    }

    .sm-hero {
        padding: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .sm-hero-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--ui-ink, #0f172a);
    }

    .sm-hero-sub {
        margin: 4px 0 0;
        color: var(--ui-muted, #64748b);
        font-size: .92rem;
    }

    .sm-hero-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .sm-filter-wrap {
        padding: 16px;
        margin-bottom: 12px;
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
        margin-bottom: 4px;
        color: var(--ui-muted, #64748b);
        font-size: .82rem;
        font-weight: 800;
    }

    .sm-field .form-control,
    .sm-field .form-select {
        border-radius: 12px;
        min-height: 42px;
        border-color: rgba(148,163,184,.35);
    }

    .sm-field .form-select {
        direction: rtl;
        background-position: left .75rem center;
        appearance: auto;
    }

    .sm-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .sm-result-head {
        padding: 12px 14px;
        border-bottom: 1px solid var(--ui-border, rgba(148,163,184,.22));
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .sm-result-meta {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .sm-pill {
        background: rgba(14,165,233,.10);
        color: var(--ui-accent, #0ea5e9);
        border: 1px solid rgba(14,165,233,.24);
        border-radius: 999px;
        padding: 4px 10px;
        font-size: .78rem;
        font-weight: 800;
    }

    .sm-result-body {
        padding: 10px;
    }

    .sm-table {
        margin: 0;
    }

    .sm-table thead th {
        font-size: .82rem;
        color: var(--ui-muted, #64748b);
        border-bottom-width: 1px;
        white-space: nowrap;
    }

    .sm-table td {
        vertical-align: middle;
        font-size: .9rem;
    }

    .sm-empty {
        text-align: center;
        color: var(--ui-muted, #64748b);
        font-weight: 700;
        padding: 20px;
    }

    .sm-pagination {
        padding: 10px 14px 14px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
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
        .sm-col-6,
        .sm-col-4,
        .sm-col-3 { grid-column: span 12; }

        .sm-hero {
            padding: 14px;
        }
    }
</style>

<div class="stmt-modern">
    <div class="sm-card sm-hero">
        <div>
            <h2 class="sm-hero-title">بحث الكشوف - النسخة الحديثة</h2>
            <p class="sm-hero-sub">واجهة سريعة بفلترة مرنة وتحميل نتائج فوري عبر Ajax.</p>
        </div>
        <div class="sm-hero-actions">
            <a href="{{ route('dashboard.statement.search') }}" class="btn btn-outline-secondary btn-sm">النسخة الكلاسيك</a>
        </div>
    </div>

    <div class="sm-card sm-filter-wrap">
        <form id="modernSearchForm" action="{{ route('dashboard.statement.query') }}" method="GET" autocomplete="off">
            <div class="sm-filter-grid">
                <div class="sm-field sm-col-3">
                    <label for="smFirstName">الاسم الأول</label>
                    <input id="smFirstName" name="first_name" type="text" class="form-control" placeholder="مثال: أحمد">
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smName">الاسم</label>
                    <input id="smName" name="name" type="text" class="form-control" placeholder="أي جزء من الاسم">
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smPhone">الهاتف</label>
                    <input id="smPhone" name="phone" type="text" class="form-control" placeholder="الهاتف">
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smCivilId">الرقم المدني</label>
                    <input id="smCivilId" name="id" type="text" class="form-control" placeholder="الرقم المدني">
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smBox">الصندوق</label>
                    <input id="smBox" name="box" type="text" class="form-control" placeholder="الصندوق">
                </div>

                <div class="sm-field sm-col-4">
                    <label for="smFamily">العائلة</label>
                    <select id="smFamily" name="family" class="form-select sm-dynamic-select">
                        <option value="" hidden>العائلة...</option>
                        <option value="">--</option>
                        @foreach ($relations['families'] as $family)
                            <option value="{{ $family->id }}">{{ $family->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-4">
                    <label for="smCommittee">اللجنة</label>
                    <select id="smCommittee" name="committee" class="form-select">
                        <option value="">كل اللجان</option>
                        @foreach ($relations['committees'] as $com)
                            <option value="{{ $com->id }}">{{ $com->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-4">
                    <label for="smStatus">الحالة</label>
                    <select id="smStatus" name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="1">حضر الانتخابات</option>
                        <option value="0">لم يحضر الانتخابات</option>
                    </select>
                </div>

                <div class="sm-field sm-col-3">
                    <label for="smFakhd">الفخذ</label>
                    <select id="smFakhd" name="elfa5z" class="form-select sm-dynamic-select">
                        <option value="" hidden>الفخذ...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['alfkhd'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smFaraa">الفرع</label>
                    <select id="smFaraa" name="elfar3" class="form-select sm-dynamic-select">
                        <option value="" hidden>الفرع...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['alfraa'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smBtn">البطن</label>
                    <select id="smBtn" name="elbtn" class="form-select sm-dynamic-select">
                        <option value="" hidden>البطن...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['albtn'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
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

                <div class="sm-field sm-col-3">
                    <label for="smCode1">Code 1</label>
                    <select id="smCode1" name="cod1" class="form-select sm-dynamic-select">
                        <option value="" hidden>Code 1...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['cod1'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smCode2">Code 2</label>
                    <select id="smCode2" name="cod2" class="form-select sm-dynamic-select">
                        <option value="" hidden>Code 2...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['cod2'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smCode3">Code 3</label>
                    <select id="smCode3" name="cod3" class="form-select sm-dynamic-select">
                        <option value="" hidden>Code 3...</option>
                        <option value="">--</option>
                        @foreach ($selectionData['cod3'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smType">النوع</label>
                    <select id="smType" name="type" class="form-select">
                        <option value="all">الكل</option>
                        <option value="ذكر">ذكر</option>
                        <option value="أنثى">أنثى</option>
                    </select>
                </div>

                <div class="sm-field sm-col-3">
                    <label for="smRestricted">حالة القيد</label>
                    <select id="smRestricted" name="restricted" class="form-select">
                        <option value="">الكل</option>
                        <option value="فعال">مقيد</option>
                        <option value="غير مقيد">غير مقيد</option>
                    </select>
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smAgeFrom">العمر من</label>
                    <input id="smAgeFrom" name="age[from]" type="number" class="form-control" placeholder="18">
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smAgeTo">العمر إلى</label>
                    <input id="smAgeTo" name="age[to]" type="number" class="form-control" placeholder="80">
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smPerPage">عدد النتائج</label>
                    <select id="smPerPage" name="per_page" class="form-select">
                        <option value="50">50</option>
                        <option value="100" selected>100</option>
                        <option value="200">200</option>
                        <option value="500">500</option>
                    </select>
                </div>
                <div class="sm-field sm-col-3">
                    <label for="smBigSearch">بحث موسع</label>
                    <select id="smBigSearch" name="search" class="form-select">
                        <option value="">بحث موسع</option>
                        <option value="1">فقط المضامين</option>
                        <option value="0">من غير المضامين</option>
                    </select>
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

    <div class="sm-card">
        <div class="sm-result-head">
            <div class="sm-result-meta">
                <span class="sm-pill">إجمالي النتائج: <span id="smTotalCount">0</span></span>
                <span class="sm-pill">الصفحة: <span id="smCurrentPage">1</span></span>
            </div>
            <small class="text-muted">الترتيب: أبجديًا حسب الاسم</small>
        </div>
        <div class="sm-result-body table-responsive">
            <table class="table sm-table text-center align-middle">
                <thead>
                <tr>
                    <th>الاسم</th>
                    <th>العائلة</th>
                    <th>العمر</th>
                    <th>الهاتف</th>
                    <th>الحالة</th>
                    <th>خيارات الأقارب</th>
                </tr>
                </thead>
                <tbody id="smResultsBody">
                <tr><td colspan="6" class="sm-empty">ابدأ البحث لعرض النتائج.</td></tr>
                </tbody>
            </table>
        </div>
        <div id="smPagination" class="sm-pagination"></div>
    </div>
</div>

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
        const totalCount = document.getElementById('smTotalCount');
        const currentPage = document.getElementById('smCurrentPage');
        const resetBtn = document.getElementById('smResetBtn');

        let lastParams = null;
        let currentRequestId = 0;

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
            params.per_page = params.per_page || '100';
            return params;
        }

        function setEmpty(text) {
            resultsBody.innerHTML = `<tr><td colspan="6" class="sm-empty">${text}</td></tr>`;
        }

        function renderRows(items) {
            if (!Array.isArray(items) || items.length === 0) {
                setEmpty('لا توجد نتائج مطابقة.');
                return;
            }

            resultsBody.innerHTML = items.map((voter) => {
                const familyName = voter?.family?.name || '--';
                const statusText = Number(voter?.status) === 1 ? 'حضر' : 'لم يحضر';
                const statusClass = Number(voter?.status) === 1 ? 'text-success' : 'text-muted';
                const father = voter?.father ? String(voter.father) : '';
                const grand = voter?.grand ? String(voter.grand) : '';
                const firstDegree = father ? encodeURIComponent(JSON.stringify({ father })) : '';
                const secondDegree = grand ? encodeURIComponent(JSON.stringify({ grand })) : '';

                return `
                    <tr>
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
        }

        function renderPagination(meta) {
            pagination.innerHTML = '';
            if (!meta || Number(meta.last_page) <= 1) {
                return;
            }

            const now = Number(meta.current_page || 1);
            const last = Number(meta.last_page || 1);
            const start = Math.max(1, now - 2);
            const end = Math.min(last, now + 2);

            const prevDisabled = now <= 1 ? 'disabled' : '';
            pagination.insertAdjacentHTML('beforeend', `<button class="btn btn-sm btn-outline-secondary" data-page="${now - 1}" ${prevDisabled}>السابق</button>`);

            for (let p = start; p <= end; p++) {
                const cls = p === now ? 'btn-primary' : 'btn-outline-primary';
                pagination.insertAdjacentHTML('beforeend', `<button class="btn btn-sm ${cls}" data-page="${p}">${p}</button>`);
            }

            const nextDisabled = now >= last ? 'disabled' : '';
            pagination.insertAdjacentHTML('beforeend', `<button class="btn btn-sm btn-outline-secondary" data-page="${now + 1}" ${nextDisabled}>التالي</button>`);
        }

        function updateMeta(meta) {
            totalCount.textContent = String(meta?.total || 0);
            currentPage.textContent = String(meta?.current_page || 1);
        }

        function runSearch(page) {
            const requestId = ++currentRequestId;
            const params = lastParams ? { ...lastParams, page: page || 1 } : toParams(page || 1);
            lastParams = params;

            showLoading();
            axios.get(form.action, { params })
                .then((response) => {
                    if (requestId !== currentRequestId) return;

                    const payload = response?.data || {};
                    renderRows(payload.voters || []);
                    renderPagination(payload.pagination || {});
                    updateMeta(payload.pagination || {});
                })
                .catch((error) => {
                    if (requestId !== currentRequestId) return;
                    console.error(error);
                    setEmpty('حدث خطأ أثناء جلب النتائج.');
                })
                .finally(() => {
                    if (requestId === currentRequestId) hideLoading();
                });
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
            runSearch(1);
        });

        pagination.addEventListener('click', function (event) {
            const btn = event.target.closest('[data-page]');
            if (!btn || btn.disabled) return;
            const targetPage = Number(btn.getAttribute('data-page'));
            if (!targetPage || targetPage < 1) return;
            runSearch(targetPage);
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
            runSearch(1);
        });

        resetBtn.addEventListener('click', function () {
            form.reset();
            lastParams = null;
            setEmpty('تمت إعادة التعيين. ابدأ البحث لعرض النتائج.');
            totalCount.textContent = '0';
            currentPage.textContent = '1';
            pagination.innerHTML = '';
            refreshDynamicFilters();
        });

        refreshDynamicFilters();
    })();
</script>
@endpush
