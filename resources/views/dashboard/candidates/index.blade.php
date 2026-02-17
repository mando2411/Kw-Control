@extends('layouts.dashboard.app')

@section('content')
<div class="page-body">
    <!-- Container-fluid starts-->
    <x-dashboard.partials.breadcrumb title="Candidates">
        <li class="breadcrumb-item active">Candidates</li>
    </x-dashboard.partials.breadcrumb>
    <!-- Container-fluid Ends-->

    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <x-dashboard.partials.message-alert />
                <div class="card candidate-page-shell">
                    <x-dashboard.partials.table-card-header model="candidate" />
                    <div class="card-body">
                        <div class="candidate-page-head mb-3">
                            <div class="candidate-page-head__title-wrap">
                                <h4 class="candidate-page-head__title mb-1">لوحة إدارة المرشحين</h4>
                                <p class="candidate-page-head__subtitle mb-0">عرض احترافي بالبطاقات أو عرض تقليدي بالجدول حسب أسلوب إدارتك.</p>
                            </div>
                            <div class="candidate-page-head__stats">
                                <div class="candidate-stat-pill">
                                    <span class="label">إجمالي المرشحين</span>
                                    <strong>{{ $candidates->count() }}</strong>
                                </div>
                                <div class="candidate-stat-pill">
                                    <span class="label">الانتخابات المرتبطة</span>
                                    <strong>{{ $candidates->pluck('election_id')->filter()->unique()->count() }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="candidate-view-switch mb-3" role="tablist" aria-label="تبديل طريقة العرض">
                            <button type="button" class="btn btn-primary active" data-view-target="professional" id="showProfessionalView" role="tab" aria-selected="true">
                                <i class="fa fa-th-large me-1"></i>
                                العرض الاحترافي
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-view-target="table" id="showTableView" role="tab" aria-selected="false">
                                <i class="fa fa-table me-1"></i>
                                عرض الجدول
                            </button>
                        </div>

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
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
    <!-- ================================================================================================================= -->
    <!-- this form for fake candidate button -->
    @include('dashboard.candidates.fake_candidate_pop_up')
    <!-- ================================================================================================================= -->
</div>
@endsection

@push('css')
<style>
    .candidate-page-shell {
        border: 1px solid rgba(148, 163, 184, .24);
        box-shadow: 0 14px 32px rgba(15, 23, 42, .08);
        border-radius: 16px;
        overflow: hidden;
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.98));
    }

    .candidate-page-head {
        border: 1px solid rgba(148, 163, 184, .25);
        background: linear-gradient(120deg, rgba(99,102,241,.10), rgba(14,165,233,.10), rgba(255,255,255,.96));
        border-radius: 14px;
        padding: .95rem 1rem;
        display: flex;
        gap: .75rem;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .candidate-page-head__title {
        font-size: 1.05rem;
        font-weight: 900;
        color: #0f172a;
    }

    .candidate-page-head__subtitle {
        font-size: .86rem;
        color: #475569;
        font-weight: 600;
    }

    .candidate-page-head__stats {
        display: flex;
        gap: .55rem;
        flex-wrap: wrap;
    }

    .candidate-stat-pill {
        background: #fff;
        border: 1px solid rgba(148, 163, 184, .32);
        border-radius: 999px;
        padding: .38rem .8rem;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        box-shadow: 0 6px 14px rgba(15, 23, 42, .06);
    }

    .candidate-stat-pill .label {
        font-size: .76rem;
        color: #475569;
        font-weight: 700;
    }

    .candidate-stat-pill strong {
        font-size: .9rem;
        color: #0f172a;
        font-weight: 900;
    }

    .candidate-view-switch {
        display: flex;
        gap: .55rem;
        flex-wrap: wrap;
        background: #eef2ff;
        border: 1px solid rgba(99, 102, 241, .22);
        border-radius: 999px;
        padding: .28rem;
        width: fit-content;
    }

    .candidate-view-switch .btn {
        border-radius: 999px;
        border-width: 1px;
        min-height: 38px;
        font-weight: 800;
        font-size: .84rem;
    }

    .candidate-section-caption {
        color: #475569;
        font-size: .84rem;
        font-weight: 700;
    }

    .candidate-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem;
    }

    .candidate-card-item {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, .3);
        background: #fff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .09);
        transition: transform .24s ease, box-shadow .24s ease;
    }

    .candidate-card-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 28px rgba(15, 23, 42, .14);
    }

    .candidate-card-image {
        position: relative;
        min-height: 320px;
        background-size: cover;
        background-position: center;
        isolation: isolate;
    }

    .candidate-card-overlay {
        position: absolute;
        right: 0;
        left: 0;
        bottom: 0;
        height: 60%;
        padding: .9rem;
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

    .candidate-card-item:hover .candidate-card-overlay {
        transform: translateY(0);
    }

    .candidate-card-name {
        margin: 0;
        font-weight: 800;
        font-size: 1rem;
    }

    .candidate-card-meta {
        display: flex;
        flex-direction: column;
        gap: .25rem;
        font-size: .82rem;
        opacity: .98;
    }

    #tableCandidatesView {
        border: 1px solid rgba(148, 163, 184, .24);
        border-radius: 12px;
        padding: .75rem;
        background: #fff;
    }

    #tableCandidatesView .dataTables_wrapper .dt-buttons .btn {
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 700;
        padding: .32rem .58rem;
    }

    .candidate-card-actions {
        display: flex;
        gap: .45rem;
        flex-wrap: wrap;
        margin-top: .2rem;
    }

    .candidate-card-actions .btn {
        font-size: .78rem;
        font-weight: 700;
    }

    @media (max-width: 576px) {
        .candidate-page-head {
            border-radius: 12px;
            padding: .85rem;
        }

        .candidate-page-head__title {
            font-size: .98rem;
        }

        .candidate-view-switch {
            width: 100%;
            justify-content: center;
        }

        .candidate-view-switch .btn {
            flex: 1 1 calc(50% - .35rem);
        }

        .candidate-card-image {
            min-height: 280px;
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

            localStorage.setItem('candidates_view_mode', target);
        }

        const savedView = localStorage.getItem('candidates_view_mode');
        activateView(savedView === 'table' ? 'table' : 'professional');

        switchButtons.on('click', function() {
            activateView($(this).data('view-target'));
        });

        //========================================================================
        $('#addFakeCandidate').on('click', function(event) {
            event.preventDefault(); // Prevent the default button action
            $('#confirmModal').modal('show');
        });
        //========================================================================
        // Handle confirm button click
        $('#confirmButton').click(function() {
            // Validate required fields
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
        //========================================================================
        function addCandidate(formData) {
            $.ajax({
                url: '/dashboard/store/fake/candidates',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    // console.log(data);
                    sucessMessageInModel(data.message);
                    $('#data-table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    // console.log(xhr);
                    // Show error in success modal
                    errorMessageInModel('حدث خطأ');
                }
            });
        }
        //========================================================================
        function errorMessageInModel(msg) {
            $('#successMessage').text(msg);
            $('#successModal .modal-header').removeClass('bg-success').addClass('bg-danger');
            $('#successModal .fa-check-circle').removeClass('text-success').addClass('text-danger');
            $('#successModal').modal('show');
        }
        //========================================================================
        function sucessMessageInModel(msg) {
            $('#successMessage').text(msg);
            $('#successModal .modal-header').removeClass('bg-danger').addClass('bg-success');
            $('#successModal .fa-check-circle').removeClass('text-danger').addClass('text-success');
            $('#successModal').modal('show');
        }
        //========================================================================
    });
</script>
@endpush