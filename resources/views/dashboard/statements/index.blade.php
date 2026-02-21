@extends('layouts.dashboard.app')

@section('content')
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

      @include('dashboard.statements.partials.export-voters-modal', ['regionLabel' => 'المنطقة'])

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
        labels: ['{!! auth()->user()->election?->name ?? 'غير محدد' !!}'],
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

@include('dashboard.statements.partials.export-voters-modal-js')
@endpush
