@extends('layouts.dashboard.app')

@section('content')
<div class="projectContainer mx-auto">
    <section class="py-1 my-1 rtl">

            <div class="container mt-3">
                <form class="madameenControl" method="GET" action="{{ route('dashboard.madameen') }}">
                    <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" for="majorMota3hed">المتعهد الرئيسى</label>
                        <select name="mot3ahed" id="majorMota3hed" class="form-control py-1 py-1">
                            <option value="">الكل</option>
                            @foreach ($parents as $item)
                                <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" for="mota3hedtrust"> التزام المتعهد</label>
                        <select name="mota3hedtrust" id="mota3hedtrust" class="form-control py-1">
                            <option value="الكل">الكل</option>
                            <option value="الملتزمين">الملتزمين</option>
                            <option value="قيد المراجعة">قيد المراجعة</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <label class="labelStyle" for="madmonStatus"> حالة المضمون</label>
                        <select name="madmonStatus" id="madmonStatus" class="form-control py-1">
                            <option value="الكل">الكل</option>
                            <option value="مقيد">مقيد</option>
                            <option value="غير مقيد">غير مقيد</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100">عرض</button>

                    <div class="moreSearch my-3">
                        <div role="button" class="btn btn-primary w-100">
                            خيارات أكثر للبحث
                        </div>
                        <div class="description d-none p-2">
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" for="madameenDate">المضامين من تاريخ</label>
                             
                                <input type="date" name="date" class="form-control py-1 py-1" id="madameenDate">
                            </div>
                            
                            <div class="d-flex align-items-center mb-1">
                                <label class="labelStyle" for="madameenCommitte"> اللجنة</label>
                                <select name="committee" id="madameenCommitte" class="form-control py-1">
                                    <option value="">الكل</option>
                                    @foreach ($committees as $com)
                                        <option value="{{ $com->id }}">{{ $com->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-danger w-100">مسح الخيارات</button>

                    <div class="d-flex align-items-center mt-3">
                        <label for="filterPerTrust" class="w-25">تصفية حسب الالتزام
                            <span class="percentage fw-bold">0</span>%</label>
                        <input type="range" name="filterPerTrust" id="filterPerTrust" value="0" class="w-100" />
                    </div>
                </form>

                <div class="d-flex widthOn3 mt-3 text-white">
                    <div class="text-center rounded-3 pt-3 bg-info font-sm">
                        استخراخ كشوف (<span class="listNumber">0</span>)
                    </div>
                    <div class="text-center rounded-3 pt-3 mx-2 bg-dark font-sm">
                        المضامين ( <span class="madameednumber">{{ $voters->count() }}</span> )
                    </div>
                    <div class="text-center rounded-3 overflow-hidden">
                        <p class="mb-0 font-sm bg-success">
                            مضمون ( <span class="madamoodnumber">{{ $children->where('status',1)->count() }}</span> )
                        </p>
                        <p class="mb-0 font-sm bg-secondary">
                            غير مفروز ( <span class="unsortedNumber">{{ $children->where('status', null)->count() }}</span> )
                        </p>
                        <p class="mb-0 font-sm bg-warning">
                            قيد المتابعة ( <span class="followUp">{{ $children->where('status',0)->count()}}</span>
                            )
                        </p>
                    </div>
                </div>
                
                <button data-bs-toggle="modal" data-bs-target="#displayData" class="btn btn-secondary w-100 mb-3 mt-2 fs-5">
                 عرض تفاصيل المضامين
                </button>
                <div class="modal modal-l rtl" id="displayData" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <!-- Modal Header with Close Button -->
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- Modal Body containing a responsive table -->
                        <div class="modal-body">
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered  rtl table-striped-columns overflow-scroll text-center">
                                    <thead class="table-dark  border-0 border-secondary border-bottom border-2">
                                           <tr>
                                                <th></th>
                                                <th>الذكور</th>
                                                <th>الاناث</th>
                                                <th>المجموع</th>
                    
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                      <tr>
                                            <td>المضامين</td>
                                            <td class="table-primary" > {{ $overAll['MenCount'] }}</td>
                                            <td class="table-danger"> {{ $overAll['WomenCount'] }}</td>
                                            <td>{{ $overAll['Count'] }}</td>
                                        </tr>
                                        <tr>
                                            <td> عدد تكرار المضامين </td>
                                            <td class="table-primary" > {{ $overAll['menHasContractors'] }}</td>
                                            <td class="table-danger"> {{ $overAll['womenHasContractors'] }}</td>
                                            <td>{{ $overAll['AllHasContractors'] }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                            </div>
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered  rtl table-striped-columns overflow-scroll text-center">
                                    <thead class="table-dark  border-0 border-secondary border-bottom border-2">
                                           <tr>
                                                <th></th>
                                                <th>الذكور</th>
                                                <th>الاناث</th>
                    
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                      <tr>
                                            <td>المضامين الاكثر تكرارا</td>
                                            <td class="table-primary" > {{ $overAll['MenName'] }}</td>
                                            <td class="table-danger"> {{ $overAll['WomenName'] }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="madameenTable table-responsive mt-2">
                    <table class="table   rtl overflow-hidden rounded-3 text-center">
                        <thead class="table-primary border-0 border-secondary border-bottom border-2">
                            <tr>
                                <th>
                                    <button class="btn btn-secondary all">الكل</button>
                                </th>
                                <th>#</th>
                                <th class="w150">أسماء المضامين (الناخبين)</th>
                                <th>لجنة</th>
                                <th>تعهد</th>
                                <th>التزام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($voters as $i=>$voter)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="check" name="madameenNameChecked"
                                            value="{{ $voter->id }}" />
                                    </td>
                                    <td>{{ $i + 1 }}</td>
                                    <input type="hidden" id="voter_id" value="{{ $voter->id }}">
                                    <td data-bs-toggle="modal" data-bs-target="#madameen">
                                        {{ $voter->name }}
                                    </td>
                                    <td>
                                        @if ($voter->status == true)
                                            <i class="fa fa-check-square text-success"></i>
                                            <span>تم التصويت</span>
                                            <span class="currentTime">{{ $voter->updated_at }}</span>
                                        @else
                                            <span>
                                                {{ $voter->committee ? $voter->committee->name : 'لايوجد' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $voter->contractors->count() }}</td>
                                    <td>0%</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Modal madameenName-->
                <div class="modal modal-md rtl" id="madameen" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">
                                    بيانات الناخب
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <label class="labelStyle" class="" for="id">الرقم المدنى</label>
                                    <input type="text" readonly class="form-control py-1" name="id"
                                        id="id" />
                                </div>
                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <label class="labelStyle" class="" for="name">الاسم الكامل </label>
                                    <input type="text" readonly class="form-control py-1" name="name" id="name"
                                        value="" />
                                </div>
                                <div class="d-flex align-items-center mb-1 mb-3">

                                    <div class="d-flex align-items-center mb-1  me-2">
                                        <label class="labelStyle" class="" for="family">العائلة </label>
                                        <input type="text" readonly class="form-control py-1" name="family"
                                            id="family" value="" />
                                    </div>
                                </div>
                                <hr />
                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <label class="labelStyle" class="" for="phone1"> الهاتف </label>
                                    <input type="text" readonly class="form-control py-1" name="phone1" id="phone1"
                                        value="144254435" min="0" />
                                    <a href="" id="phone1-call" class="d-inline-block px-4 text-white bg-primary"><i
                                            class="pt-2 fs-5 fa fa-phone"></i></a>
                                    <a href="" id="phone1-wa" class="d-inline-block px-4 text-white bg-success"><i
                                            class="pt-2 fs-5 fa-brands fa-whatsapp"></i></a>
                                </div>
                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <label class="labelStyle" class="" for="phone2"> الهاتف </label>
                                    <input type="text" readonly class="form-control py-1" name="phone2"
                                        id="phone2" />
                                    <a href="" id="phone2-call" class="d-inline-block px-4 text-white bg-primary"><i
                                            class="pt-2 fs-5 fa fa-phone"></i></a>
                                    <a href="" id="phone2-wa" class="d-inline-block px-4 text-white bg-success"><i
                                            class="pt-2 fs-5 fa-brands fa-whatsapp"></i></a>
                                </div>
                                <hr />
                                <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="alfkhd">فخذ </label>
                                        <input type="text" readonly class="form-control py-1" name="alfkhd"
                                            id="alfkhdd" value="" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="alfraa">فرع </label>
                                        <input type="text" readonly class="form-control py-1" name="alfraaa"
                                            id="alfraaa" value="" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="albtn">بطن </label>
                                        <input type="text" readonly class="form-control py-1" name="albtnn"
                                            id="albtnn" value="" />
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="codd1">كود1 </label>
                                        <input type="text" readonly class="form-control py-1" name="codd1"
                                            id="codd1" value="" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="codd2">كود2 </label>
                                        <input type="text" readonly class="form-control py-1" name="codd2"
                                            id="codd2" value="" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="codd3">كود3 </label>
                                        <input type="text" readonly class="form-control py-1" name="codd3"
                                            id="codd3" value="" />
                                    </div>
                                </div>
                                <hr />

                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <div class="d-flex align-items-center mb-1 w-50">
                                        <label class="labelStyle" class="" for="type">الجنس</label>
                                        <input type="text" readonly class="form-control py-1" name="type"
                                            id="typeee" value="" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1 w-50">
                                        <label class="labelStyle" class="" for="work"> المهنة </label>
                                        <input type="text" readonly class="form-control py-1" name="work"
                                            id="work" value="" />
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <div class="d-flex align-items-center mb-1 w-50">
                                        <label class="labelStyle" class="" for="yearOfBirth">الميلاد</label>
                                        <input type="text" readonly class="form-control py-1" name="yearOfBirth"
                                            id="yearOfBirth" value="" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1 w-50">
                                        <label class="labelStyle" class="" for="age"> العمر </label>
                                        <input type="text" readonly class="form-control py-1" name="age"
                                            id="age" value="" />
                                    </div>
                                </div>

                                <div class="d-flex mb-1 align-items-center">
                                    <label class="labelStyle" class="" for="address">العنوان</label>
                                    <input type="text" readonly class="form-control py-1" name="address" id="address"
                                        value="" />
                                </div>
                                <hr />

                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <div class="d-flex align-items-center mb-1 w-50">
                                        <label class="labelStyle" class="" for="circle">الدائرة</label>
                                        <input type="text" readonly class="form-control py-1" name="circle"
                                            id="circle" value="6" min="1" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1 w-50">
                                        <label class="labelStyle" class="" for="almrgaa"> المرجع </label>
                                        <input type="text" readonly class="form-control py-1" name="almrgaa"
                                            id="almrgaaa" value="" min="0" />
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="character">حرف </label>
                                        <input type="text" readonly class="form-control py-1" name="character"
                                            id="character" value="" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="userTable">جدول </label>
                                        <input type="text" readonly class="form-control py-1" name="userTable"
                                            id="userTable" value="" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="labelStyle" class="" for="registe">قيد </label>
                                        <input type="text" readonly class="form-control py-1" name="registe"
                                            id="registe" value="" />
                                    </div>
                                </div>
                                <div class="d-flex mb-1 align-items-center justify-content-between">
                                    <label class="labelStyle" class="" for="committee">لجنة</label>
                                    <input type="text" readonly class="form-control py-1 w-25 ms-2" name="committee"
                                        id="committee" value="19" min="1" />
                                    <input type="text" readonly class="form-control py-1 w-75" name="school"
                                        id="school" value="مدرسة الفردوس المتوسطة بنات" />
                                </div>
                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <div class="d-flex align-items-center mb-1 w-50">
                                        <label class="labelStyle" class="" for="registeNumber">تاريخ القيد</label>
                                        <input type="text" readonly class="form-control py-1" name="registeNumber"
                                            id="registeNumber" value="6" min="1" />
                                    </div>
                                    <div class="d-flex align-items-center mb-1 w-50">
                                        <label class="labelStyle" class="" for="lastCircle"> الدائرة السابقة </label>
                                        <input type="text" readonly class="form-control py-1" name="lastCircle"
                                            id="lastCircle" value="4651365463" min="0" />
                                    </div>
                                </div>

                                <div class="table-responsive mt-4">
                                    <table class="table overflow-hidden rtl">
                                        <thead class="table-secondary text-center border-0 border-dark border-bottom border-2">
                                            <tr>
                                                <th>المتعهدين بالناخب</th>
                                                <th>مدخلات المتعهد بالناخب</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider" id="mot3deen_table">


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
@push('js')
    <script>
        $('td[data-bs-target="#madameen"]').on("click", function() {

            let id = $(this).siblings("#voter_id").val();
            var url = '/voter/' + id;
            axios.get(url)
                .then(function(response) {
                    console.log('Success:', response);
                    $("#id").val(response.data.voter.alrkm_almd_yn)
                    $("#name").val(response.data.voter.name)
                    $("#family").val(response.data.family)
                    $("#phone1").val(response.data.voter.phone1)
                    $("#phone2").val(response.data.voter.phone2)
                    $("#alfkhdd").val(response.data.voter.alfkhd)
                    $("#alfraaa").val(response.data.voter.alfraa)
                    $("#albtnn").val(response.data.voter.albtn)
                    $("#codd1").val(response.data.voter.cod1)
                    $("#codd2").val(response.data.voter.cod2)
                    $("#codd3").val(response.data.voter.cod3)
                    $("#typeee").val(response.data.voter.type)
                    $("#almrgaaa").val(response.data.voter.almrgaa)
                    $("#yearOfBirth").val(response.data.voter.yearOfBirth)
                    $("#committee").val(response.data.committee)
                    $("#school").val(response.data.school)
                    document.getElementById('phone1-call').href = `tel:${response.data.voter.phone1}`;
                    document.getElementById('phone1-wa').href =
                    `https://wa.me/965${response.data.voter.phone1}`;
                    document.getElementById('phone2-call').href = `tel:${response.data.voter.phone2}`;
                    document.getElementById('phone2-wa').href =
                    `https://wa.me/965${response.data.voter.phone2}`;

                    let contractors = response.data.contractors;
                    var cartona = [];
                    for (let i = 0; i < contractors.length; i++) {
                        cartona.push(`
<tr>
<td rowspan="3">${contractors[i].name}</td>
<td>الالتزام: ${contractors[i].pivot.percentage ? contractors[i].pivot.percentage : "0"}%</td>
</tr>
<tr>
<td>الهاتف: ${contractors[i].phone ? contractors[i].phone : "لايوجد"}</td>
</tr>
<tr>
<td>الملاحظات: ${contractors[i].note ? contractors[i].note : "لايوجد" } </td>
</tr>
`);
                    }
                    document.getElementById("mot3deen_table").innerHTML = cartona.join('');
                    console.log(contractors.length);

                })
                .catch(function(error) {
                    console.error('Error:', error);
                    alert('Failed to set votes.');
                });


        });
        $("#addMota3ahed").on('change', function() {
            let url = "/ass/" + $(this).val()
            $("#form-attach").attr("action", url)
        })
    </script>

    <script>
        $(document).ready(function() {
            $('.button').on('click', function() {
                var buttonValue = $(this).val();
                $('#type').val(buttonValue);

                if (!checkedValues) {
                    var checkedElements = $('.check:checked');

                    var checkedValues = $.map(checkedElements, function(element) {
                        return $(element).val();
                    });

                }
                checkedValues.forEach(function(value) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'voter[]',
                        value: value
                    }).appendTo('#export');
                });


                var form = $('#export')[0]; // Ensure we get the DOM element, not jQuery object
                var formData = new FormData(form);
                var submitBtn = $(this);
                axios.get(form.action, formData)
                    .then((res) => {
                        console.log(res);
                        setTimeout(() => {
                            console.log(res.data);
                        }, 2000);
                        if (res.data.Redirect_Url) {
                            window.location.href = res.data.Redirect_Url;
                        } else {
                            toastr.success(res.data.success);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        toastr.error(error.response.data.error ?? '{{ __('main.unexpected-error') }}');
                        submitBtn.attr('disabled', false);
                    });
            });
        });
    </script>
@endpush
