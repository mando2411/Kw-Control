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
                    <input id="datatable-search" aria-label="Search" class="form-control" type="search" placeholder="بحث باسم المرشح...">
                </div>
            </div>

            <div class="candidate-toolbar__right">
                <div class="candidate-view-switch" role="tablist" aria-label="تبديل طريقة العرض">
                    <button type="button" class="btn btn-primary active" data-view-target="professional" id="showProfessionalView" role="tab" aria-selected="true">
                        <i class="fa fa-th-large me-1"></i>
                        العرض الاحترافي
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-view-target="table" id="showTableView" role="tab" aria-selected="false">
                        <i class="fa fa-table me-1"></i>
                        عرض الجدول
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
                            @endphp

                            <article class="candidate-card-item">
                                <div class="candidate-card-image" style="background-image: url('{{ $image }}')">
                                    <div class="candidate-card-overlay">
                                        <h5 class="candidate-card-name">{{ $candidate->user?->name ?? '—' }}</h5>

                                        <div class="candidate-card-meta">
                                            <span><i class="fa fa-flag me-1"></i>{{ $candidate->election?->name ?? 'انتخابات غير محددة' }}</span>
                                            <span><i class="fa fa-users me-1"></i>متعهدين: {{ $candidate->max_contractor }}</span>
                                            <span><i class="fa fa-user me-1"></i>مناديب: {{ $candidate->max_represent }}</span>
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
        background: #fff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .09);
        transition: transform .24s ease, box-shadow .24s ease;
    }

    .candidates-index-page .candidate-card-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 28px rgba(15, 23, 42, .14);
    }

    .candidates-index-page .candidate-card-image {
        position: relative;
        min-height: 330px;
        background-size: cover;
        background-position: center;
        isolation: isolate;
    }

    .candidates-index-page .candidate-card-overlay {
        position: absolute;
        right: 0;
        left: 0;
        bottom: 0;
        height: 60%;
        padding: .95rem;
        color: #fff;
        background: linear-gradient(to top, rgba(2, 6, 23, .96), rgba(2, 6, 23, .82) 58%, rgba(2, 6, 23, .25));
        transform: translateY(66%);
        opacity: .96;
        transition: transform .28s ease;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: .45rem;
        backdrop-filter: blur(2px);
    }

    .candidates-index-page .candidate-card-item:hover .candidate-card-overlay {
        transform: translateY(0);
    }

    .candidates-index-page .candidate-card-name {
        margin: 0;
        font-weight: 800;
        font-size: 1rem;
    }

    .candidates-index-page .candidate-card-meta {
        display: flex;
        flex-direction: column;
        gap: .25rem;
        font-size: .82rem;
        opacity: .98;
    }

    .candidates-index-page .candidate-card-actions {
        display: flex;
        gap: .45rem;
        flex-wrap: wrap;
        margin-top: .2rem;
    }

    .candidates-index-page .candidate-card-actions .btn {
        font-size: .78rem;
        font-weight: 700;
        min-width: 92px;
        border-radius: 9px;
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
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        const professionalView = $('#professionalCandidatesView');
        const tableView = $('#tableCandidatesView');
        const switchButtons = $('.candidate-view-switch button');

        function activateView(target) {
            const showProfessional = target === 'professional';
            professionalView.toggleClass('d-none', !showProfessional);
            tableView.toggleClass('d-none', showProfessional);

            switchButtons.removeClass('active btn-primary').addClass('btn-outline-primary');
            switchButtons.filter('[data-view-target="' + target + '"]')
                .addClass('active btn-primary')
                .removeClass('btn-outline-primary')
                .attr('aria-selected', 'true');

            switchButtons.not('[data-view-target="' + target + '"]').attr('aria-selected', 'false');

            if (!showProfessional) {
                setTimeout(function () {
                    if ($.fn.DataTable && $('#data-table').length) {
                        $('#data-table').DataTable().columns.adjust().draw(false);
                    }
                }, 120);
            }
        }

        activateView('professional');

        switchButtons.on('click', function() {
            activateView($(this).data('view-target'));
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
