@extends('layouts.dashboard.app')

@section('content')
<section class=" statistics rtl">
    <div class="container mt-4">
        <form action="{{route('dashboard.statistics')}}" method="GET" id="Stat_form" class="madameenControl">
            @csrf
            <div class="d-flex align-items-center mb-1">
                <label class="labelStyle" for="committees">اللجنة</label>
                <select name="committee" id="committees" class="form-control">
                    <option value="">كل اللجان</option>
                    @foreach ($relations['committees'] as $comm )
                        <option value="{{$comm->id}}">
                            {{$comm->name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-center mb-1">
                <label class="labelStyle" for="familySearch">العائلة</label>
                <select name="family" id="familySearch" class="form-control">
                    <option value="">كل العوائل</option>
                    @foreach ($relations['families'] as $fam )
                        <option value="{{$fam->id}}">
                            {{$fam->name}}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="moreSearch mt-3">
                <div role="button" class=" btn btn-primary w-100">خيارات أكثر للبحث</div>
                <div role="form" class="description d-none border p-2">
                    <div class="d-flex align-items-center mt-1">
                    <select name="userName" id="userName" class="form-control">
                        <option value="">الكل</option>
                        <option value="">المقيد</option>
                        <option value="">غير المقيد</option>
                    </select>
                    <input type="number" class="form-control mx-2" placeholder="العمر من" value="" name="ageFrom" id="ageFrom" min="15">
                    <input type="number" class="form-control" placeholder="العمر الى" value="" name="ageTo" id="ageTo" min="15">
                </div>
                <div class="d-flex align-items-center mt-1">
                    <input type="text" class="form-control " placeholder="الفخذ" value="" name="fa5zSearch" id="fa5zSearch" >
                    <input type="text" class="form-control mx-2" placeholder="الفرع" value="" name="brannchSearch" id="brannchSearch" >
                    <input type="text" class="form-control" placeholder="البطن" value="" name="batenSearch" id="batenSearch" >
                </div>
                <div class="d-flex align-items-center mt-1">
                    <input type="number" class="form-control " placeholder="code1" value="" name="code1Search" id="code1Search" >
                    <input type="number" class="form-control mx-2" placeholder="code2" value="" name="code2Search" id="code2Search" >
                    <input type="number" class="form-control" placeholder="code3" value="" name="code3Search" id="code3Search" >
                </div>
                </div>
            </div>

            <div class="d-flex mb-1">
                <div class="d-flex align-items-center mb-1 w-50">
                    <label class="labelStyle" for="fromHour">من الساعة</label>
                    <input type="time" class="form-control py-1" id="fromHour">

                </div>
                <div class="d-flex align-items-center mb-1 w-50">
                    <label class="labelStyle" for="toHour">الى الساعة</label>
                    <input type="time" class="form-control py-1" id="toHour">

                </div>

            </div>

            <button type="submit" class=" btn btn-secondary w-100">بحث</button>

        </form>

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
                        <td>الناخبين</td>
                        <td class="table-primary">{{ $data['allVoters']->male_voters ?? 0}}</td>
                        <td class="table-danger">{{ $data['allVoters']->female_voters ?? 0}}</td>
                        <td>{{ $data['allVoters']->total_voters ?? 0}}</td>
                    </tr>
                    <tr>
                        <td>الحضور</td>
                        <td class="table-primary">{{ $data['allVoters']->male_voters_present ?? 0}}</td>
                        <td class="table-danger">{{ $data['allVoters']->female_voters_present ?? 0}}</td>
                        <td>{{ $data['allVoters']->total_present ?? 0}}</td>
                    </tr>
                    <tr>
                        <td>الباقى</td>
                        <td class="table-primary">{{ $data['allVoters']->male_voters_absent ?? 0}}</td>
                        <td class="table-danger">{{ $data['allVoters']->female_voters_absent ?? 0}}</td>
                        <td>{{ $data['allVoters']->total_absent ?? 0}}</td>
                    </tr>
                        <tr>
                        <td>نسبة الحضور</td>
                        <td class="table-primary">{{ number_format($data['allVoters']->male_attendance_percentage, 2) }}%</td>
                        <td class="table-danger">{{ number_format($data['allVoters']->female_attendance_percentage, 2) }}%</td>
                        <td>{{ number_format($data['allVoters']->overall_attendance_percentage, 2) }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

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
                        <td class="table-primary" >{{ $data['allTrustVoters']->male_voters ?? 0}}</td>
                        <td class="table-danger">{{ $data['allTrustVoters']->female_voters ?? 0}}</td>
                        <td>{{ $data['allTrustVoters']->total_voters ?? 0}}</td>
                    </tr>
                    <tr>
                        <td>الحضور</td>
                        <td class="table-primary" >{{ $data['allTrustVoters']->male_voters_present ?? 0}}</td>
                        <td class="table-danger">{{ $data['allTrustVoters']->female_voters_present ?? 0 }}</td>
                        <td>{{ $data['allTrustVoters']->total_present ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>الباقى</td>
                        <td class="table-primary" >{{ $data['allTrustVoters']->male_voters_absent ?? 0 }}</td>
                        <td class="table-danger">{{ $data['allTrustVoters']->female_voters_absent ?? 0 }}</td>
                        <td>{{ $data['allTrustVoters']->total_absent ?? 0}}</td>
                    </tr>
                        <tr>
                        <td>نسبة الحضور</td>
                        <td class="table-primary" >{{ number_format($data['allTrustVoters']->male_attendance_percentage, 2) }}%</td>
                        <td class="table-danger">{{ number_format($data['allTrustVoters']->female_attendance_percentage, 2) }}%</td>
                        <td>{{ number_format($data['allTrustVoters']->overall_attendance_percentage, 2) }}%</td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </div>
</section>


@endsection
