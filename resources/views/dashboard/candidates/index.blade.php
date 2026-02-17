@extends('layouts.dashboard.app')

@section('content')
<div class="page-body candidates-index-page">
    <x-dashboard.partials.breadcrumb title="المرشحون">
        <li class="breadcrumb-item active">المرشحون</li>
    </x-dashboard.partials.breadcrumb>

    <div class="container-fluid">
        <x-dashboard.partials.message-alert />

        <section class="candidate-hero mb-3">
            <div class="candidate-hero__content">
                <h3 class="candidate-hero__title mb-1">لوحة إدارة المرشحين</h3>
                <p class="candidate-hero__subtitle mb-0">واجهة احترافية لإدارة بيانات المرشحين بسرعة ووضوح مع أوضاع عرض متعددة.</p>
            </div>
            <div class="candidate-hero__stats">
                <div class="candidate-stat-pill">
                    <span class="label">إجمالي المرشحين</span>
                    <strong>{{ $candidates->count() }}</strong>
                </div>
                <div class="candidate-stat-pill">
                    <span class="label">الانتخابات المرتبطة</span>
                    <strong>{{ $candidates->pluck('election_id')->filter()->unique()->count() }}</strong>
                </div>
            </div>
        </section>

        <section class="candidate-toolbar card mb-3">
            <div class="candidate-toolbar__left">
                <div class="candidate-search-wrap">
                    <i class="fa fa-search"></i>
                    <input id="candidate-search-input" aria-label="Search" class="form-control" type="search" placeholder="بحث باسم المرشح...">
                </div>
            </div>

            <div class="candidate-toolbar__right">
                <div class="candidate-view-switch" role="tablist" aria-label="تبديل طريقة العرض">
                    <button type="button" class="btn btn-primary" id="viewModeToggleBtn" role="button" aria-live="polite">
                        <i class="fa fa-table me-1"></i>
                        الجدول
                    </button>
                </div>

                <div class="candidate-toolbar__actions">
                    @if(\Illuminate\Support\Facades\Route::has('dashboard.candidates.create') && admin()->can('candidates.create'))
                        <a href="{{ route('dashboard.candidates.create') }}" class="btn btn-primary add-row">
                            <i class="fa fa-plus me-1"></i>
                            إضافة مرشح
                        </a>
                    @endif

                    <a id="addFakeCandidate" type="button" class="btn btn-danger add-row">
                        <i class="fa fa-user-secret me-1"></i>
                        إضافة مرشح وهمي
                    </a>
                </div>
            </div>
        </section>

        <section class="candidate-content-shell card">
            <div class="card-body">
                <div id="professionalCandidatesView" class="candidate-cards-grid-view">
                    <div class="candidate-section-caption mb-3">
                        <i class="fa fa-image me-1"></i>
                        مرّر على أي بطاقة لإظهار التفاصيل والإجراءات السريعة
                    </div>

                    <div class="candidate-cards-grid">
                        @foreach($candidates as $candidate)
                            @php
                                $image = $candidate->user?->image ?: 'https://ui-avatars.com/api/?name=' . urlencode($candidate->user?->name ?? 'Candidate') . '&background=0ea5e9&color=fff&size=400';
                                $currentContractors = (int) ($candidate->user?->contractors_count ?? 0);
                                $currentRepresentatives = (int) ($candidate->user?->representatives_count ?? 0);
                                $maxContractors = max(0, (int) ($candidate->max_contractor ?? 0));
                                $maxRepresentatives = max(0, (int) ($candidate->max_represent ?? 0));
                                $contractorsPercent = $maxContractors > 0 ? min(100, (int) round(($currentContractors / $maxContractors) * 100)) : 0;
                                $representativesPercent = $maxRepresentatives > 0 ? min(100, (int) round(($currentRepresentatives / $maxRepresentatives) * 100)) : 0;
                                $createdAt = optional($candidate->created_at)->format('Y/m/d') ?? '—';
                            @endphp

                            <article class="candidate-card-item">
                                <div class="candidate-card-image" style="background-image: url('{{ $image }}')">
                                    <div class="candidate-card-name-badge">
                                        <span>{{ $candidate->user?->name ?? '—' }}</span>
                                    </div>

                                    <div class="candidate-card-campaign-badge" title="اسم الحملة">
                                        <i class="fa fa-check-circle"></i>
                                        <span>{{ $candidate->election?->name ?? 'حملة غير محددة' }}</span>
                                    </div>

                                    <div class="candidate-card-overlay">
                                        <div class="candidate-card-created">
                                            <i class="fa fa-calendar me-1"></i>
                                            تم إضافته في {{ $createdAt }}
                                        </div>

                                        <div class="candidate-card-meta">
                                            <div class="candidate-metric">
                                                <div class="candidate-metric__head">
                                                    <span class="metric-label">المتعهدين</span>
                                                    <span class="metric-value">{{ $currentContractors }}/{{ $maxContractors }}</span>
                                                </div>
                                                <div class="candidate-metric__bar">
                                                    <span style="width: {{ $contractorsPercent }}%"></span>
                                                </div>
                                            </div>

                                            <div class="candidate-metric">
                                                <div class="candidate-metric__head">
                                                    <span class="metric-label">المناديب</span>
                                                    <span class="metric-value">{{ $currentRepresentatives }}/{{ $maxRepresentatives }}</span>
                                                </div>
                                                <div class="candidate-metric__bar">
                                                    <span style="width: {{ $representativesPercent }}%"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="candidate-card-actions">
                                            <a href="{{ route('dashboard.candidates.edit', $candidate->id) }}" class="btn btn-sm btn-light">
                                                <i class="fa fa-edit me-1"></i>تعديل
                                            </a>

                                            <a data-delete-url="{{ route('dashboard.candidates.destroy', $candidate->id) }}" href="javascript:;"
                                               type="button" class="btn btn-sm btn-danger btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal">
                                                <i class="fa fa-trash me-1"></i>حذف
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div id="tableCandidatesView" class="order-datatable overflow-x-auto d-none">
                    <div class="candidate-section-caption mb-3">
                        <i class="fa fa-list me-1"></i>
                        عرض جدولي كامل مع البحث والتصدير
                    </div>
                    {!! $dataTable->table(['class'=>'display']) !!}
                </div>
            </div>
        </section>
    </div>

    @include('dashboard.candidates.fake_candidate_pop_up')
</div>
@endsection

@push('css')
<style>
    .candidates-index-page {
        direction: rtl;
    }

    .candidates-index-page .candidate-hero {
        border: 1px solid rgba(148, 163, 184, .24);
        border-radius: 16px;
        padding: 1rem 1.1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .9rem;
        flex-wrap: wrap;
        background: linear-gradient(120deg, rgba(59,130,246,.12), rgba(99,102,241,.10), rgba(255,255,255,.98));
        box-shadow: 0 12px 26px rgba(15, 23, 42, .08);
    }

    .candidates-index-page .candidate-hero__title {
        font-size: 1.2rem;
        font-weight: 900;
        color: #0f172a;
    }

    .candidates-index-page .candidate-hero__subtitle {
        font-size: .88rem;
        color: #475569;
        font-weight: 600;
    }

    .candidates-index-page .candidate-hero__stats {
        display: flex;
        gap: .55rem;
        flex-wrap: wrap;
    }

    .candidates-index-page .candidate-stat-pill {
        background: #fff;
        border: 1px solid rgba(148, 163, 184, .32);
        border-radius: 999px;
        padding: .38rem .8rem;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        box-shadow: 0 6px 14px rgba(15, 23, 42, .06);
    }

    .candidates-index-page .candidate-stat-pill .label {
        font-size: .76rem;
        color: #475569;
        font-weight: 700;
    }

    .candidates-index-page .candidate-stat-pill strong {
        font-size: .9rem;
        color: #0f172a;
        font-weight: 900;
    }

    .candidates-index-page .candidate-toolbar {
        border: 1px solid rgba(148, 163, 184, .24);
        border-radius: 14px;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .07);
        padding: .85rem;
        display: grid;
        grid-template-columns: minmax(260px, 1fr) auto;
        align-items: start;
        gap: .8rem;
    }

    .candidates-index-page .candidate-toolbar__left {
        min-width: 0;
    }

    .candidates-index-page .candidate-toolbar__right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .candidates-index-page .candidate-toolbar__actions {
        display: flex;
        align-items: center;
        gap: .5rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .candidates-index-page .candidate-search-wrap {
        position: relative;
        min-width: min(100%, 320px);
    }

    .candidates-index-page .candidate-search-wrap i {
        position: absolute;
        right: .7rem;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: .82rem;
    }

    .candidates-index-page .candidate-search-wrap input {
        min-height: 38px;
        border-radius: 999px;
        padding-right: 2rem;
        border: 1px solid rgba(148, 163, 184, .35);
        font-size: .84rem;
        font-weight: 700;
    }

    .candidates-index-page .candidate-view-switch {
        display: flex;
        gap: .4rem;
        flex-wrap: wrap;
        background: #eef2ff;
        border: 1px solid rgba(99, 102, 241, .22);
        border-radius: 999px;
        padding: .22rem;
    }

    .candidates-index-page .candidate-view-switch .btn {
        border-radius: 999px;
        border-width: 1px;
        min-height: 36px;
        font-weight: 800;
        font-size: .82rem;
        min-width: 130px;
    }

    .candidates-index-page .candidate-toolbar__actions .btn {
        min-height: 38px;
        border-radius: 10px;
        font-size: .82rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .candidates-index-page .candidate-content-shell {
        border: 1px solid rgba(148, 163, 184, .24);
        border-radius: 16px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .candidates-index-page .candidate-section-caption {
        color: #475569;
        font-size: .84rem;
        font-weight: 700;
    }

    .candidates-index-page .candidate-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(245px, 1fr));
        gap: 1rem;
    }

    .candidates-index-page .candidate-card-item {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, .3);
        background: linear-gradient(160deg, rgba(255,255,255,.95), rgba(248,250,252,.9));
        box-shadow: 0 10px 20px rgba(15, 23, 42, .1), 0 1px 0 rgba(255,255,255,.5) inset;
        transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
    }

    .candidates-index-page .candidate-card-item:hover {
        transform: translateY(-6px);
        border-color: rgba(99, 102, 241, .38);
        box-shadow: 0 18px 34px rgba(15, 23, 42, .18), 0 0 0 1px rgba(99,102,241,.12);
    }

    .candidates-index-page .candidate-card-image {
        position: relative;
        min-height: 330px;
        background-size: cover;
        background-position: center;
        isolation: isolate;
        transition: transform .38s ease;
    }

    .candidates-index-page .candidate-card-image::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 18% 12%, rgba(255,255,255,.24), transparent 44%);
        pointer-events: none;
        z-index: 1;
    }

    .candidates-index-page .candidate-card-item:hover .candidate-card-image {
        transform: scale(1.02);
    }

    .candidates-index-page .candidate-card-name-badge {
        position: absolute;
        top: .65rem;
        right: .65rem;
        left: .65rem;
        z-index: 3;
        display: flex;
        justify-content: center;
        pointer-events: none;
    }

    .candidates-index-page .candidate-card-name-badge span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        max-width: 100%;
        min-height: 38px;
        padding: .35rem .8rem;
        border-radius: 999px;
        background: linear-gradient(120deg, rgba(15, 23, 42, .82), rgba(30, 41, 59, .76));
        border: 1px solid rgba(255, 255, 255, .26);
        box-shadow: 0 10px 20px rgba(2, 6, 23, .35);
        color: #ffffff;
        font-size: .95rem;
        font-weight: 900;
        letter-spacing: .2px;
        text-shadow: 0 1px 2px rgba(0,0,0,.35);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .candidates-index-page .candidate-card-campaign-badge {
        position: absolute;
        left: .65rem;
        top: .7rem;
        z-index: 3;
        max-width: calc(100% - 1.3rem);
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        min-height: 32px;
        padding: .3rem .62rem;
        border-radius: 999px;
        background: linear-gradient(120deg, rgba(37, 99, 235, .92), rgba(14, 116, 144, .9));
        border: 1px solid rgba(255,255,255,.32);
        box-shadow: 0 8px 18px rgba(2, 6, 23, .3);
        color: #fff;
        font-size: .74rem;
        font-weight: 800;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .candidates-index-page .candidate-card-campaign-badge i {
        font-size: .86rem;
        color: rgba(255,255,255,.96);
        flex: 0 0 auto;
    }

    .candidates-index-page .candidate-card-campaign-badge span {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .candidates-index-page .candidate-card-overlay {
        position: absolute;
        right: 0;
        left: 0;
        bottom: 0;
        height: 60%;
        padding: .95rem;
        color: #fff;
        background: linear-gradient(to top, rgba(2, 6, 23, .97), rgba(2, 6, 23, .86) 54%, rgba(2, 6, 23, .28));
        transform: translateY(64%);
        opacity: .96;
        transition: transform .34s cubic-bezier(.2,.8,.2,1), opacity .28s ease;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: .5rem;
        backdrop-filter: blur(4px);
        z-index: 2;
        border-top: 1px solid rgba(255,255,255,.13);
    }

    .candidates-index-page .candidate-card-item:hover .candidate-card-overlay {
        transform: translateY(0);
    }

    .candidates-index-page .candidate-card-created {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        font-size: .76rem;
        font-weight: 800;
        border-radius: 999px;
        padding: .28rem .6rem;
        color: #f8fafc;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.22);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.18);
    }

    .candidates-index-page .candidate-card-meta {
        display: grid;
        gap: .42rem;
    }

    .candidates-index-page .candidate-metric {
        background: linear-gradient(145deg, rgba(255,255,255,.13), rgba(255,255,255,.06));
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 10px;
        padding: .38rem .46rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.13);
    }

    .candidates-index-page .candidate-metric__head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .26rem;
        font-size: .76rem;
        font-weight: 700;
    }

    .candidates-index-page .candidate-metric .metric-label {
        color: rgba(255,255,255,.88);
    }

    .candidates-index-page .candidate-metric .metric-value {
        color: #ffffff;
        font-weight: 900;
        letter-spacing: .2px;
    }

    .candidates-index-page .candidate-metric__bar {
        width: 100%;
        height: 6px;
        background: rgba(255,255,255,.24);
        border-radius: 999px;
        overflow: hidden;
    }

    .candidates-index-page .candidate-metric__bar span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #22d3ee, #6366f1);
        transition: width .28s ease;
    }

    .candidates-index-page .candidate-card-actions {
        display: flex;
        gap: .45rem;
        flex-wrap: wrap;
        margin-top: .1rem;
    }

    .candidates-index-page .candidate-card-actions .btn {
        font-size: .78rem;
        font-weight: 800;
        min-width: 92px;
        border-radius: 11px;
        padding: .34rem .62rem;
        border-width: 1px;
        transition: transform .24s ease, box-shadow .24s ease, background-color .24s ease;
    }

    .candidates-index-page .candidate-card-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(2, 6, 23, .26);
    }

    .candidates-index-page .candidate-card-actions .btn-light {
        background: rgba(255,255,255,.94);
        border-color: rgba(255,255,255,.98);
        color: #0f172a;
    }

    .candidates-index-page .candidate-card-actions .btn-light:hover {
        background: #ffffff;
    }

    .candidates-index-page .candidate-card-actions .btn-danger {
        background: linear-gradient(120deg, #dc2626, #b91c1c);
        border-color: rgba(239,68,68,.9);
    }

    .candidates-index-page #tableCandidatesView {
        border: 1px solid rgba(148, 163, 184, .24);
        border-radius: 12px;
        padding: .75rem;
        background: #fff;
    }

    .candidates-index-page #tableCandidatesView .dataTables_wrapper .dt-buttons .btn {
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 700;
        padding: .32rem .58rem;
    }

    @media (max-width: 768px) {
        .candidates-index-page .candidate-toolbar {
            grid-template-columns: 1fr;
            align-items: stretch;
            gap: .65rem;
        }

        .candidates-index-page .candidate-toolbar__right {
            width: 100%;
            justify-content: stretch;
        }

        .candidates-index-page .candidate-view-switch {
            width: 100%;
        }

        .candidates-index-page .candidate-view-switch .btn {
            flex: 1 1 calc(50% - .3rem);
        }

        .candidates-index-page .candidate-search-wrap {
            min-width: 100%;
        }

        .candidates-index-page .candidate-toolbar__actions {
            width: 100%;
            justify-content: stretch;
        }

        .candidates-index-page .candidate-toolbar__actions .btn {
            flex: 1 1 calc(50% - .3rem);
            justify-content: center;
        }

        .candidates-index-page .candidate-card-image {
            min-height: 290px;
        }

        .candidates-index-page .candidate-card-name-badge span {
            font-size: .88rem;
            min-height: 34px;
        }

        .candidates-index-page .candidate-card-campaign-badge {
            max-width: calc(100% - 1rem);
            left: .5rem;
            top: .55rem;
            font-size: .7rem;
            min-height: 30px;
        }
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        const professionalView = $('#professionalCandidatesView');
        const tableView = $('#tableCandidatesView');
        const viewToggleBtn = $('#viewModeToggleBtn');
        const searchInput = $('#candidate-search-input');

        function getDataTableInstance() {
            if (!$.fn.DataTable || !$.fn.DataTable.isDataTable('#data-table')) {
                return null;
            }
            return $('#data-table').DataTable();
        }

        function filterProfessionalCards(keyword) {
            const normalized = (keyword || '').toString().trim().toLowerCase();
            const cards = $('.candidate-cards-grid .candidate-card-item');

            if (!normalized) {
                cards.removeClass('d-none');
                return;
            }

            cards.each(function() {
                const card = $(this);
                const nameText = card.find('.candidate-card-name-badge span').text().toLowerCase();
                const electionText = card.find('.candidate-card-campaign-badge span').text().toLowerCase();
                const visible = nameText.includes(normalized) || electionText.includes(normalized);
                card.toggleClass('d-none', !visible);
            });
        }

        function applySearch(keyword) {
            const showProfessional = !professionalView.hasClass('d-none');
            if (showProfessional) {
                filterProfessionalCards(keyword);
                return;
            }

            const tableInstance = getDataTableInstance();
            if (tableInstance) {
                tableInstance.search(keyword || '').draw();
            }
        }

        function activateView(target) {
            const showProfessional = target === 'professional';
            professionalView.toggleClass('d-none', !showProfessional);
            tableView.toggleClass('d-none', showProfessional);

            if (showProfessional) {
                viewToggleBtn
                    .removeClass('btn-outline-primary')
                    .addClass('btn-primary')
                    .html('<i class="fa fa-table me-1"></i>الجدول')
                    .attr('data-current-view', 'professional');
            } else {
                viewToggleBtn
                    .removeClass('btn-primary')
                    .addClass('btn-outline-primary')
                    .html('<i class="fa fa-th-large me-1"></i>العرض الحديث')
                    .attr('data-current-view', 'table');
            }

            if (!showProfessional) {
                setTimeout(function () {
                    const tableInstance = getDataTableInstance();
                    if (tableInstance) {
                        tableInstance.columns.adjust().draw(false);
                    }
                    applySearch(searchInput.val());
                }, 120);
                return;
            }

            applySearch(searchInput.val());
        }

        activateView('professional');

        viewToggleBtn.on('click', function() {
            const currentView = viewToggleBtn.attr('data-current-view') || 'professional';
            activateView(currentView === 'professional' ? 'table' : 'professional');
        });

        searchInput.on('input', function() {
            applySearch($(this).val());
        });

        $('#addFakeCandidate').on('click', function(event) {
            event.preventDefault();
            $('#confirmModal').modal('show');
        });

        $('#confirmButton').click(function() {
            if (!$('#name').val()) {
                errorMessageInModel('اسم المرشح مطلوب');
                return;
            }

            if (!$('#election_id').val()) {
                errorMessageInModel('اختر الانتخاب');
                return;
            }

            if (!$('#image')[0].files[0]) {
                errorMessageInModel('الصورة مطلوبة');
                return;
            }

            const formData = new FormData();
            const imageFile = $('#image')[0].files[0];

            formData.append('name', $('#name').val());
            formData.append('fake', $('#fake').val());
            formData.append('election_id', $('#election_id').val());
            formData.append('image', imageFile);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $('#confirmModal').modal('hide');
            addCandidate(formData);
        });

        function addCandidate(formData) {
            $.ajax({
                url: '/dashboard/store/fake/candidates',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    sucessMessageInModel(data.message);
                    if ($.fn.DataTable && $('#data-table').length) {
                        $('#data-table').DataTable().ajax.reload();
                    }
                },
                error: function() {
                    errorMessageInModel('حدث خطأ');
                }
            });
        }

        function errorMessageInModel(msg) {
            $('#successMessage').text(msg);
            $('#successModal .modal-header').removeClass('bg-success').addClass('bg-danger');
            $('#successModal .fa-check-circle').removeClass('text-success').addClass('text-danger');
            $('#successModal').modal('show');
        }

        function sucessMessageInModel(msg) {
            $('#successMessage').text(msg);
            $('#successModal .modal-header').removeClass('bg-danger').addClass('bg-success');
            $('#successModal .fa-check-circle').removeClass('text-danger').addClass('text-success');
            $('#successModal').modal('show');
        }
    });
</script>
@endpush
