@extends('layouts.dashboard.app')

@section('content')
<style>
  .loader {
    width: 10px;
    height: 10px;
    border: 5px solid #FFF;
    border-bottom-color: transparent;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
    }

    @keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
    } 
</style>
<section class="py-1 my-1 rtl">
    <div class="container mt-5">

        <div class="table-responsive mt-4">
            <table
              class="table table-hover rtl overflow-hidden rounded-3 text-center"
            >
              <thead
                class=" border-0 border-secondary border-bottom border-2 fw-bold"
              >
                <tr>
                  <th>المدرسه</th>
                  <th >الرجال</th>
                  <th >النساء</th>
                  <th>المجموع</th>
                  <th></th>
                </tr>
              </thead>
               <tbody>

                 <tr>
                    <td>منطقه {{ auth()->user()->election?->name ?? 'غير محدد' }}</td>
                    <td class="table-primary">{{$voters->where('type','ذكر')->count()}}</td>
                    <td class="table-danger">{{$voters->where('type', '!=', 'ذكر')->count()}}</td>
                    <td>{{$voters->count()}}</td>

                </tr>

               </tbody>
            </table>
          </div>

          <div class="w-100 my-4">
           <canvas id="myChart"></canvas>
          </div>





      <!-- Modal elkshoofDetails-->
      <div
        class="modal modal-md rtl"
        id="elkshoofDetails"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                بيانات الناخب
              </h1>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body px-4">

              <form action="{{route('export')}}" method="GET" id="export">
                <input type="hidden" name="search_id"id="search_id" value="">
                <div class="d-flex align-items-center mb-3">
                    <label class="labelStyle pt-2 pb-1 rounded-3" for="na5ebArea">المنطقة</label>
                  <input type="text" class="form-control bg-secondary bg-opacity-25 " value="" name="region" id="na5ebArea">
                </div>

                <div class="row g-4">
                  <div class="col-6 ">
                    <div class="d-flex align-items-center ">
                        <input checked disabled type="checkbox" class="" name="columns[]" value="name" id="na5ebName">
                        <label for="na5ebName">اسم الناخب</label>
                    </div>
                    <div class="d-flex align-items-center ">
                        <input checked type="checkbox" class="" name="columns[]" value="family" id="na5ebFamilyName">
                        <label for="na5ebFamilyName"> العائلة</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <input type="checkbox" class="ms-2" name="columns[]" value="age" id="na5ebAge">
                        <!-- <label for="na5ebAge"> العائلة</label> -->
                        <select name="na5ebAge" class="border-0 p-1">
                            <option value="الميلاد / العمر">الميلاد / العمر</option>
                            <option value=" العمر"> العمر</option>
                            <option value=" سنة الميلاد  ">سنة الميلاد</option>
                            <option value="تاريخ الميلاد">تاريخ الميلاد</option>
                        </select>
                    </div>
                  </div>

                  <div class="col-6 ">
                      {{-- <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebCircle">
                      <label for="na5ebCircle"> الدائرة</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebChar">
                      <label for="na5ebChar"> الحرف</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebTableNumber">
                      <label for="na5ebTableNumber"> رقم الجدول</label>
                    </div> --}}
                    <div class="d-flex align-items-center ">
                      <input checked  type="checkbox" class="" name="columns[]" value="alsndok" id="na5ebRejestNumber">
                      <label for="na5ebRejestNumber"> رقم القيد</label>
                    </div>

                  </div>

                   <div class="col-6 ">
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="phone" id="na5ebPhone">
                      <label for="na5ebPhone"> الهاتف</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="type" id="na5ebType">
                      <label for="na5ebType"> الجنس</label>
                    </div>
                    <div class="d-flex align-items-center mb-1 ">
                        <input  type="checkbox" class="ms-2" name="columns[]" value="created_at" id="na5ebRejesterDate">
                        <label class="labelStyle" for="na5ebRejesterDate"> تاريخ القيد</label>
                      </div>
                    {{-- <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="job" id="na5ebJop">
                      <label for="na5ebJop"> المهنة</label>
                    </div>

                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebAddress">
                      <label for="na5ebAddress"> العنوان</label>
                    </div> --}}

                    <div class="d-flex align-items-center ">
                        <input  type="checkbox" class="" name="columns[]" value="region" id="na5ebRejon">
                        <label for="na5ebRejon"> المنطقة</label>
                      </div>
                  </div>

                  <div class="col-6 ">
                      <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="committee" id="na5ebCommitte">
                      <label for="na5ebCommitte"> اللجنة</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="madrasa" id="na5ebSchool">
                      <label for="na5ebSchool"> مدرسة الانتخاب</label>
                    </div>
                    <div class="d-flex align-items-center ">
                     <input checked type="checkbox" class="" name="columns[]" value="restricted" id="na5ebRejesterStatus">
                     <label for="na5ebRejesterStatus"> حالة القيد</label>
                   </div>
                    {{--
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebRejesterDate">
                      <label for="na5ebRejesterDate"> تاريخ القيد</label>
                    </div>
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebReference">
                      <label for="na5ebReference"> مرجع الداخلية</label>
                    </div> --}}
                    <div class="d-flex align-items-center ">
                      <input  type="checkbox" class="" name="columns[]" value="" id="na5ebCheckedTime">
                      <label for="na5ebCheckedTime"> وقت التصويت</label>
                    </div>
                  </div>



                </div>
              <hr>
              <div class="d-flex align-items-center">
                <label class="labelStyle" for="mota3aheedTrusted">ترتيب</label>
                <select name="sorted" id="sorted" class="form-control">
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
              <div class="d-flex align-items-center mb-3 pdfe">
                  <label class="w-50 lableStyle" for="sendToNa5eb">ارسال PDF عبر whatsapp</label>
                  <input type="number" class="form-control bg-secondary bg-opacity-25" name="to" id="sendToNa5eb" placeholder="رقم الهاتف">
                  <button class="btn btn-primary me-2 button" value="Send">ارسال</button>
              </div>




              </form>



            </div>
          </div>
        </div>
      </div>

      <form action="{{route('dashboard.statement')}}" method="GET" class="d-flex ">
        <input type="search" name="family" id="searchByFamily" class="form-control w-75" placeholder="البحث">
        <button type="submit" class="btn btn-outline-dark mx-2 mb-1 ">بحث</button>
    </form>
      <div class="table-responsive mt-2">
        <table
          class="table table-hover rtl overflow-hidden rounded-3 text-center"
        >
          <thead
            class=" border-0 border-secondary border-bottom border-2 fw-bold"
          >
            <tr>
              <th>بحث</th>
              <th>العوائل</th>
              <th >الرجال</th>
              <th >النساء</th>
              <th>المجموع</th>
              <th></th>
            </tr>
          </thead>
           <tbody>
            @forelse ( $relations['families'] as $family )
            <tr>
                <td >
                    <a href="{{ route('dashboard.statement.search', ['family' => $family['id']]) }}">
                        <button  class="btn btn-outline-dark"><i class="fa fa-magnifying-glass"></i></button>
                    </a>
                </td>
                <td>{{$family['name']}}</td>
                <td class="table-primary">{{$family['men']}}</td>
                <td class="table-danger">{{$family['women']}}</td>
                <td>{{$family['total']}}</td>
                <td >
                    <input type="hidden" id="family_id" value="{{$family['id']}}">
                    <button data-bs-toggle="modal" data-bs-target="#elkshoofDetails" class="btn btn-outline-dark">كشوف</button>
                </td>
            </tr>

            @empty

            @endforelse
           </tbody>
        </table>
      </div>



</div>
  </section>

@endsection
@push('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

// el keshoof
// chart js

(function(){
  let ctx = document.getElementById("myChart");
let x = new Chart(ctx, {
    type: "bar",
    data: {
        labels: ['{!! auth()->user()->election->name !!}'],
      datasets: [
        {
          label: "الرجال",
          data: [{{$voters->where('type','ذكر')->count()}}],
          borderWidth: 1,
        },
        {
          label: "النساء",
          data: [{{$voters->where('type','!=','ذكر')->count()}}],
          borderWidth: 1,
        },
      ],
    },
    options: {
      scales: {
        x: {
          stacked: true,
        },
        y: {
          stacked: true,
        },
      },
    },
  });
})();

</script>

<script>
$(document).ready(function() {
    $('button[data-bs-target="#elkshoofDetails"]').on("click", function () {
        var siblingInput = $(this).siblings('input[type="hidden"]');
        var inputId = siblingInput.attr('id');
        var value = siblingInput.val();
        $('input[id="search_id"]').attr('name', inputId);
        $('input[id="search_id"]').val(value);
    });

    $('.button').on('click', function() {
        var buttonValue = $(this).val();
        $('#type').val(buttonValue);

        var form = $('#export')[0]; // Ensure we get the DOM element, not jQuery object
        var formData = new FormData(form);
        var submitBtn = $(this);
        icon=`<span class="loader"></span>`;
        submitBtn.html(icon);

        axios.get(form.action, formData)
            .then((res) => {
                console.log(res);
                    console.log(res.data);
               
                if (res.data.Redirect_Url) {
                    window.location.href = res.data.Redirect_Url;
                } else {
                    toastr.success(res.data.success);
                    // setTimeout(() => {
                    //     location.reload();
                    // }, 1000);
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
<script>
  $(document).ready(function () {
            $('.button').on('click', function () {
                // Get the button value and set it in the form
                var buttonValue = $(this).val();
                $('#type').val(buttonValue);

                // Get all checked values
                var checkedElements = $('.check:checked');
                var checkedValues = $.map(checkedElements, function (element) {
                    return $(element).val();
                });

                // Remove any previously added hidden inputs
                $('#export').find('input[name="voter[]"]').remove();

                // Append hidden inputs for all checked values
                checkedValues.forEach(function (value) {
                    $('<input>')
                        .attr({
                            type: 'hidden',
                            name: 'voter[]',
                            value: value,
                        })
                        .appendTo('#export');
                });

                var form = $('#export');
                var submitBtn = $(this);
                submitBtn.attr('disabled', true); // Disable button to prevent duplicate requests
                icon=`<span class="loader"></span>`;
                submitBtn.html(icon);
                // Serialize the form data into an array
                var formDataArray = form.serializeArray();

                // Convert the array into an object
                var queryData = {};
                $.each(formDataArray, function (_, field) {
                    if (queryData[field.name]) {
                        if (!Array.isArray(queryData[field.name])) {
                            queryData[field.name] = [queryData[field.name]];
                        }
                        queryData[field.name].push(field.value || '');
                    } else {
                        queryData[field.name] = field.value || '';
                    }
                });

                // Use axios.get with the form data as params
                axios
                    .get(form.attr('action'), {
                        params: queryData, // Pass form data as an object
                        responseType: buttonValue === "Excel" || buttonValue === "PDF" ? 'blob' : 'json',
                    })
                    .then((res) => {
                        if (buttonValue === "Excel" || buttonValue === "PDF") {
                            // Handle file download
                            const url = window.URL.createObjectURL(new Blob([res.data]));
                            const link = document.createElement('a');
                            link.href = url;
                            link.setAttribute('download', buttonValue === "Excel" ? 'Voters.xlsx' : 'Voters.pdf');
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                        } else if ( buttonValue === "Send" && res.data.Redirect_Url){
                            const newTab = window.open();
                            newTab.document.open();
                            newTab.location.href = res.data.Redirect_Url; // Use href instead of url
                            newTab.document.close();

                        }else {
                            // Handle fallback: open link to the view
                            const newTab = window.open();
                            newTab.document.open();
                            newTab.document.write(res.data);
                            newTab.document.close();
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        toastr.error(
                            error.response?.data?.error ?? '{{ __('main.unexpected-error') }}'
                        );
                    })
                    .finally(() => {
                        submitBtn.html(submitBtn.val());
                        submitBtn.attr('disabled', false); // Re-enable the button
                    });
            });
        });
</script>
@endpush
