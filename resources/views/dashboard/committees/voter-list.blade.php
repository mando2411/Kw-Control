@extends('layouts.dashboard.app')

@section('content')
    <style>
        .new-row {
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            /* Adjust duration */
        }

        .new-row.show {
            opacity: 1;
        }

        /* Styling the load more button */
        #load-more {
            background-color: #007bff;
            /* Primary blue color */
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
            /* Darker blue on hover */
            transform: scale(1.05);
            /* Slightly enlarge on hover */
        }

        #load-more:active {
            transform: scale(0.95);
            /* Slightly shrink on click */
        }

        /* Loading spinner styles */
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

        /* Spinner animation */
        @keyframes spinner {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <section class="pb-5 pt-2 rtl">
        <div class="container">

            <div class="flex-grow-1 ms-2">
                <button data-bs-toggle="modal" data-bs-target="#elkshoofDetails" class="btn btn-info w-100">استخراج
                    كشوف</button>
            </div>
            <div class="mainTable table-responsive mt-4">
                <table class="table rtl overflow-hidden rounded-3 text-center">
                    <thead class="table-secondary border-0 border-secondary border-bottom border-2">
                        <tr>
                            <th>
                                <button class="btn btn-secondary all">الكل</button>
                            </th>
                            <th class="">الأسماء
                                <span class="namesListCounter listNumber bg-dark text-white rounded-2 p-1 px-3 me-2"
                                    id="search_count" style="color:#fff !important">{{ $voters->count() }} </span>
                            </th>
                            <th>العائلة</th>
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody id="voter-list">
                        @include('dashboard.statements.component.table')
                    </tbody>
                </table>
                <!-- Load More Button -->
                <div class="text-center">
                    @if ($voters->hasMorePages())
                        <button id="load-more" data-next-page="{{ $voters->nextPageUrl() }}" class="btn">المزيد</button>
                    @endif
                </div>
            </div>



            <!-- Modal elkshoofDetails-->
            <div class="modal modal-md rtl" id="elkshoofDetails" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-headerpy-1">
                            <h1 class="modal-title h4" id="exampleModalLabel">
                                بيانات الناخب
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4">

                            <form action="{{ route('export') }}" method="GET" id="export">
                                <input type="hidden" name="search_id"id="search_id" value="">
                                <div class="d-flex align-items-center mb-1 mb-3">
                                    <label class="labelStyle" class="bg-secondary bg-opacity-25 pt-2 pb-1 rounded-3"
                                        for="na5ebArea">العائله</label>
                                    <input type="text" class="form-control py-1 bg-secondary bg-opacity-25 "
                                        value="" name="region" id="na5ebArea">
                                </div>

                                <div class="row g-2">
                                    <div class="col-6 ">
                                        <div class="d-flex align-items-center mb-1 ">
                                            <input checked disabled type="checkbox" class="" name="columns[]"
                                                value="name" id="na5ebName">
                                            <label class="labelStyle" for="na5ebName">اسم الناخب</label>
                                        </div>
                                        <div class="d-flex align-items-center mb-1 ">
                                            <input checked type="checkbox" class="ms-2" name="columns[]" value="family"
                                                id="na5ebFamilyName">
                                            <label class="labelStyle" for="na5ebFamilyName"> العائلة</label>
                                        </div>
                                        <div class="d-flex align-items-center mb-1">
                                            <input type="checkbox" class="ms-2" name="columns[]" value="age"
                                                id="na5ebAge">
                                            <!-- <label class="labelStyle" for="na5ebAge"> العائلة</label> -->
                                            <select name="na5ebAge" class="border-0 p-1">
                                                <option value="الميلاد / العمر">الميلاد / العمر</option>
                                                <option value=" العمر"> العمر</option>
                                                <option value=" سنة الميلاد  ">سنة الميلاد</option>
                                                <option value="تاريخ الميلاد">تاريخ الميلاد</option>
                                            </select>
                                        </div>

                                        <div class="d-flex align-items-center mb-1 ">
                                            <input type="checkbox" class="ms-2" name="columns[]" value="phone"
                                                id="na5ebPhone">
                                            <label class="labelStyle" for="na5ebPhone"> الهاتف</label>
                                        </div>
                                        <div class="d-flex align-items-center mb-1 ">
                                            <input type="checkbox" class="ms-2" name="columns[]" value="type"
                                                id="na5ebType">
                                            <label class="labelStyle" for="na5ebType"> الجنس</label>
                                        </div>
                                    </div>


                                </div>

                                <div class="col-6 ">


                                </div>

                                <div class="col-6 ">

                                    <div class="d-flex align-items-center mb-1 ">
                                        <input type="checkbox" class="ms-2" name="columns[]" value="madrasa"
                                            id="na5ebSchool">
                                        <label class="labelStyle" for="na5ebSchool"> مدرسة الانتخاب</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1 ">
                                        <input checked type="checkbox" class="ms-2" name="columns[]"
                                            value="restricted" id="na5ebRejesterStatus">
                                        <label class="labelStyle" for="na5ebRejesterStatus"> حالة القيد</label>
                                    </div>
                                    <div class="d-flex align-items-center mb-1 ">
                                        <input type="checkbox" class="ms-2" name="columns[]" value="created_at"
                                            id="na5ebRejesterDate">
                                        <label class="labelStyle" for="na5ebRejesterDate"> تاريخ القيد</label>
                                    </div>
                                    {{-- <div class="d-flex align-items-center mb-1 ">
                        <input  type="checkbox" class="ms-2" name="columns[]" value="" id="na5ebReference">
                        <label class="labelStyle" for="na5ebReference"> مرجع الداخلية</label>
                      </div> --}}
                                    <div class="d-flex align-items-center mb-1 ">
                                        <input type="checkbox" class="ms-2" name="columns[]" value=""
                                            id="na5ebCheckedTime">
                                        <label class="labelStyle" for="na5ebCheckedTime"> وقت التصويت</label>
                                    </div>
                                </div>



                        </div>
                        <hr>
                        <div class="d-flex align-items-center mb-1">
                            <label class="labelStyle" for="mota3aheedTrusted">ترتيب</label>
                            <select name="sorted" id="sorted" class="form-control py-1">
                                <option value="asc">أبجدى</option>
                                <option value="phone">الهاتف</option>
                                <option value="asc">الألتزام</option>
                            </select>
                        </div>
                        <input type="hidden" name="type" id="type">
                        <div class="rtl my-4 text-center">
                            <a href="">
                                <button class="btn btn-primary button" value="PDF">PDF</button>
                            </a>
                            <a href="">
                                <button class="btn btn-success button" value="Excel">Excel</button>
                            </a>
                            <a href="">
                                <button class="btn btn-secondary button" value="print">طباعة</button>
                            </a>
                            <a href="">
                                <button class="btn btn-secondary button" value="show">عرض</button>
                            </a>
                        </div>


                        <p class="text-danger">* ملاحظة : لا يم;ن استخراج البيانات الضخمة عبر ملف PDF</p>
                        <div class="d-flex flex-column mb-1 pdfe">
                            <label class="labelStyle" class="w-50" for="sendToNa5eb">ارسال PDF عبر whatsapp</label>
                            <div class="d-flex">
                                <input type="number" class="form-control py-1 bg-secondary bg-opacity-25" name="to"
                                    id="sendToNa5eb" placeholder="رقم الهاتف">
                                <button class="btn btn-primary me-2 button" value="Send">ارسال</button>

                            </div>
                        </div>




                        </form>



                    </div>
                </div>
            </div>
            <!-- Modal voterData-->
            <div class="modal modal-md rtl" id="voterData" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header py-1">
                            <h1 class="modal-title h4" id="exampleModalLabel">
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

                            <div class="d-flex mb-1 align-items-center justify-content-between d-none" id="div_attend">
                                <div class="text-center">

                                    <label class="labelStyle" class="" for="attendance">تفاصيل التصويت</label>

                                </div>
                                <textarea readonly name="attendance" class="form-control py-1 w-100 ms-2" id="attendance" cols="30"
                                    rows="2">
                        </textarea>
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
@endsection

@push('js')
    <script>
                    $(document).ready(function() {
                $('#load-more').on('click', function() {
                    var nextPage = this.getAttribute('data-next-page');
                    var loadMoreButton = this;

                    console.log(nextPage,
                    loadMoreButton);
                    if (nextPage) {


                        var $loadMoreBtn = $('#load-more');
                        $loadMoreBtn.addClass('loading').prop('disabled',
                        true); // Add loading class and disable button

                        $.ajax({
                            url: nextPage, // Page URL for the next page
                            type: 'GET',
                            success: function(response) {
                                var $newRows = $(response.html);
                                $newRows.hide(); // Hide new rows initially
                                $('#voter-list').append(
                                $newRows); // Append the new rows to the voter list
                                $newRows.fadeIn(800); // Fade in the new rows for a smooth effect

                                loadMoreButton.setAttribute('data-next-page', response.nextPageUrl);

                                // Hide button if there are no more pages
                                if (!response.hasMorePages) {
                                    loadMoreButton.style.display = 'none';
                                }

                                $loadMoreBtn.removeClass('loading').prop('disabled', false).text(
                                    'المزيد'
                                    ); // Remove loading class, re-enable button, and reset text
                                    voterData()
                            },
                            error: function() {
                                alert('Something went wrong. Please try again.');
                                $loadMoreBtn.removeClass('loading').prop('disabled', false).text(
                                    'المزيد'); // Remove loading class and re-enable button on error
                            }
                        });
                    }
                });
            });

    </script>
      <script>
        function Search(value) {
            $(value).parent().submit()
        }
    </script>
@endpush

@push('js')
<script>
    function voterData(){
        $('i[data-bs-target="#voterData"]').on("click", function() {

        let id = $(this).attr("data-target-id");
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
        if (response.data.attend) {
            $("#div_attend").removeClass('d-none');
            let text = "تم التحضير بواسطه" + " " + response.data.attend.name + " " + "في" + " " +
                response.data.committee;
            let text2 = "تم التحضير " + " " + "في" + " " + response.data.committee;
            $("#school").val(text2)
            $("#attendance").val(text);
        }
        document.getElementById('phone1-call').href = `tel:${response.data.voter.phone1}`;
        document.getElementById('phone1-wa').href =
            `https://wa.me/965${response.data.voter.phone1}`;
        document.getElementById('phone2-call').href = `tel:${response.data.voter.phone2}`;
        document.getElementById('phone2-wa').href =
            `https://wa.me/965${response.data.voter.phone2}`;
        let contractors = response.data.contractors;
        var cartona = [];

        console.log(contractors);

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
     }
</script>
<script>
    $(document).ready(function() {
        voterData()
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
    @push('js')

        <script>
            function Search(value) {
                $(value).parent().submit()
            }
        </script>
        <script>
            $('i[data-bs-target="#voterData"]').on("click", function() {
                console.log();

                let id = $(this).attr("data-target-id");
                console.log(id);

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
                        if (response.data.attend) {
                            $("#div_attend").removeClass('d-none');
                            let text = "تم التحضير بواسطه" + " " + response.data.attend.name + " " + "في" + " " +
                                response.data.committee;
                            let text2 = "تم التحضير " + " " + "في" + " " + response.data.committee;
                            $("#school").val(text2)
                            $("#attendance").val(text);
                        }
                        document.getElementById('phone1-call').href = `tel:${response.data.voter.phone1}`;
                        document.getElementById('phone1-wa').href =
                            `https://wa.me/965${response.data.voter.phone1}`;
                        document.getElementById('phone2-call').href = `tel:${response.data.voter.phone2}`;
                        document.getElementById('phone2-wa').href =
                            `https://wa.me/965${response.data.voter.phone2}`;
                        let contractors = response.data.contractors;
                        var cartona = [];

                        console.log(contractors);

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
