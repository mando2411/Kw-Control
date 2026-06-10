@extends('layouts.dashboard.app')

@section('content')

<!-- Container-fluid starts-->
<x-dashboard.partials.breadcrumb title="Import Voters For Contractor" :hideFirst="true">
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard.voters.index') }}">Voters</a>
    </li>
</x-dashboard.partials.breadcrumb>
<!-- Container-fluid Ends-->



<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <x-dashboard.partials.message-alert />
        <div class="card">
            <div class="card-body">
                <div class="form-group text-end mb-4">
                    <a class="btn btn-success" href="{{ asset('assets/admin/sheets/voters.xlsx') }}" download> تنزيل نموذج للرفع </a>
                </div>
                
                @can('import.contractor.voters')
                    <div class="container">
                        <div class="form-container">
                            <x-dashboard.partials.message-alert />
    
                            <form id="fileUploadForm" action="{{ route('dashboard.import-contractor-voters') }}" class="row" enctype="multipart/form-data" method="POST">
                                @csrf
                                
                                <div class="col-12 mb-3">
                                    <div class="card border-danger text-center fw-bold">
                                    <span style="color:red; font-weight: bold;padding: 1%;">يُرجى التاكد من اضافه عمود للمتعهد الفرعى فى النموذج الخاص باضافه ناخبين  فى حاله عدم اختيار متعهد فرعى</span>                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="candidate" class="form-label"> المرشح</label>
                                    <select class="form-select" name="candidate" id="candidate" required>
                                        <option value="">اختر المرشح</option>
                                        @foreach ($candidates as $candidate )
                                        <option value="{{$candidate->user_id}}"> {{$candidate->user->name . "(".$candidate->user_id .")" }} </option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="main_contractor" class="form-label"> المتعهد رئيسى</label>
                                    <select class="form-select" name="main_contractor" id="main_contractor" required>
                                        <option value="">اختر المتعهد</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="sub_contractor" class="form-label"> المتعهد الفرعى</label>
                                    <select class="form-select" name="sub_contractor" id="sub_contractor">
                                        <option value="0">اختر المتعهد</option>
                                    </select>
                                </div>
    
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="import" class="form-label">ادراج ملف</label>
                                    <input type="file" class="form-control" id="import" name="import" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                </div>
    
                                <div class="col-12 mb-3">
                                    <span id="progress_percentage" class="d-block mb-2">0%</span>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="col-12 text-center mb-4">
                                    <button type="submit" class="btn btn-success px-4">ادراج</button>
                                </div>
                            </form>
    
                            <center>
                                <br>
                                <div class="card border-success mb-6" style="max-width: 45rem;display:none" id="result" >
                                    <div class="card-body text-success text-start">
                                        <h5 class="card-title" style="color:red"> <span id="msg"></span></h5>
                                        <hr>
                                        <h5 class="card-title">عدد الصفوف المجهزة: <span id="total_rows">0</span></h5>
                                        <h5 class="card-title">عدد الناخبين اللذين تمت اضافتهم: <span id="success_count">0</span></h5>
                                        <h5 class="card-title" style="color:red">عدد الناخبين اللذين فشل اضافتهم: <span id="failed_count">0</span></h5>
                                        <h5 class="card-title" style="color:orange">عدد الناخبين المُكررين :<span id="repeat_count">0</span></h5>
                                        <div id="failed_rows_summary" class="text-danger"></div>
                                        <div id="failed_rows_details" class="mt-3" style="display:none; text-align:right;"></div>
                                        <button id="download_failed_rows" type="button" class="btn btn-warning mt-3" style="display:none;">تحميل ملف تفاصيل الفشل</button>
                                    </div>
                                </div>
                            </center>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->

@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
<script>
    $(document).ready(function() {
        document.getElementById('result').style.display     = 'none';
        document.getElementById('success_count').innerHTML  = 0;
        document.getElementById('failed_count').innerHTML   = 0;
        document.getElementById('repeat_count').innerHTML   = 0;
        //=========================================================================================================
        $('#candidate').on('change', function() {
            var candidate = $(this).val();
            // alert(candidate);
            if (candidate) {
                $.ajax({
                    url: '/user/contractors/' + candidate,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        // console.log(data)
                        $('#main_contractor').empty();
                        $('#sub_contractor').empty();
                        $('#main_contractor').append('<option value="">اختر المتعهد</option>');
                        $('#sub_contractor').append('<option value="0">اختر المتعهد</option>');
                        $.each(data, function(key, value) {
                            $('#main_contractor').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                    error: function(data) {
                        // console.log(data)
                    }
                });
            } else {
                $('#main_contractor').empty();
                $('#sub_contractor').empty();
            }
        });
        //=========================================================================================================
        $('#main_contractor').on('change', function() {
            var main_contractor = $(this).val();
            // alert(main_contractor);
            if (main_contractor) {
                $.ajax({
                    url: '/subcontractors/' + main_contractor,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        // console.log(data)
                        $('#sub_contractor').empty();
                        $('#sub_contractor').append('<option value="0">اختر المتعهد</option>');
                        $.each(data, function(key, value) {
                            $('#sub_contractor').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                    error: function(data) {
                        // console.log(data)
                    }
                });
            } else {
                $('#sub_contractor').empty();
            }
        });
        //=========================================================================================================
        $('#fileUploadForm').ajaxForm({
            beforeSend: function() {
                var percentage = '0';
                document.getElementById('progress_percentage').innerHTML = percentage + '%';
                document.getElementById('result').style.display = 'none';
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentage = percentComplete;
                document.getElementById('progress_percentage').innerHTML = percentage + '%';
                $('.progress .progress-bar').css("width", percentage + '%', function() {
                    return $(this).attr("aria-valuenow", percentage) + "%";
                })
            },
            success: function(response) {
                if (response.success) {
                    document.getElementById('result').style.display     = 'block';
                    document.getElementById('msg').innerHTML            = (response.data.msg) ?? '';
                    document.getElementById('total_rows').innerHTML      = (response.data.total_rows) ?? 0;
                    document.getElementById('success_count').innerHTML  = (response.data.success_count) ?? 0;
                    document.getElementById('failed_count').innerHTML   = (response.data.failed_count) ?? 0;
                    document.getElementById('repeat_count').innerHTML   = (response.data.repeat_count) ?? 0;

                    const summary = [];
                    const reasonLabels = {
                        not_allowed: 'غير مصرح لهم',
                        voter_not_found: 'لم يتم العثور على ناخبين',
                        contractor_not_found: 'متعهد غير موجود',
                        missing_identifier: 'رقم مدني مفقود'
                    };
                    if (response.data.failed_rows_summary) {
                        Object.entries(response.data.failed_rows_summary).forEach(([reason, count]) => {
                            const label = reasonLabels[reason] ?? reason;
                            summary.push(label + ': ' + count);
                        });
                    }
                    document.getElementById('failed_rows_summary').innerHTML = summary.length ? '<strong>تفاصيل أسباب الفشل:</strong> ' + summary.join('، ') : '';

                    const failedRows = response.data.failed_rows ?? [];
                    window.failedRowsDetail = failedRows;
                    if (failedRows.length > 0) {
                        const preview = failedRows.slice(0, 10).map((row, index) => {
                            const rowText = JSON.stringify(row.row, null, 0);
                            return '<div><strong>' + (index + 1) + '.</strong> ' + row.reason + ' - ' + rowText + '</div>';
                        }).join('');
                        document.getElementById('failed_rows_details').innerHTML = '<div><strong>أول 10 صفوف فاشلة:</strong></div>' + preview;
                        document.getElementById('failed_rows_details').style.display = 'block';
                        document.getElementById('download_failed_rows').style.display = 'inline-block';
                    } else {
                        document.getElementById('failed_rows_details').style.display = 'none';
                        document.getElementById('failed_rows_details').innerHTML = '';
                        document.getElementById('download_failed_rows').style.display = 'none';
                    }
                    
                    // Reset form
                    $('#candidate').val('');
                    $('#main_contractor').empty();
                    $('#sub_contractor').empty();
                    
                    // Reset progress bar
                    document.getElementById('progress_percentage').innerHTML = 0 + '%';
                    $('.progress .progress-bar').css("width", "0%");
                    $('.progress .progress-bar').attr("aria-valuenow", 0);
                } else {
                    alert('Error uploading file: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error uploading file: ' + error);
                document.getElementById('result').style.display = 'none';
            }
        });

        document.getElementById('download_failed_rows').addEventListener('click', function() {
            if (!window.failedRowsDetail || window.failedRowsDetail.length === 0) {
                return;
            }

            const rows = window.failedRowsDetail;
            const allKeys = new Set();
            rows.forEach(({ row }) => {
                Object.keys(row || {}).forEach((key) => allKeys.add(key));
            });

            const headers = ['reason', ...Array.from(allKeys)];
            const escapeValue = (value) => {
                if (value === null || value === undefined) return '';
                const str = String(value).replace(/"/g, '""');
                return '"' + str + '"';
            };

            const csvLines = rows.map(({ reason, row }) => {
                const rowValues = headers.map((header) => {
                    if (header === 'reason') {
                        return escapeValue(reason);
                    }
                    return escapeValue(row ? row[header] : '');
                });
                return rowValues.join(',');
            });

            const csvContent = [headers.map(escapeValue).join(','), ...csvLines].join('\r\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const filename = 'failed_import_rows_' + new Date().toISOString().slice(0,19).replace(/[:T]/g, '-') + '.csv';

            if (navigator.msSaveBlob) {
                navigator.msSaveBlob(blob, filename);
            } else {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            }
        });
    });
</script>
@endpush