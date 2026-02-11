@foreach ($voters as $i => $voter)
    <tr class="
            @if ($voter->status == 1) table-success @endif">
        <input type="hidden" id="voter_id" value="{{ $voter->id }}">
        <td>
            <input type="checkbox" class="check" name="voter[]" value="{{ $voter->id }}" />
        </td>
        <td>
            <p class="mb-0 fw-bold text-dark">
                {{ $voter->name }}
            </p>
            <span class="text-secondary">{{ $voter->age }} عام</span>
            @if ($voter->status == 1)
                <p class=" my-1">
                    <span><i class="fa fa-check-square text-success ms-1"></i>تم التصويت </span>
                    <span>{{ $voter->updated_at->format('Y/m/d') }}</span>
                </p>
            @endif
        </td>
        <td>{{ $voter->family?->name }}</td>
        <td>
            <div class="d-flex row justify-content-center align-items-center">
                <form class="d-flex justify-content-center align-items-center" id="Siblings-form-{{ $i }}"
                    action="{{ route('dashboard.statement.query') }}" method="GET">
                    <i data-bs-toggle="modal" data-bs-target="#voterData"
                        class="cPointer fa fa-info p-2 px-3 rounded-circle bg-warning"
                        data-target-id={{ $voter->id }}></i>
                    <select name="siblings" id="siblings-{{ $i }}"
                        class="form-control py-1 mt-1 me-2 text-center w60" onchange="Search(this)">
                        <option value="" disabled>أختر</option>
                        <option value="" hidden class="">
                            بحث
                        </option>
                        <option class=""
                            value='{{ json_encode(['father' => $voter->father], JSON_UNESCAPED_UNICODE) }}'>أقارب من
                            الدرجة الاولى</option>
                        <option class=""
                            value="{{ json_encode(['grand' => $voter->grand], JSON_UNESCAPED_UNICODE) }}">أقارب من
                            الدرجة التانية</option>
                        <option class="" value="بحث موسع">بحث موسع</option>
                    </select>
                </form>
            </div>
        </td>
    </tr>
@endforeach
