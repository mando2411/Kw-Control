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
                <div class="card">
                    <x-dashboard.partials.table-card-header model="candidate" />
                    <div class="card-body">
                        <div class="candidate-view-switch mb-3">
                            <button type="button" class="btn btn-primary active" data-view-target="professional" id="showProfessionalView">
                                <i class="fa fa-th-large me-1"></i>
                                العرض الاحترافي
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-view-target="table" id="showTableView">
                                <i class="fa fa-table me-1"></i>
                                عرض الجدول
                            </button>
                        </div>

                        <div id="professionalCandidatesView" class="candidate-cards-grid-view">
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
    .candidate-view-switch {
        display: flex;
        gap: .55rem;
        flex-wrap: wrap;
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
                .removeClass('btn-outline-primary');

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