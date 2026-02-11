@extends('layouts.dashboard.app')

@section('content')
    <section class="py-1 my-1 rtl">
        <div class="container mt-3">
            <form action="{{ route('dashboard.committee.home') }}" class="w-100" method="get" id="school-form">
                @csrf
                <label for="school">المدرسة</label>
                <select name="id" id="school" class="form-control">
                    <option value="all">كل المدارس</option>
                    @foreach ($relations['schools'] as $sch)
                        <option
                            @if (isset($request)) @if ($request->id == $sch->id)
                  selected @endif
                            @endif
                            value="{{ $sch->id }}"> {{ $sch->name }} </option>
                    @endforeach
                </select>
            </form>

            <div class="table-responsive mt-3">
                <table class="table rtl overflow-hidden rounded-3 text-center table-striped">
                    <thead class="table-primary border-0 border-secondary border-bottom border-2">
                        <tr>
                            <th></th>
                            <th>الذكور</th>
                            <th>الاناث</th>
                            <th>المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">الناخبين</td>
                            <td>{{ App\Models\Voter::where('type', 'ذكر')->count() }}</td>
                            <td>{{ App\Models\Voter::where('type', '!=', 'ذكر')->count() }}</td>
                            <td>{{ App\Models\Voter::count() }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">الحضور</td>
                            <td>{{ App\Models\Voter::where('type', 'ذكر')->where('status', 1)->count() }}</td>
                            <td>{{ App\Models\Voter::where('type', '!=', 'ذكر')->where('status', 1)->count() }}</td>
                            <td>{{ App\Models\Voter::where('status', 1)->count() }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">الباقى</td>
                            <td>{{ App\Models\Voter::where('type', 'ذكر')->where('status', 0)->count() }}</td>
                            <td>{{ App\Models\Voter::where('type', '!=', 'ذكر')->where('status', 0)->count() }}</td>
                            <td>{{ App\Models\Voter::where('status', 0)->count() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div class="controls text-center my-3">
                <button type="button" class="btn btn-outline-secondary" data-filiter="all">
                    الكل
                </button>
                <button type="button" class="btn btn-outline-primary" data-filiter="man">
                    ذكور
                </button>
                <button type="button" class="btn btn-outline-danger" data-filiter="weman">
                    أناث
                </button>
            </div>

            <div class="table-responsive">
                <table class="table rtl overflow-hidden rounded-3 text-center">
                    <thead class="table-dark border-0 border-secondary border-bottom border-2">
                        <tr>
                            <th class="w150">
                                <span class="fs-5">اللجنة</span>
                                <span class="d-block">حضور الناخبين</span>
                            </th>
                            <th class="w150">مضمون</th>
                            <th class="fs-5 w150">الملاحظات</th>
                        </tr>
                    </thead>
                    @php
                        if (isset($school)) {
                            $schools = $school;
                        } else {
                            $schools = $relations['schools'];
                        }
                    @endphp
                    <!-- مدرسة حيطان رجال -->
                    @foreach ($schools as $school)
                        <tbody class="@if ($school->type == 'ذكور') man @else weman @endif">

                            @php
                                $attends = 0;
                                $all = 0;
                                $trust = 0;
                                $attendTrust = 0;
                                foreach ($school->committees as $com) {
                                    $attends += $com->voters
                                        ->filter(function ($voter) {
                                            return $voter->status == 1;
                                        })
                                        ->count();
                                    $attendTrust += $com
                                        ->voters()
                                        ->where('status', 1)
                                        ->whereHas('contractors')
                                        ->count();
                                    $trust += $com->voters()->whereDoesntHave('contractors')->count();
                                }
                            @endphp
                            <!-- meeeeeen رجال -->
                            <tr class="@if ($school->type == 'ذكور') table-primary @else table-danger @endif ">
                                <td>
                                    {{ $school->name }}({{ $school->type }})
                                    <p class="bg-white mt-2" style="color: #000">
                                        الحضور <span class="attend">{{ $attends }}</span> من
                                        <span class="totalAttend">{{ counts($school->type) }}</span>
                                    </p>
                                </td>
                                <td>
                                    @php
                                        $voterIds = $school
                                            ->committees()
                                            ->with([
                                                'voters' => function ($query) {
                                                    $query->whereHas('contractors'); // Filters voters that have contractors
                                                },
                                            ])
                                            ->get()
                                            ->pluck('voters.*.id')
                                            ->flatten()
                                            ->unique()
                                            ->toArray();
                                    @endphp

                                    <button class="btn btn-outline-dark">{{ $trust }}</button>
                                    <a href="{{ route('dashboard.statement.show', ['voters' => $voterIds]) }}">

                                        <div class="text-success my-2">
                                            <i class="text-success fa-regular fa-check-square ms-2"></i>
                                            <span class="fw-bold text-success">{{ $attendTrust }}</span>
                                        </div>
                                    </a>

                                    <span> % <span class="precentageTrue fw-bold">
                                            @if ($trust != 0)
                                                {{ number_format(($attendTrust / $trust) * 100, 1) }}
                                            @else
                                                0
                                            @endif
                                        </span></span>
                                </td>
                                <td></td>
                            </tr>

                            @foreach ($school->committees as $com)
                                <tr>
                                    <td>
                                        {{ $com->name }}({{ $school->type }})
                                        <p class="bg-white mt-2" style="color: #000">
                                            <span class="attend">
                                                {{ $com->voters->filter(function ($voter) {
                                                        return $voter->status == 1;
                                                    })->count() }}
                                            </span> من
                                            <span class="totalAttend">
                                                {{ counts($school->type) }}
                                            </span>
                                        </p>
                                    </td>
                                    <td>
                                        @php
                                            $voterIds = $com
                                                ->voters()
                                                ->whereHas('contractors')
                                                ->get()
                                                ->pluck('id')
                                                ->toArray();
                                        @endphp
                                            <a href="{{route('voters.voters-attends',
                                            [
                                                'voters'=>$com->voters()->whereDoesntHave('contractors')->pluck('id')->toArray()
                                            ]
                                            )}}">
                                                <button class="btn btn-outline-dark">
                                                    {{ $com->voters()->whereDoesntHave('contractors')->count() }}
                                                </button>
                                            </a>
                                        <a
                                            href="
                                                @if (!empty($voterIds)) {{ route('dashboard.statement.show', ['voters' => $voterIds]) }}
                                                @else
                                                javascript:; @endif
                                            ">
                                            <div class="text-success my-2">
                                                <i class="text-success fa-regular fa-check-square ms-2"></i>
                                                <span class="fw-bold text-success">
                                                    {{ $com->voters()->where('status', 1)->whereHas('contractors')->count() }}
                                                </span>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        @foreach ($com->representatives as $rep)
                                            <p>- {{ $rep->user->name }}</p>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach

                </table>
            </div>
            <div class="table-responsive mt-3">
                <table class="table rtl overflow-hidden rounded-3 text-center table-striped"></table>
            </div>
        </div>
    </section>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('#school').on('change', function() {
                $('#school-form').submit();
            });
        });
    </script>
@endpush
