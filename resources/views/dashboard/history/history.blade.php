@extends('layouts.dashboard.app')

@section('content')

<section class=" rtl">
    <div class="container-fluid mt-4">
      <div>
          <form class="row mt-4 whatsappCard align-items-center" action="{{route('dashboard.history')}}" method="GET">
              <div class="col-2 px-1 text-center"><label>التاريخ</label></div>
              <div class="col-4 px-1"><input type="date" class="form-control" name="dateFrom" id="dateFrom"></div>
              <div class="col-4 px-1"><input type="date" class="form-control mx-2" name="dateTo" id="dateTo"></div>
              <div class="col-2 px-1 text-center"><button  class="btn btn-dark">عرض</button></div>
          </form>

          <div class="table-responsive mt-4">
            <table
              class="table   rtl overflow-hidden rounded-3 text-center table-striped"
            >
              <thead
                class=" border-0 border-secondary border-bottom border-2"
              >
                <tr>
                  <th class="w150">الاسم</th>
                  <th class="w150">الوقت</th>
                  <th class="w150">الرابط</th>

                </tr>
              </thead>
              <tbody>
                @foreach ($allLogs as $log)
                <tr class="fw-semibold border-bottom border">

                    <td>
                        <p class="mb-1 fw-bold">{{ $log->causer->name ?? 'System' }}</p>
                    </td>
                    <td>
                        <span class="text-muted">{{ $log->created_at->format('Y/m/d') }}</span>
                        <p class=" mb-0">{{ $log->created_at->format('H:i:s') }}</p>
                    </td>
                    <td>
                        <textarea name="websiteAttachment" rows="1" class="form-control" readonly>{{$log->subject_type . " => "  ."  "  .$log->description }}</textarea>
                    </td>

                  </tr>

                @endforeach

              </tbody>
            </table>
          </div>


      </div>
    </div>
  </section>



@endsection
