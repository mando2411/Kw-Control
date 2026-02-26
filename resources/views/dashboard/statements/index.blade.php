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

      @include('dashboard.partials.sm-export-modal', [
        'includeSearchId' => true,
        'searchIdInputId' => 'smExportSearchId',
        'searchIdInputName' => 'family_id',
        'regionLabel' => 'المنطقة'
      ])

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
                    <button data-bs-toggle="modal" data-bs-target="#smExportModal" class="btn btn-outline-dark sm-open-export-family" data-family-id="{{$family['id']}}">كشوف</button>
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

<script>
  (function () {
    const exportForm = document.getElementById('smExportForm');
    const exportType = document.getElementById('smExportType');
    const familyInput = document.getElementById('smExportSearchId');

    if (!exportForm || !exportType || !familyInput) {
      return;
    }

    document.querySelectorAll('.sm-open-export-family').forEach((button) => {
      button.addEventListener('click', function () {
        familyInput.value = String(button.getAttribute('data-family-id') || '');
      });
    });

    document.querySelectorAll('.sm-export-action').forEach((button) => {
      button.addEventListener('click', function () {
        const actionType = button.value;
        const familyId = String(familyInput.value || '');

        if (!familyId) {
          toastr.warning('تعذر تحديد العائلة المطلوبة لاستخراج الكشف.');
          return;
        }

        exportType.value = actionType;

        const submitBtn = button;
        submitBtn.disabled = true;

        const formData = new FormData(exportForm);
        const queryData = {};
        formData.forEach((value, key) => {
          if (Object.prototype.hasOwnProperty.call(queryData, key)) {
            if (!Array.isArray(queryData[key])) {
              queryData[key] = [queryData[key]];
            }
            queryData[key].push(value || '');
          } else {
            queryData[key] = value || '';
          }
        });

        axios.get(exportForm.action, {
          params: queryData,
          responseType: actionType === 'Excel' || actionType === 'PDF' ? 'blob' : 'json',
        })
        .then(async (res) => {
          if (actionType === 'Excel' || actionType === 'PDF') {
            const contentType = String(res?.headers?.['content-type'] || '').toLowerCase();
            if (contentType.includes('text/html') || contentType.includes('application/json')) {
              const errorText = await res.data.text();
              toastr.error('تعذر استخراج الملف. حاول مرة أخرى.');
              console.error('Export unexpected payload:', errorText);
              return;
            }

            const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = fileUrl;
            link.setAttribute('download', actionType === 'Excel' ? 'Voters.xlsx' : 'Voters.pdf');
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(fileUrl);
            return;
          }

          if (actionType === 'Send' && res.data?.Redirect_Url) {
            window.location.href = res.data.Redirect_Url;
            return;
          }

          const newTab = window.open();
          if (newTab && typeof res.data === 'string') {
            newTab.document.open();
            newTab.document.write(res.data);
            newTab.document.close();
          }
        })
        .catch((error) => {
          console.error(error);
          toastr.error(error.response?.data?.error || 'حدث خطأ غير متوقع');
        })
        .finally(() => {
          submitBtn.disabled = false;
        });
      });
    });
  })();
</script>
@endpush
