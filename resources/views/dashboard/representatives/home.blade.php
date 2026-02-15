@extends('layouts.dashboard.app')

@section('content')
<section class="-5 rtl">
    <x-dashboard.partials.message-alert />

    <div class="container">
<div class="madameenControl">
    <div class="d-flex align-items-center pt-3">
        <form action="{{route('dashboard.rep-home')}}" class="w-100" method="get" id="school-form">
            @csrf
            <label class="labelStyle" for="school">المدرسة</label>
          <select name="id" id="school" class="form-control py-1">
              <option value="all">كل المدارس</option>
              @foreach ( $relations['schools'] as $sch )
              <option
                  @if (isset($request))
                  @if ($request->id == $sch->id)
                  selected
              @endif

                  @endif
              value="{{$sch->id}}"> {{$sch->name}} </option>
              @endforeach
            </select>
      </form>
    </div>
    <div class="moreSearch my-3">
      <div role="button" class="btn btn-primary">+ اضافة مندوب جديد</div>
      <form class="description d-none p-2" action="{{route('dashboard.representatives.store')}}" method="POST" >
          @csrf

          <div class="d-flex align-items-center mb-1">
          <label class="labelStyle"  for="name">الاسم</label>
          <input
            type="text"
            class="form-control py-1"
            placeholder="Name"
            name="name"
            id="name"
          />
        </div>
        <div class="d-flex align-items-center mb-1">
          <label class="labelStyle" for="phone">الهاتف</label>
          <input
            type="text"
            class="form-control py-1"
            placeholder="phone"
            name="phone"
            id="phone"
          />
        </div>

        <div class="d-flex align-items-center mb-1">
          <label class="labelStyle" for="committee_id">اللجنة</label>
          <select
            name="committee_id"
            id="committee_id"
            class="form-control py-1"
          >
            <option hidden>أختر اللجنة</option>
            @foreach ( $relations['committees'] as $com )
                  <option value="{{$com->id}}"> {{$com->name . ' ' . "(" . $com->type .")"}} </option>
            @endforeach
          </select>
        </div>

        <button class="btn btn-primary w-50 mx-auto d-block">أضافة</button>
      </form>
    </div>
</div>

      <div class="table-responsive">
        <table class="table overflow-hidden rtl">
          <thead
            class="table-primary text-center border-0 border-dark border-bottom border-2"
          >
            <tr>
              <th>اللجنة</th>
              <th>المندوب</th>
              <th>الهاتف</th>
              <th>أدوات</th>
            </tr>
          </thead>

          <tbody class="table-group-divider text-center">
            @php
                if(isset($school)){
                    $schools=$school;
                }else{
                    $schools=$relations['schools'];
                }
            @endphp
          @foreach ( $schools as  $school )

          <td colspan="4" class="bg-secondary bg-opacity-50 text-center">
            {{$school->name}}

        </td>
            @forelse ( $school->committees as $com )
                @forelse ($com->users() as $user )
                <tr>
                    <input type="hidden" id="user_id" value="{{$user['id']}}">
                    <td id="com_name" >{{$com->name}}</td>
                    <td id="user_name">{{$user['name']}}</td>
                    <td id="user_phone">{{$user['phone']}}</td>
                    <td
                      data-bs-toggle="modal"
                      data-bs-target="#settingSearchName"
                    >
                      <i class="fa fa-gear bg-dark text-white p-2 rounded-3"></i>
                    </td>
                  </tr>
                  @empty

                  <tr>

                    <td colspan="4" class="bg-danger bg-opacity-50 text-center">
                        لا يوجد مندوبين في هذه المدرسه

                      </td>
                      </tr>
                @endforelse

                @empty

                  <tr>

                <td colspan="4" class="bg-danger bg-opacity-50 text-center">
                    لا يوجد لجان في هذه المدرسه

                  </td>
                  </tr>
            @endforelse
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- search name setting-->
  <div
    class="modal rtl"
    id="settingSearchName"
    tabindex="-1"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">بحث بالأسم</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id" >
            <div class="d-flex align-items-center mb-1">
              <label class="labelStyle" class="" for="nameMandoob">الاسم </label>
              <input
                type="text"
                class="form-control py-1"
                name="name"
                id="nameMandoob"
                min=""
              />
            </div>
            <div class="d-flex align-items-center mb-1">
              <label class="labelStyle" class="" for="phoneMandoob"> الهاتف </label>
              <input
                type="text"
                class="form-control py-1"
                name="phone"
                id="phoneMandoob"
                min="0"
              />
            </div>
            <div class="d-flex align-items-center mb-1">
              <label class="labelStyle" class="" for="committeMandoob">اللجنة </label>
              <select
                name="committee_id"
                id="committeMandoob"
                class="form-control py-1"
              >
              <option hidden value="">أختر اللجنة</option>
              @foreach ( $relations['committees'] as $com )
                    <option value="{{$com->id}}"> {{$com->name}} </option>
              @endforeach
              </select>
            </div>
            <button id="submit-form" class="btn btn-primary w-100">تعديل</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning">
            اعادة تعيين الرقم السرى
          </button>
          <button type="button" class="btn btn-danger">حذف المستخدم</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('js')


<script>
    $(document).ready(function() {
            $('#school').on('change', function() {
                $('#school-form').submit();
            });
    });
</script>
<script>
    (function(){
        $('td[data-bs-target="#settingSearchName"]').on("click", function () {
    console.log($(this).siblings("#user_name").text());
    $("#nameMandoob").val($(this).siblings("#user_name").text());
    $("#phoneMandoob").val($(this).siblings("#user_phone").text());
    let currentSoundCount = $(this).siblings(".soundCount");
    let theCurrentSoundBefourAdd = currentSoundCount.text();
    $(".editTotalSoundBtn").on("click", function () {
        var modelId = $("#Model_id").val();
        var votes = $("#totalSound").val();
        var committee = $("#Model_committee").val();
        var url = 'candidates/' + modelId + '/votes/set';
        var data = {
            votes: votes,
            committee: committee
        };
        axios.post(url, data)
            .then(function (response) {
                console.log('Success:', response);
                if (confirm('Votes set successfully! Click OK to refresh the page.')) {
                    window.location.reload();
                }
                        })
            .catch(function (error) {
                console.error('Error:', error);
                alert('Failed to set votes.');
            });
    });
  });
    })
</script>
@endpush
