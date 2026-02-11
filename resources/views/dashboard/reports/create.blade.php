@extends('layouts.dashboard.app')

@section('content')
    <style>
        .select2-dropdown{
            width: 205px !important;
        }
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            z-index: 9999; /* Ensure it appears above everything */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 6px solid #ccc; /* Light gray border */
            border-top-color: #000; /* Darker border for animation */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .export-button {
            background-color: #4CAF50; /* Green background */
            color: white; /* White text */
            padding: 10px 20px; /* Padding */
            font-size: 16px; /* Font size */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth hover effect */
        }

        .export-button:hover {
            background-color: #45a049; /* Darker green on hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }

        .export-button:active {
            transform: scale(0.98); /* Slightly shrink on click */
        }
        .attend-btn {
            background-color: #4CAF50; /* Green background */
            color: white; /* White text */
            padding: 10px 20px; /* Padding */
            font-size: 16px; /* Font size */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth hover effect */
        }

        .attend-btn:hover {
            background-color: #45a049; /* Darker green on hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }

        .attend-btn:active {
            transform: scale(0.98); /* Slightly shrink on click */
        }

    </style>
    <div id="loading-overlay" style="display: none;">
        <div class="spinner"></div>
    </div>

    <form action="{{ route('dashboard.reports.store' ) }}" method="POST" class="page-body" id="report">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Report" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.reports.index') }}">Reports</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />


                <div class="card rtl">
                    <div class="card-body">
                        @if(auth()->user()->hasRole('Administrator'))
                        <label class="labelStyle" for="elections">الانتخابات</label>

                        <select name="election" id="elections" class=" filterSelect form-control py-1 w-100 js-example-basic-single">
                            <option value=""> اختر الانتخابات </option>
                            @foreach ($relations['elections'] as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>

                        <label class="labelStyle" for="candidates">المرشحين</label>

                        <select name="candidate" id="candidates" class=" filterSelect form-control py-1 w-100 js-example-basic-single">
                            <option value=""> اختر المرشح </option>
                            @foreach ($relations['candidates'] as $c)
                                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                            @endforeach
                        </select>

                        <br>



                        @else
                            <input type="hidden" value="{{ auth()->user()->id }}" name="candidate">
                            <input type="hidden" value="{{ auth()->user()->election_id }}" name="election">

                        @endif
                        <div>
                            <label class="labelStyle" for="contractors">المتعهدين</label>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" data-div="conDiv" data-select="contractors" checked name="AllCon">
                                <label class="form-check-label" for="flexSwitchCheckDefault">كل المتعهدين</label>
                            </div>
                            <div id="conDiv">
                                <select name="contractor" id="contractors" class="filterSelect form-control py-1 w-100 js-example-basic-single">
                                    <option value=""> اختر المتعهد </option>
                                    @foreach ($relations['contractors'] as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="card rtl">
                    <div class="card-body">
                    <h3>
                        تفاصيل الناخبين
                    </h3>
                        <br>
                        <div class="d-flex flex-row flex-wrap">
                            <!-- First Column -->
                            <div class="d-flex flex-column mx-4">
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        checked
                                        disabled
                                        type="checkbox"
                                        name="columns[]"
                                        value="name"
                                        id="na5ebName"
                                    >
                                    <label class="labelStyle ms-2" for="na5ebName">اسم الناخب</label>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        checked
                                        type="checkbox"
                                        name="columns[]"
                                        value="family"
                                        id="na5ebFamilyName"
                                    >
                                    <label class="labelStyle ms-2" for="na5ebFamilyName">العائلة</label>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        type="checkbox"
                                        name="columns[]"
                                        value="phone"
                                        id="na5ebPhone"
                                    >
                                    <label class="labelStyle ms-2" for="na5ebPhone">الهاتف</label>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        type="checkbox"
                                        name="columns[]"
                                        value="type"
                                        id="na5ebType"
                                    >
                                    <label class="labelStyle ms-2" for="na5ebType">الجنس</label>
                                </div>
                            </div>

                            <!-- Second Column -->
                            <div class="d-flex flex-column mx-4">
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        type="checkbox"
                                        name="columns[]"
                                        value="madrasa"
                                        id="na5ebSchool"
                                    >
                                    <label class="labelStyle ms-2" for="na5ebSchool">مدرسة الانتخاب</label>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        checked
                                        type="checkbox"
                                        name="columns[]"
                                        value="restricted"
                                        id="na5ebRegisterStatus"
                                    >
                                    <label class="labelStyle ms-2" for="na5ebRegisterStatus">حالة القيد</label>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        type="checkbox"
                                        name="columns[]"
                                        value="created_at"
                                        id="na5ebRegisterDate"
                                    >
                                    <label class="labelStyle ms-2" for="na5ebRegisterDate">تاريخ القيد</label>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        type="checkbox"
                                        name="columns[]"
                                        value="checked_time"
                                        id="na5ebCheckedTime"
                                    >
                                    <label class="labelStyle ms-2" for="na5ebCheckedTime">وقت التصويت</label>
                                </div>
                            </div>

                            <div class="d-flex flex-column mx-4">
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        type="checkbox"
                                        name="columns[]"
                                        value="contractor"
                                        id="contractor"
                                    >
                                    <label class="labelStyle ms-2" for="contractor">تفاصيل المتعهد</label>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <input
                                        type="checkbox"
                                        name="columns[]"
                                        value="alrkm_almd_yn"
                                        id="alrkm_almd_yn"
                                    >
                                    <label class="labelStyle ms-2" for="alrkm_almd_yn">الرقم المدني</label>
                                </div>
                            </div>

                        </div>



                    </div>
                </div>

                <div class="card rtl">
                    <div class="card-body">
                        <h3>
                            تفاصيل الحضور
                        </h3>
                        <br>
                        <div class="col-4 pe-0 mb-3">
                            <div class="d-flex align-items-center">
                                <label class="labelStyle mb-0" for="statusSearch">الحالة</label>
                                <select
                                    name="status"
                                    id="statusSearch"
                                    class="form-control py-1"
                                >
                                    <option value="" selected>الكل</option>
                                    <option value="1">حضر الانتخابات</option>
                                    <option value="0">لم يحضر الانتخابات</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4 pe-0 mb-3">
                            <div class="d-flex align-items-center">
                                <label class="labelStyle mb-0" for="voterType">النوع</label>
                                <select name="voterType" id="voterType" class="form-control py-1">
                                    <option value="" selected>الكل</option>
                                    <option value="{{ App\Enums\Type::MALE->value }}">{{ App\Enums\Type::MALE->value }}</option>
                                    <option value="{{ App\Enums\Type::FEMALE->value }}">{{ App\Enums\Type::FEMALE->value }}</option>
                                </select>
                                
                            </div>
                        </div>


                        <div id="attendances">
                            <label class="labelStyle" for="committees">اللجان</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" data-div="comDiv" data-select="committees" checked name="AllCom">
                                <label class="form-check-label" for="flexSwitchCheckDefault">كل اللجان</label>
                            </div>
                            <div id="comDiv">
                                <select name="committee" id="committees" class=" filterSelect form-control py-1 w-100 js-example-basic-single">
                                    <option value=""> اختر الجنه </option>
                                    @foreach ($relations['committees'] as $c)
                                        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br>

                            <div class="d-flex flex-row flex-wrap mt-3">
                                <!-- First Column -->
                                <div class="d-flex flex-column mx-4">
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            type="checkbox"
                                            name="columns[attendCom][]"
                                            value="date"
                                            id="date"
                                        >
                                        <label class="labelStyle ms-2" for="date">وقت التصويت</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <input
                                            type="checkbox"
                                            name="columns[attendCom][]"
                                            value="type"
                                            id="attended"
                                        >
                                        <label class="labelStyle ms-2" for="attended">حضر بواسطه</label>
                                    </div>
                                </div>

                            </div>


                        </div>


                        <input type="hidden" id="type" name="type" value="">
                        <button class="export-button mt-2" value="PDF">تصدير  PDF</button>
                        <button class="export-button mt-2" value="Excel">تصدير Excel</button>
                    </div>
                </div>





            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>

    <form action="{{ route('attend-report') }}" class="rtl mb-5">

        <button class="attend-btn mt-2" value="Excel">استخراج الحضور الكلي  Excel</button>

    </form>
@endsection

@push('js')
    <script>
            $(document).ready(function () {
                status($('#statusSearch'))
                $('#statusSearch').on('change',function(){
                    status($(this))
                })
                $('.form-check-input').each(function () {
                    // Inside this function, `this` refers to the current DOM element
                    checking($(this)); // Call your `checking` function with the current element
                });
                $('.form-check-input').on('change', function () {
                   checking($(this))
                });
                $('#elections').on('change',function (){
                    $('#candidates').val('')
                    $('#contractors').val('')
                    $('#committees').val('')
                })
                function checking(check){
                    if (check.is(':checked')) {
                        console.log("check")
                        $('#'+check.data('div')).addClass('d-none'); // Show the element
                        $('#'+check.data('select')).val("")
                    } else {
                        console.log("uncheck")
                        $('#'+check.data('div')).removeClass('d-none'); // Hide the element
                    }
                }
                function status(selector){
                    const value =selector.val();
                    console.log(value)
                        if(value =='0' ){
                            $('#attendances').addClass('d-none')
                        }else {
                            $('#attendances').removeClass('d-none')

                        }
                }

                function fetchFilteredOptions() {
                    const data = {
                        election: $('#elections').val(),
                        candidate: $('#candidates').val(),
                        contractor: $('#contractors').val(),
                        committee: $('#committees').val(),
                       };

                    // Send AJAX request to get filtered options
                    $.ajax({
                        url: '{{ route('report.selections') }}', // Adjust route name if needed
                        type: 'GET',
                        data: data,
                        success: function (response) {
                            console.log(response)
                            updateSelectOptions('#candidates', response.candidate);
                            updateSelectOptions('#contractors', response.contractor);
                            updateSelectOptions('#committees', response.committee);},

                        error: function (error) {
                            console.error('Error fetching filtered options:', error);
                        },
                    });
                }

                function updateSelectOptions(selector, options) {
                    const select = $(selector);

                    // Check if the select already has a selected value
                    if (select.val() !== '' ) {
                        console.log(selector,select.val())
                        return;
                    }

                    console.log(select)
                    // Clear and repopulate the dropdown if no value is selected

                    select.empty().append('<option value=""> -- </option>'); // Add default empty option

                        for (const [id, value] of Object.entries(options)) {
                            select.append(`<option value="${id}">${value}</option>`);
                        }

                }
                $('.filterSelect').on('change', fetchFilteredOptions);
            });

    </script>
    <script>
    $(document).ready(function () {
    $('.export-button').on('click', function (e) {
        e.preventDefault(); // Prevent the default form submission

        $("#type").val($(this).val()); // Set the type dynamically (PDF/Excel)
        const form = $(this).closest('form');
        const formData = new FormData(form[0]); // Gather form data

        const fileType = $("#type").val(); // Get the selected file type (PDF or Excel)
        const fileName = fileType === "PDF" ? "Voters.pdf" : "Voters.xlsx";

        axios({
            method: 'post',
            url: form.attr('action'), // Use the form's action attribute as the URL
            data: formData,
            responseType: 'blob', // Handle binary data
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': fileType === "PDF" ? "application/pdf" : "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // Correct MIME type
            }
        })
            .then(response => {
                // Hide the overlay after the file is downloaded
                document.getElementById('loading-overlay').style.display = 'none';

                // Handle file download
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName); // Set the correct file name
                document.body.appendChild(link);
                link.click();
                link.remove();
            })
            .catch(error => {
                // Hide the overlay in case of an error
                document.getElementById('loading-overlay').style.display = 'none';
                console.error('Error:', error);
                alert('An error occurred while generating the report.');
            });

        // Show the overlay before starting the request
        document.getElementById('loading-overlay').style.display = 'flex';
    });

    function saveFilePath(filePath) {
        axios({
            method: 'post',
            url: '/save-file-path', // Replace with your backend endpoint for saving file paths
            data: { file_path: filePath },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            }
        })
            .then(response => {
                console.log('File path saved successfully:', response.data);
            })
            .catch(error => {
                console.error('Error saving file path:', error);
                alert('An error occurred while saving the file path.');
            });
    }
});


    </script>
@endpush
