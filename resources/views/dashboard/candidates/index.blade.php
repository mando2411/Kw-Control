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
                    <div class="card-body order-datatable overflow-x-auto">
                        <div class="">
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
@push('js')
<script>
    $(document).ready(function() {
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