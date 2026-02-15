<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{$contractor->name}}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/style.css') }}">

  </head>
  <style>
    .headerDetails {
    justify-content: center;

  figure{
    width:120px;
    height:120px;
  }

  .content {
    h1{
      font-size: 45px;
      font-family: "Gulzar", serif !important;
    }
  }
}

@media (max-width: 767px) {
    .headerDetails {
        justify-content: space-around;
        .content h1 {
            font-size: 30px     ;
        }
    }
}
@media (max-width: 600px) {
    .headerDetails {
        & .content {
            h1 {
                font-size: 20px ;
            }
        }
    }
    .headerDetails{
    justify-content: space-around;
    .content{
      h1{font-size: 20px;}
      p{font-size: px;}
    }
  }
}

  </style>
  <body>

    <section class="rtl">
      <div class="container-fluid">
        <div
          class="headerDetails row bg-secondary py-4 align-items-center "
        >
        <div class="col-2">
            <figure class="mb-0 rounded-circle overflow-hidden" >
                <img
                src="{{$contractor->creator ? $contractor->creator->image : ""}}"
                class="w-100 h-100"
                alt="{{$contractor->creator?->name}}"
              />
            </figure>
          </div>
          <div class="col-5">
            <div class="content text-white text-center">
              <h1>{{$contractor->creator?->name}}</h1>
              <p>

                  مرشح انتخابات جمعيه  {{ $contractor->creator?->election->name }}
              </p>

            </div>
          </div>
        </div>
      </div>
      <div class="committeDetails mx-auto my-4 text-center">
        <p class="fs-5 fw-semibold mb-1">مرحبا {{$contractor->name}}</p>
        <p>
          هذه الصفحة تمكنكم من متابعة المتعهدين<br />
          ولكم كل الشكر والتقدير على دعمكم لنا 🌹
        </p>
      </div>
      <div class="container">
        <div class="mx-auto my-3">
            <x-dashboard.partials.message-alert />
         @if ($contractor->hasPermissionTo('search-stat-con'))
         <div class="moreSearch ">
            <div role="button" class="btn w-100">
              <h6 class="bg-dark py-2 text-white text-center rounded-2">
                <i class="fa fa-magnifying-glass"></i>
                البحث بالكشوف
              </h6>
            </div>

            <!--  -->
            <form id="SearchForm"  class="description d-none my-1">
              <div class="d-flex align-items-center mb-2">
                <label for="searchByNameOrNum">البحث عن</label>
                <input type="hidden" name="id" value="{{$contractor->id}}">
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  id="searchByNameOrNum"
                  placeholder=" البحث عن الأسم او الرقم"
                  value=""
                />
              </div>
              <div class="d-flex align-items-center mb-2">
                <label for="searchByFamily"> العائلة</label>
                <select
                  name="family"
                  id="searchByFamily"
                  class="form-control js-example-basic-single"
                >
                  <option value="" hidden>حصر نتائج البحث حسب العائلة</option>
                    @foreach ($families as $fam )
                        <option value="{{$fam->id}}"> {{$fam->name}} </option>
                    @endforeach
                </select>
              </div>

              <button
                type="submit"
                onclick="getResultSearch()"
                class="resultSearchBtn btn btn-secondary w-100"
              >
                بحث
              </button>
            </form>
          </div>
         @endif
        </div>
      </div>
    </section>


    <!-- resultOfSearch -->
    <div
      class="modal modal-lg rtl"
      id="resultOfSearch"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="modal-title" id="exampleModalLabel">
              <img src="{{$contractor->creator ? $contractor->creator->image : "https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Flag_of_Kuwait.svg/1200px-Flag_of_Kuwait.svg.png"}} " class="w-100 mb-5" style="height: 150px" alt="banner" />
            </div>
            <table 
              class="table rtl overflow-hidden rounded-3 text-center mt-3 table-striped"
            >
              <thead
                class="table-secondary border-0 border-secondary border-bottom border-2"
              >
                <tr>
                  <th>
                    <button class="btn btn-secondary all">الكل</button>
                  </th>
                  <th class="w150 fs-5">الأسماء
                    <span
            class="namesListCounter listNumber bg-dark text-white rounded-2 p-1 px-3 me-2"
           id="search_count" style="color: #fff !impotant"> </span
          >
                  </th>
                  <th></th>
                </tr>
              </thead>
              <button type="submit" id="all_voters" class="btn btn-primary "> اضافه المحدد</button>
              <form action="{{route('ass',$contractor->id)}}" method="POST" id="form-attach">
                @csrf

                <tbody id="resultSearchData">

                </tbody>

              </form>
            </table>
          </div>
        </div>
      </div>
    </div>
    @php
    $id=$contractor->id;
    $voters = $contractor->voters()
    ->whereDoesntHave('groups', function ($query) use ($id) {
        $query->where('contractor_id', $id);
    })
    ->orderByRaw('status = true ASC')  // This puts the voters with `status = true` at the end
    ->orderBy('name', 'asc')     // This orders the rest by `created_at` ascending
    ->get();
    @endphp
    <form action="{{route("modify")}}" method="POST" id="form-transfer">
        @csrf
        <input type="hidden" name="id" value="{{$contractor->id}}" id="con_id">
    <section class="py-3 rtl bg-secondary">
      <div class="container-fluid">
        <h5>
          قائمة الأسماء
          <span
            class="  bg-dark text-white rounded-2 p-1 px-3 me-2"
            >{{$voters->count()}}</span
          >
        </h5>

        <div class="madameenTable table-responsive mt-4">
          <table
            class="table rtl overflow-hidden rounded-3 text-center"
          >
            <thead
              class="table-primary border-0 border-secondary border-bottom border-2"
            >
              <tr>
                <th>#</th>
                <th class="w150">الأسم</th>
                <th>نسبة الصدق</th>
                <th>أدوات</th>
              </tr>
            </thead>
            <tbody>
              @foreach ( $voters as $voter )
              <tr
                class="
                 @if ($voter->status == 1)
                    table-success
                 @endif
                "
              >
                <td id="voter_td">
                  <input type="checkbox" id="voter_id" class="check" name="voters[]" value="{{$voter->id}}" />
                </td>

                <td>
                  <p class="@if ($voter->restricted != 'فعال')
                      line
                  @endif">{{$voter->name}}</p>

                    @if ($voter->status == 1)
                <p class=" my-1">
                    <span><i class="fa fa-check-square text-success ms-1"></i>تم التصويت </span>
                    <span>{{ $voter->updated_at->format('Y/m/d') }}</span>
                </p>
            @endif
                </td>
                <td>%  {{$voter->pivot->percentage}} </td>

                <td data-bs-toggle="modal" data-bs-target="#nameChechedDetails">
                    <a class="btn btn-dark">تفاصيل</a>
                  </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="d-flex align-items-center">
            <label class="btn btn-dark allBtn">تحديد الكل</label>
            <select name="select" id="allSelected" class="form-control mx-2">
                <option value="" ></option>
                <option value="" hidden>التطبيق على المحدد</option>
                @forelse ($contractor->groups as $g )
                <option value="{{$g->id}}" data-message="نقل الي  {{$g->name}} ({{$g->type}} "> نقل الي  {{$g->name}} ({{$g->type}})  </option>
              @empty
              @endforelse
              @if ($contractor->hasPermissionTo('delete-stat-con'))
              <option value="delete" data-message="حذف الاسماء" class="btn btn-danger">حذف الأسماء</option>
                @endif
            </select>
            <button class="btn btn-primary" id="sub-btn" disabled>تنفيذ</button>
          </div>
      </div>
    </section>
</form>

    <!-- Modal nameChechedDetails-->
    <!-- لسه هيتعمل -->
    <div
      class="modal modal-lg rtl"
      id="nameChechedDetails"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-1">
                <button
                type="button"
                class="btn-close "
                data-bs-dismiss="modal"
                aria-label="Close"
                     ></button>
          </div>
          <form class="modal-body">
            <div>
              <img src="./image/image4.jpg" class="w-100" alt="">
            </div>
            <h5
              class=" bg-dark text-center py-2 pe-2 text-white"
            >عرض وتحرير البيانات</h5>
            <div class="d-flex align-items-center mb-3">
              <label for="mota3ahedDetailsName"> الأسم</label>
               <input
              type="text"
              name="name"
              id="mota3ahedDetailsName"
                              value=""
                class="form-control fw-semibold"
                readonly="true"
              />
            </div>
            <!-- ================================================================================ -->
            <!-- <form action="#" method="POST" class="page-body">
              @csrf -->
              
              <div class="d-flex align-items-center mb-3">
              
                <input type="hidden" id="mota3ahedDetailsVoterId" value="">
                <label class="" for="mota3ahedDetailsPhone"> الهاتف </label>
                <input
                    type="text"
                    class="form-control"
                    name="mota3ahedDetailsPhone"
                    id="mota3ahedDetailsPhone"
                    value=""
                    min="0"
                    
                />
                
                <button class="btn btn-danger rounded-circle" id="update_voter_phone_btn" title="update phone">
                  <i class="pt-1 fs-5 fa fa-pen"></i>
                </button>
                
                <a href="" class="d-inline-block px-2 py-1 mx-1 text-white bg-primary rounded-circle"><i class="pt-1 fs-5 fa fa-phone"></i></a>
                <a href="" class="d-inline-block px-2 py-1 mx-1 text-white bg-success rounded-circle"><i class="pt-1 fs-5 fa-brands fa-whatsapp"></i></a>
              </div>
            <!-- </form> -->
            <!-- ================================================================================ -->
        
        
            
      <div class="d-flex align-items-center mb-3">
          <label class="" for="mota3ahedDetailsNote">ملاحظات </label>
          <textarea
              class="form-control"
              name="mota3ahedDetailsNote"
              id="mota3ahedDetailsNote"
              value=""
              min="">
          </textarea>
      </div>
      <div class="d-flex align-items-center mt-3 border">
        <label for="mota3ahedDetailsTrustingRate">نسبة الألتزام</label>
        <input type="range" name="mota3ahedDetailsTrustingRate" id="mota3ahedDetailsTrustingRate" value="0" min="0" class="w-100">
        <span class="bg-body-secondary p-2 px-3 d-flex">% <span class="fw-semibold" id="percent">0</span></span>
      </div>

      <hr>

      <div class="d-flex align-items-center mt-3">
        <label for="mota3ahedDetailsRegiterNumber"> رقم القيد</label>
        <input class="form-control" type="number" name="mota3ahedDetailsRegiterNumber" id="mota3ahedDetailsRegiterNumber" value="9996"  class="w-100">
      </div>
      <div class="d-flex align-items-center mt-3">
        <label for="mota3ahedDetailsCommitte">  اللجنة</label>
        <input class="form-control" type="text" name="mota3ahedDetailsCommitte" id="mota3ahedDetailsCommitte" value=""  class="w-100" readonly>
      </div>
      <div class="d-flex align-items-center mt-3">
        <label for="mota3ahedDetailsSchool">  المدرسة</label>
        <input class="form-control" type="text" name="mota3ahedDetailsSchool" id="mota3ahedDetailsSchool" value=""  class="w-100" readonly>
      </div>

      <hr>

      <div class="d-flex align-items-center">
        <label for="getNearly">عرض أقرب المحتملين</label>
        <form id="Form-Siblings">
            <select class="form-control" name="getNearly" id="siblings">
                <option value="من الدرجة الاولى " id="father">من الدرجة الاولى</option>
                <option value="من الدرجة التانية ">من الدرجة التانية</option>
                <option value="بحث موسع">بحث موسع</option>
              </select>

              <button type="submit" class="btn btn-secondary" id="search">
                بحث
              </button>
        </form>
      </div>



    </form>
        </div>
      </div>
    </div>

    <section class="pt-5 pb-1">
        <!-- <div class="container-fluid px-0"> -->
        <div class="container">
            @forelse ($contractor->groups as $g )
            <div
         class="ta7reerContent @if ($g->type == 'مضمون')
            bg-success
            @else
            bg-warning @endif d-flex px-3 py-2 mb-4 rounded-3 flex-column rtl"
       >
         <div
           class="ta7reerList d-flex justify-content-between align-items-center"
         >
           <div>
             <span class="listName">{{$g->name}}</span>
             <span class="bg-dark px-2 text-white rounded-1">{{$g->voters->count()}}</span>
             <span class="listType">{{$g->type}}</span>
           </div>
           <div>
            <input type="hidden" id="group_id" value="{{$g->id}}">
             <button
               data-bs-toggle="modal"
               data-bs-target="#ta7reerData"
               class="btn btn-dark"
             >
               تحرير
             </button>
           </div>
         </div>

         @if ($g->voters->isNotEmpty())

             <div class="table-responsive mt-1 d-none">
        <form action="{{route('modify_g')}}" method="POST" id="modify-g">
            @csrf
            <input type="hidden" name="id" value="{{$contractor->id}}" id="con_id">

            <table
              class="table rtl overflow-hidden rounded-3 text-center"
            >
              <thead
                class="table-primary border-0 border-secondary border-bottom border-2"
              >
                <tr>
                  <th>#</th>
                  <th class="w150">الأسم</th>
                  <th>نسبة الصدق</th>
                  <th>أدوات</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($g->voters as  $voter)

                <tr
                class="
                 @if ($voter->status == 1)
                    table-success
                 @endif
                "
                >
                    <td id="voter_td">
                        <input type="checkbox" id="voter_id" class="check" name="voters[]" value="{{$voter->id}}" />
                      </td>

                    <td>{{$voter->name}}

                    @if ($voter->status == 1)
                <p class=" my-1">
                    <span><i class="fa fa-check-square text-success ms-1"></i>تم التصويت </span>
                    <span>{{ $voter->updated_at->format('Y/m/d') }}</span>
                </p>
            @endif
                    </td>
                    <td>{{$voter->contractors()->where('contractor_id' , $contractor->id)->first()?->pivot->percentage}} % </td>
                    <td
                      data-bs-toggle="modal"
                      data-bs-target="#nameChechedDetails"
                    >
                      <button class="btn btn-dark voter_details">تفاصيل</button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <div class="d-flex align-items-center">
                <label class="btn btn-dark allBtn">تحديد الكل</label>
                <input type="hidden" name="group_id" value="{{$g->id}}">
                <select name="select" id="allSelected-{{$g->id}}" class="form-control mx-2 select-group">
                    <option value=""></option>
                    <option value="" hidden>التطبيق على المحدد</option>
                    @forelse ($contractor->groups as $group )
                        @if ($g->id != $group->id)
                        <option data-message="نقل الي  {{$g->name}} ({{$g->type}} " value="{{$group->id}}"> نقل الي  {{$group->name}} ({{$group->type}})  </option>
                        @endif
                    @empty
                  @endforelse
                  @if ($contractor->hasPermissionTo('delete-stat-con'))
                  <option value="delete-g" class="btn btn-danger" data-message="حذف الاسماء من المجموعه">حذف الاسماء من المجموعه</option>
                  <option value="delete" class="btn btn-danger" data-message="حذف الاسماء من المضامين">حذف الاسماء من المضامين</option>
                    @endif
                </select>
                <button class="btn btn-primary" id="sub-btn-g" disabled>تنفيذ</button>
              </div>
            </form>
          </div>


         @else
         <div class="fs-5 bg-white px-3 d-none">لا يوجد</div>

         @endif


       </div>
         @empty

         @endforelse



          <!-- Modal ta7reerData-->
          <div
            class="modal modal-lg rtl"
            id="ta7reerData"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
          >
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                  ></button>
                </div>
                <div class="modal-body">
                    <form action="" id="edit-form" method="POST">
                        @csrf
                  <div class="d-flex align-items-center mt-3">
                    <label for="listNamemodal">اسم القائمة</label>
                    <input
                      type="text"
                      name="name"
                      id="listNamemodal"
                      value=""
                      class="form-control fw-semibold"
                    />
                  </div>
                  <div class="d-flex align-items-center mt-3">
                    <label for="listTypemodal">نوع القائمة</label>
                    <select
                      name="type"
                      id="listTypemodal"
                      class="form-control mx-2"
                    >
                      <option value="" hidden></option>
                      <option value="مضمون">مضمون</option>
                      <option value="تحت المراجعة">تحت المراجعة</option>
                    </select>
                  </div>
                  <button type="submit" id="button-g" class="btn btn-primary w-100 mt-3 doBtn">تنفيذ</button>
                </form>
                  <a href="" id="delete-g">
                    <button class="btn btn-danger w-100 mt-3 deleteBtn">
                        حذف المجموعة
                      </button>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <h6 class="bg-dark py-2 text-white text-center rounded-2">
            انشاء قائمة جديدة للاسماء
          </h6>
          <form action="{{route('group')}}" method="POST" class="d-flex my-3 rtl">
            @csrf
            <input type="hidden" name="contractor_id" value="{{$contractor->id}}">
            <input
             type="text"
             class="form-control"
             name="name"
             id="listName"
             placeholder="اسم القائمة"
             value=""
           />
           <select name="type" id="listType" class="form-control">
                <option value="" hidden>نوع القائمة</option>
                <option value="مضمون">مضمون</option>
                <option value="تحت المراجعة">تحت المراجعة</option>
                </select>
                <button type="submit" class="btn btn-secondary">
                    انشاء
                </button>
        </form>
        </div>

        <section class="countDown py-3">
          <div class="container">
            <div class="text-center madameenControl mx-auto">
              <h3 class="textMainColor time-election">
                باقى على موعد الانتخابات
              </h3>

              <div class="row g-3 align-items-center mt-3 mb-1">
                <div class="col-3">
                  <div class="inner border border-2 p-2 rounded-3 rounded">
                    <div class="textMainColor text-center fw-bold">
                      <span class="fs-2" id="days"></span> <br />
                      يوم
                    </div>
                  </div>
                </div>

                <div class="col-3">
                  <div class="inner border border-2 p-2 rounded-3 rounded">
                    <div class="textMainColor text-center fw-bold">
                      <span class="fs-2" id="hours"></span> <br />
                      ساعه
                    </div>
                  </div>
                </div>

                <div class="col-3">
                  <div class="inner border border-2 p-2 rounded-3 rounded">
                    <div class="textMainColor text-center fw-bold">
                      <span class="fs-2" id="minutes"></span> <br />
                      دقيقه
                    </div>
                  </div>
                </div>

                <div class="col-3">
                  <div class="inner border border-2 p-2 rounded-3 rounded">
                    <div class="textMainColor text-center fw-bold">
                      <span class="fs-2" id="seconds"></span> <br />
                      ثانيه
                    </div>
                  </div>
                </div>
              </div>
                <input type="hidden" id="startDate" value="{{ \Carbon\Carbon::parse($contractor->creator?->election->start_date)->format('Y-m-d') }}">
                <input type="hidden" id="startTime" value="{{ \Carbon\Carbon::parse($contractor->creator?->election->start_time)->format('H:i:s') }}">
                <input type="hidden" id="endDate" value="{{ \Carbon\Carbon::parse($contractor->creator?->election->end_date)->format('Y-m-d') }}">
                <input type="hidden" id="endTime" value="{{ \Carbon\Carbon::parse($contractor->creator?->election->end_time)->format('H:i:s') }}">
                <div id="election_start">
                    <p class="text-danger">
                        حتى تاريخ {{ \Carbon\Carbon::parse($contractor->creator?->election->start_date)->format('d/m/Y') }} , الساعة
                        {{ \Carbon\Carbon::parse($contractor->creator?->election->start_time)->format('h:i') }}
                        {{ \Carbon\Carbon::parse($contractor->creator?->election->start_time)->format('A') === 'AM' ? 'ص' : 'م' }}
                    </p>
                </div>
                <div id="election_end" class="d-none">
                    <p class="text-danger">
                        حتى تاريخ {{ \Carbon\Carbon::parse($contractor->creator?->election->end_date)->format('d/m/Y') }} , الساعة
                        {{ \Carbon\Carbon::parse($contractor->creator?->election->end_time)->format('h:i') }}
                        {{ \Carbon\Carbon::parse($contractor->creator?->election->end_time)->format('A') === 'AM' ? 'ص' : 'م' }}
                    </p>
                </div>
            </div>
          </div>
        </section>
        <div class="banner">
          <img
            src="{{$contractor->creator?->candidate ? ($contractor->creator->candidate ?? $contractor->creator->candidate[0]->banner) : "" }}"
            class="w-100 h-100"
            alt="description banner image project "
          />
        </div>
        
        <!-- ================================================================================================================= -->
        <!-- this form for update voter phone -->
        @include('dashboard.contractors.update_phone_pop_up')
        <!-- ================================================================================================================= -->
    
        <!-- </div> -->
      </section>


    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.easing.1.3.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>
    <script>
        $("#allSelected").on('change',function(){

            if($("#allSelected").val()){
                $("#sub-btn").prop('disabled',false)
            }else{
                $("#sub-btn").prop('disabled',true)
            }
        })
        $("#sub-btn").on('click',function(){
            if($("#allSelected").val()){
                event.preventDefault(); // Prevent the form from submitting immediately

var selectedOption = $('#allSelected option:selected'); // Get the selected option
var message = selectedOption.data('message'); // Get the data-message attribute

                if (confirm(message)) {
    $("#form-transfer").submit();
} else {
    return false;
            }
        }
        })
            $(document).ready(function() {
    $('.select-group').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var message = selectedOption.data('message');
        console.log($(this));


        let btn=$(this).closest('div').find('#sub-btn-g');
        if (selectedOption.val()) {
            btn.prop('disabled', false);
        } else {
            btn.prop('disabled', true);
        }

        btn.off('click').on('click', function(event) {
            event.preventDefault();
            if (confirm(message)) {
                $(this).closest('form').submit();
            } else {
                return false;
            }
        });
    });
});
    </script>
    <script>
      $(document).ready(function () {
      
        //========================================================================
          //update voter phone
          $('.voter_details').on('click', function(event) {
            event.preventDefault(); // Prevent the default button action
          });
        //========================================================================
      
          $(".js-example-basic-single").select2();
          //========================================================================
          //update voter phone
          $('#update_voter_phone_btn').on('click', function(event) {
            event.preventDefault(); // Prevent the default button action
            var voter_phone = $('#mota3ahedDetailsPhone').val();
            var voter_id    = $('#mota3ahedDetailsVoterId').val();
            updateVoterPhone(voter_phone,voter_id);
          });
          //========================================================================
          function updateVoterPhone(voter_phone,voter_id) {
            $.ajax({
              url: '/update/voter/phone',
              type: 'POST',
              data: JSON.stringify({
                _token: $('meta[name="csrf-token"]').attr('content'),
                voter_phone: voter_phone,
    				    voter_id: voter_id,
              }),
              contentType: 'application/json',
              success: function(data) {
                // console.log(data);
                sucessMessageInModel(data.message);
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
let users = [];

// abdallah
function getResultSearch() {

  let searchByNameOrNumValue =
    document.getElementById("searchByNameOrNum").value;
  let searchByFamilyValue = document.getElementById("searchByFamily").value;
  let resultSearchDate = document.getElementById("resultSearchData");
  let cartona = "";

  if (searchByFamilyValue == "" && searchByNameOrNumValue == "") {
    alert("يرجي ادخال البيانات");
  } else {
    // $(".resultSearchBtn")
    //   .attr("data-bs-toggle", "modal")
    //   .attr("data-bs-target", "#resultOfSearch");
      var url = '/search' ;
      console.log(searchByFamilyValue, searchByNameOrNumValue);
      event.preventDefault();
      let formData = new FormData($('#SearchForm')[0]);
      let data = {};
      let params = new URLSearchParams();

      formData.forEach((value, key) => {
          params.append(key, value);
      });


      axios.get(url, { params: params })
          .then(function (response) {
              console.log('Success:', response);
             users=response.data.voters;
             var con_id={{$contractor->id}}
          var route = "{{ route('ass', ':id') }}";
          var link = route.replace(':id', con_id);
          console.log(users.length);

$("#search_count").text(users.length)

             for (let i = 0; i < users.length; i++) {
        var id = users[i].id;

        cartona += `<tr>
                <td>
                  <input
                    type="checkbox"
                    class="check"
                    name="voter[]"
                    value="${id}"
                  />
                </td>
                <td >
                    <div class="row">
                      <p style="margin:0; padding:0;"class="${users[i].restricted !='فعال' ? "line" : ""}">
                                      ${users[i].name}
                      </p>
                      <span style="color:red"> ${users[i].restricted != 'فعال' ? " غير فعال" : ""} </span>
                
                      </div>  
                  <button class="btn btn-sm btn-outline-secondary p-0 m-0 search-relatives-btn" style="font-size: 10px;" data-voter-grand="${users[i].father}" type="button">البحث عن أقارب</button>
                  </td>
                <td>
                    <form action="${link}" method="POST">
                    @csrf
                    <input type="hidden" name="voter[]" value="${id}">
                    <button type="submit" class="btn btn-secondary">اضافة</button>
                  </form>
                </td>

              </tr>`;

    }
    resultSearchDate.innerHTML = cartona;

             var myModal = new bootstrap.Modal(document.getElementById('resultOfSearch'), {
  keyboard: false})
             myModal.show()

             document.querySelectorAll('.search-relatives-btn').forEach(button => {
          button.addEventListener('click', function () {
            
            searchRelatives(this.dataset.voterGrand);
          });
        });

                      })
          .catch(function (error) {
              console.error('Error:', error);
              alert('Failed to set votes.');
          });

  }

}
function searchRelatives(voterName) {
  console.log(voterName);
  
  var url = '/search';
  let params = new URLSearchParams();
	 var con_id={{$contractor->id}}
		  params.append('id', con_id);
  params.append('sibling', voterName);

  axios.get(url, { params: params })
    .then(function (response) {
      console.log('Relatives Search Success:', response);
      let relatives = response.data.voters;
     
          var route = "{{ route('ass', ':id') }}";
          var link = route.replace(':id', con_id);

      let cartona = "";
      $("#search_count").text(relatives.length)

      for (let i = 0; i < relatives.length; i++) {
        var id = relatives[i].id;

        cartona += `<tr>
          <td>
            <input
              type="checkbox"
              class="check"
              name="voter[]"
              value="${id}"
            />
          </td>
          <td >
                                <div class="row">

              <p style="margin:0; padding:0;" class="${relatives[i].restricted != 'فعال' ? "line" : ""}">
                ${relatives[i].name}
                </p>
                <span style="color:red"> ${relatives[i].restricted != 'فعال' ? " غير فعال" : ""} </span>
                </div>
              <button class="btn btn-sm btn-outline-secondary p-0 m-0 search-relatives-btn" style="font-size: 10px;" data-voter-grand="${relatives[i].father}" type="button">البحث عن أقارب</button>
          </td>
          <td>
              <form action="${link}" method="POST">
              @csrf
              <input type="hidden" name="voter[]" value="${id}">
              <button type="submit" class="btn btn-secondary">اضافة</button>
            </form>
          </td>
        </tr>`;
      }

      document.getElementById("resultSearchData").innerHTML = cartona;

      // Reattach event listeners to the new "البحث عن أقارب" buttons
      document.querySelectorAll('.search-relatives-btn').forEach(button => {
        button.addEventListener('click', function () {
          searchRelatives(this.dataset.voterGrand);
        });
      });

    })
    .catch(function (error) {
      console.error('Relatives Search Error:', error);
      alert('Failed to search for relatives.');
    });
}
$("#search").on('click',function(){
    let cartona = "";
    let resultSearchDate = document.getElementById("resultSearchData");

    var url = '/search' ;
      event.preventDefault();
      let formData = new FormData($('#Form-Siblings')[0]);
      let data = {};
      let params = new URLSearchParams();

      formData.forEach((value, key) => {
          params.append(key, value);
      });


      axios.get(url, { params: params })
          .then(function (response) {
              console.log('Success:', response);
             users=response.data.voters;
             var con_id={{$contractor->id}}
          var route = "{{ route('ass', ':id') }}";
          var link = route.replace(':id', con_id);
            console.log(users.length);
            
          $("#search_count").text(users.length)

             for (let i = 0; i < users.length; i++) {
        var id = users[i].id;

        cartona += `<tr>
                <td>
                  <input
                    type="checkbox"
                    class="check"
                    name="voter[]"
                    value="${id}"
                  />
                </td>
                <td >
                ${users[i].name}
                </td>
                <span style="color:red"> ${users[i].restricted != 'فعال' ? " غير فعال" : ""} </span>
                <td>
                    <form action="${link}" method="POST">
                    @csrf
                    <input type="hidden" name="voter[]" value="${id}">
                    <button type="submit" class="btn btn-secondary">اضافة</button>
                  </form>
                </td>

              </tr>`;

    }
    resultSearchDate.innerHTML = cartona;

             var myModal = new bootstrap.Modal(document.getElementById('resultOfSearch'), {
  keyboard: false})
             myModal.show()

                      })
          .catch(function (error) {
              console.error('Error:', error);
              alert('Failed to set votes.');
          });

})
$('button[data-bs-target="#ta7reerData"]').on("click", function () {

    let group_id=$(this).siblings('#group_id').val();
    url = "/group/"+ group_id;
    console.log(url);

    axios.get(url)
    .then(function (response) {
        console.log(response);
        $("#listNamemodal").val(response.data.group.name)
        $("#listTypemodal").val(response.data.group.type)
        let editUrl="/group-e/"+group_id;
        let deleteUrl="/group-d/"+group_id;
        $("#edit-form").attr("action",editUrl)
        $("#delete-g").attr("href", deleteUrl)
        console.log($("#delete-g").attr("href"));
    })
    .catch(function (error) {
        console.error('Error:', error);
    });
    $("#button-g").on('click',function(){
    $("#edit-form").submit();

    })



})




    </script>
  </body>
</html>
