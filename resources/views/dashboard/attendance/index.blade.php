@extends('layouts.dashboard.app')

@section('content')

<style>
    #load-more {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 50px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    #load-more:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    #load-more:active {
        transform: scale(0.95);
    }

    #load-more.loading::after {
        content: '';
        width: 20px;
        height: 20px;
        border: 3px solid white;
        border-top: 3px solid transparent;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        animation: spinner 0.8s linear infinite;
    }
    

    @keyframes spinner {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
     /* Add new loading spinner styles */
    .loading-container {
        display: none;
        text-align: center;
        padding: 2rem;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-text {
        margin-top: 1rem;
        font-size: 1.1rem;
        color: #666;
    }

</style>

<meta name="csrf-token" content="{{ csrf_token() }}">

<section class=" statistics rtl">
    <div class="container mt-5">
        <!-- ================================================================================================================= -->
        @if (auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('مرشح') || auth()->user()->hasRole('مندوب'))
            <label class="bg-warning py-2 bg-opacity-75 px-3"  for="committee">اللجنة</label>
            <select name="committee" id="committee" class="form-control">
                <option value="" selected>أختر اللجنة</option>
                @foreach ($committees as $i=>$com )
                    <option value="{{$com->id}}"> {{$i+1}}-- {{$com->name}} ({{$com->type}})</option>
                @endforeach
            </select>
        @endif
        <!-- ================================================================================================================= -->
        <div class="rtl text-center d-flex justify-content-center flex-wrap my-2" >
            <div style="display:none" id="counts">
                <br>
                <span class="bg-dark text-white px-4 py-2 rounded-end-3">الناخبين</span>
                <span class="bg-secondary bg-opacity-50 fw-semibold px-3 py-2 rounded-start-3 me-3" id="voter_count">0</span>
                <span class="bg-dark text-white px-4 py-2 rounded-end-3">الحضور</span>
                <span class="bg-secondary bg-opacity-50 fw-semibold px-3 py-2 rounded-start-3 me-3" id="attend_count"> 0</span>
            </div>
        </div>
        <!-- ================================================================================================================= -->
        <div style="display:none" id="search">
            <br><input type="text" id="searchBox"  class="form-control py-1 mb-3" placeholder=" البحث عن ناخبين" value="">
        </div>      
        <!-- ================================================================================================================= -->
        <div class="table-responsive mt-4" style="display:none" id="resualt">
            <table class="table  font14 table-striped rtl overflow-hidden" id="voter_table">
                <thead class="table-dark">
                    <tr>
                        <th>القيد</th>
                        <th>الاسم</th>
                        <th>الصندوق</th>
                        <th>الحاله</th>
                        <th>تغير الحاله</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider" id="voter-list">
                    <!-- Table data will be loaded here dynamically -->
                    <!-- ================================================================================================================= -->
                    <tr><td colspan="5" class="text-center">
                    <!-- Add loading container after the committee select -->
                    <div class="loading-container" id="loading-indicator">
                        <div class="loading-spinner"></div>
                        <div class="loading-text">جاري تحميل البيانات...</div>
                    </div>
                    </td></tr>
                    <!-- ================================================================================================================= -->
                    
                </tbody>
            </table>
        
            <div class="text-center">
        
            </div>
        </div>
        <!-- ================================================================================================================= -->
        <!-- Add this HTML code right after your table section -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="confirmModalLabel" style="color:black">تأكيد العملية</h5>
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal" aria-label="Close"> X </button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-question-circle text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <h5 id="confirmMessage" class="mb-3"></h5>
                        <input type="hidden" id="voterIdInput">
                        <input type="hidden" id="statusInput">
                    </div>
                    <div class="modal-footer justify-content-center border-top-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                        <button type="button" class="btn btn-success px-4" id="confirmButton">
                            <i class="fas fa-check"></i> تأكيد
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- ================================================================================================================= -->
        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">نجاح العملية</h5>

                        <button type="button" class="btn-secondary btn-close-white" data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h5 id="successMessage"></h5>
                    </div>
                    <div class="modal-footer justify-content-center border-top-0">
                        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">موافق</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- ================================================================================================================= -->
    </div>
</section>

@endsection
@push('js')
<script>
    $(document).ready(function() {        
        //=========================================================================================================
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //=========================================================================================================
        $('#committee').change(function() {
            //loadVoters($(this).val());
            //show search input
            $('#search').show();
            fetchVoterCounts($(this).val());
        });
        //=========================================================================================================
        function fetchVoterCounts(committee){
            if (committee) {
                $.ajax({
                    url: '/get_attending_counts/' + committee,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        console.log(data)
                        $('#counts').show();
                        document.getElementById('voter_count').innerText = data.voter_count;
                        document.getElementById('attend_count').innerText = data.attend_count;
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr);
                        // Show error in success modal
                        errorMessageInModel('حدث خطأ');
                    }
                });
            }
        }
        //=========================================================================================================
        let timer;
        $('#searchBox').on('keyup', function() {
            // searchTable();
            clearTimeout(timer);
            
            timer = setTimeout(() => {
                const searchValue = $(this).val();
                //alert(searchValue);
                var committee = document.getElementById('committee').value;
                loadVoters(committee,searchValue)
            }, 500); // Wait 500ms after user stops typing
        });
        //=========================================================================================================
        function initializeButtons() {
            // Remove existing event listeners first
            $('.approve-btn').off('click').click(function() {  
                // alert('hi-accept');
                const voterId = $(this).data('id');
                // showAlert('هل انت متاكد من حضور الناخب؟',1,voterId);
                showConfirmModal('هل انت متاكد من حضور الناخب؟',1,voterId);
                // if(confirm('هل انت متاكد من حضور الناخب؟')) {
                //     // Action on confirm
                //     updateStatus(1,voterId);
                // }
            });

            $('.reject-btn').off('click').click(function() {            
                // alert('hi-reject');
                const voterId = $(this).data('id');
                // showAlert('هل انت متاكد من عدم حضور الناخب؟',0,voterId);
                showConfirmModal('هل انت متاكد من عدم حضور الناخب؟',0,voterId);
                // if(confirm('هل انت متاكد من عدم حضور الناخب؟')) {
                //     // Action on confirm
                //     updateStatus(0,voterId);
                // }
            });
        } 
        //=========================================================================================================
        // function showAlert(msg,status,voterId){
        //     Swal.fire({
        //             title: 'تاكيد ؟',
        //             text: msg,
        //             icon: 'warning',
        //             showCancelButton: true,
        //             confirmButtonColor: '#28a745', // Green color for the confirm button
        //             cancelButtonColor: '#d33', // Red color for the cancel button
        //             confirmButtonText: '<i class="fa fa-check"></i> نعم، متابعة', // Adding a checkmark icon
        //             cancelButtonText: '<i class="fa fa-times"></i> إلغاء', // Adding an 'x' icon for cancel
        //         }).then((result) => {
        //             if (result.isConfirmed) {
        //                 updateStatus(status,voterId);
        //             }
        //         });
        // }
        //=========================================================================================================
        function updateStatus(status,voterId){
            $('#resualt').show();
            $('#loading-indicator').show();
            
            $.ajax({
                url: '{{ route('dashboard.voters.change-status',0 ) }}', // Adjust route name if needed
                type: 'POST',
                data: JSON.stringify({
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: status,
				    voterId:voterId,
                    committee: $('#committee').val()
                }),
                contentType: 'application/json',
                success: function(response){
                    $('#resualt').hide();
                    $('#loading-indicator').hide();
                    // console.log(response);
                    if(response.success) {
                        // Show success modal
                        $('#successMessage').text(response.message);
                        $('#successModal').modal('show');
                        fetchVoterCounts($('#committee').val());
                        // Swal.fire('', response.message, 'success');
                        // loadVoters($('#committee').val());
                        // $('#voter_status_'+voterId).text(status ? 'حضر' : " لم يحضر");
                        // $('#voter_buttons_'+voterId).html(tableButtons(voterId,status));
                        // initializeButtons();

                        document.getElementById('searchBox').value = '';
                        // searchTable();
                        // $('#resualt').hide();
                    } else {
                        // Swal.fire('Error!', response.message, 'error');
                        
                        // Show error in success modal with error styling
                        errorMessageInModel('حدث خطأ في العملية');
                    }
                },
                error: function() {
                    // Swal.fire('Error!', 'حدث خطا !! ', 'خطا');
                    
                    // Show error in success modal
                    errorMessageInModel('حدث خطأ في العملية');
                }
            });
        }
        //=========================================================================================================
        function loadVoters(committee,searchValue = '') {
            // alert(committee);
            if (committee) {
                // Show loading indicator
                $('#resualt').show();                
                $('#loading-indicator').show();                
                $.ajax({
                    url: '/voters/' + committee,
                    type: "GET",
                    dataType: 'json',
                    // data: JSON.stringify({
                    data: {
                        searchValue: searchValue,
                    },
                    success: function(data) {
                        console.log(data)
                        //here we will return all voters
                        var tbody = '';
                        var loop  = 0;
                        var voters = data.voters
                        document.getElementById('resualt').style.display = 'block'; 
                        if(voters.length == 0){
                            tbody = '<tr><td colspan="5" class="text-center">لا يوجد نتائج</td></tr>';
                        }else{
                            document.getElementById('searchBox').val = '';
                        }
                        voters.forEach(function(voter) {
                                tbody += `<tr>
                                    <td>${++loop}</td>
                                    <td>${voter.name}</td>
                                    <td>${voter.alsndok}</td>
                                    <td id="voter_status_${voter.id}">${voter.status ? 'حضر' : " لم يحضر"}</td>
                                    <td id="voter_buttons_${voter.id}">`
                                    +
                                    tableButtons(voter.id,voter.status)
                                    + 
                                    `</td>
                                </tr>`;
                            });
                        $('#voter-list').html(tbody);
                        initializeButtons();
                        
                        // Hide loading indicator and show results
                        $('#loading-indicator').hide();
                        $('#resualt').show();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr);
                        // Hide loading indicator on error
                        $('#loading-indicator').hide();
                        document.getElementById('resualt').style.display = 'none';
                        // console.error('Error:', error);
                        // Swal.fire('Error!', 'Error fetching voters data', 'error');
                        // Show error in success modal
                        errorMessageInModel('حدث خطأ');
                    }
                    
                });
            } else {
            }
        }
        //=========================================================================================================
        function tableButtons(voterId,status){
            var buttons = `<button class="btn btn-success btn-sm approve-btn" 
                data-id="${voterId}" 
                ${status == 1 ? 'disabled' : ''}>
                حضر
            </button>
            <button class="btn btn-primary  btn-sm reject-btn" 
                data-id="${voterId}"
                ${status == 0 ? 'disabled' : ''}>
                لم يحضر
            </button>`;
            return buttons;
        }
        //=========================================================================================================
        // function searchTable(){
        //     // let entireValue = $("#searchBox").val().toLowerCase();
        //     $("#voter_table tbody tr").each(function () {
        //         let rowText = $(this).text().toLowerCase();
        //         // Show all rows if entire search is empty
        //         let matchEntire = entireValue === "";
        //         if (entireValue !== "") {
        //             matchEntire = rowText.indexOf(entireValue) > -1;
        //         }
        //         $(this).toggle(matchEntire);
        //     });
        // }
        //=========================================================================================================
        function showConfirmModal(message, status, voterId) {
            $('#confirmMessage').text(message);
            $('#voterIdInput').val(voterId);
            $('#statusInput').val(status);
            $('#confirmModal').modal('show');
        }
    
        // Handle confirm button click
        $('#confirmButton').click(function() {
            const status = $('#statusInput').val();
            const voterId = $('#voterIdInput').val();
            $('#confirmModal').modal('hide');
            updateStatus(status, voterId);
        });
    
        // Handle cancel button and modal close
        $('#confirmModal .btn-close, #confirmModal .btn-secondary').click(function() {
            $('#confirmModal').modal('hide');
        });
        
        // Handle success modal close
        $('#successModal').on('hidden.bs.modal', function () {
            // Reset the success modal styling when it's closed
            $('#successModal .modal-header').removeClass('bg-danger').addClass('bg-success');
            $('#successModal .fa-check-circle').removeClass('text-danger').addClass('text-success');
        });
        
        function errorMessageInModel(msg){
            $('#successMessage').text(msg);
            $('#successModal .modal-header').removeClass('bg-success').addClass('bg-danger');
            $('#successModal .fa-check-circle').removeClass('text-success').addClass('text-danger');
            $('#successModal').modal('show');
        }
        //=========================================================================================================

    });
</script>
@endpush