@extends('layouts.dashboard.app')

@section('content')
    @php
        $totalVoters = (int) ($summary['total_voters'] ?? 0);
        $totalAttended = (int) ($summary['total_attended'] ?? 0);
        $totalRemaining = (int) ($summary['total_remaining'] ?? 0);
        $maleVoters = (int) ($summary['male_voters'] ?? 0);
        $femaleVoters = (int) ($summary['female_voters'] ?? 0);
        $maleAttended = (int) ($summary['male_attended'] ?? 0);
        $femaleAttended = (int) ($summary['female_attended'] ?? 0);
        $attendanceRate = $totalVoters > 0 ? round(($totalAttended / $totalVoters) * 100, 1) : 0;
    @endphp

    <style>
        .committee-overview-page {
            --cop-font-main: "Changa", "Cairo", sans-serif;
            --cop-ink: #102d49;
            --cop-muted: #58708b;
            --cop-border: #d6e4f3;
            --cop-shadow: 0 14px 34px rgba(16, 45, 73, 0.12);
            --cop-soft-shadow: 0 9px 23px rgba(16, 45, 73, 0.09);
            --cop-men: #1169a5;
            --cop-women: #b42362;

            position: relative;
            min-height: 100%;
            padding: 1rem 0 2rem;
            font-family: var(--cop-font-main);
            color: var(--cop-ink);
            overflow: hidden;
        }

        .committee-overview-page::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(circle at 10% 14%, rgba(17, 105, 165, 0.16), transparent 36%),
                radial-gradient(circle at 91% 8%, rgba(180, 35, 98, 0.14), transparent 34%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        }

        .committee-overview-shell {
            position: relative;
            z-index: 1;
        }

        .committee-hero {
            border: 1px solid var(--cop-border);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: var(--cop-shadow);
            padding: 1rem 1.1rem;
        }

        .committee-hero-title {
            margin: 0;
            font-size: clamp(1.06rem, 2.2vw, 1.56rem);
            font-weight: 900;
            color: #113558;
        }

        .committee-hero-subtitle {
            margin: 0.34rem 0 0;
            color: var(--cop-muted);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .committee-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 999px;
            padding: 0.42rem 0.82rem;
            font-size: 0.8rem;
            font-weight: 800;
            color: #11598d;
            background: #e5f2ff;
        }

        .committee-filter-panel {
            margin-top: 0.9rem;
            border: 1px solid var(--cop-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: var(--cop-soft-shadow);
            padding: 0.84rem;
        }

        .committee-filter-label {
            margin-bottom: 0.35rem;
            color: #1f4568;
            font-size: 0.82rem;
            font-weight: 800;
        }

        .committee-filter-select {
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid #c7d9ed;
            font-size: 0.9rem;
            font-weight: 700;
            color: #193f62;
        }

        .committee-filter-select:focus {
            border-color: #5aa3da;
            box-shadow: 0 0 0 0.2rem rgba(90, 163, 218, 0.15);
        }

        .kpi-grid {
            margin-top: 0.9rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .kpi-card {
            border: 1px solid #d6e4f3;
            border-radius: 14px;
            background: linear-gradient(160deg, #ffffff 0%, #f6fbff 100%);
            box-shadow: var(--cop-soft-shadow);
            padding: 0.75rem 0.8rem;
            text-align: center;
        }

        .kpi-label {
            display: block;
            color: #4e6782;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .kpi-value {
            display: block;
            margin-top: 0.25rem;
            color: #143d62;
            font-size: 1.14rem;
            font-weight: 900;
        }

        .kpi-card.is-success {
            border-color: rgba(13, 148, 136, 0.4);
            background: linear-gradient(160deg, #ecfffb 0%, #ddfaf4 100%);
        }

        .kpi-card.is-success .kpi-value {
            color: #0b6e66;
        }

        .kpi-card.is-warning {
            border-color: rgba(217, 119, 6, 0.4);
            background: linear-gradient(160deg, #fff8ef 0%, #feecd8 100%);
        }

        .kpi-card.is-warning .kpi-value {
            color: #8b4b13;
        }

        .committee-type-controls {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .type-filter-btn {
            border: 1px solid #bfd4eb;
            border-radius: 999px;
            background: #fff;
            color: #1c476c;
            font-size: 0.82rem;
            font-weight: 800;
            padding: 0.38rem 0.9rem;
            transition: all 0.18s ease;
        }

        .type-filter-btn:hover {
            transform: translateY(-1px);
        }

        .type-filter-btn.is-active {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, #1d74b5 0%, #0f5c95 100%);
            box-shadow: 0 8px 16px rgba(15, 92, 149, 0.23);
        }

        .school-block {
            margin-top: 0.9rem;
            border: 1px solid var(--cop-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: var(--cop-soft-shadow);
            overflow: hidden;
        }

        .school-head {
            padding: 0.78rem 0.88rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.55rem;
            border-bottom: 1px solid #deebf7;
            background: #f7fbff;
        }

        .school-head.is-men {
            background: linear-gradient(135deg, rgba(17, 105, 165, 0.14), rgba(17, 105, 165, 0.03));
        }

        .school-head.is-women {
            background: linear-gradient(135deg, rgba(180, 35, 98, 0.14), rgba(180, 35, 98, 0.03));
        }

        .school-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 900;
            color: #133a5f;
        }

        .school-subtitle {
            margin: 0.22rem 0 0;
            color: #56708d;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .school-badges {
            display: flex;
            gap: 0.42rem;
            flex-wrap: wrap;
        }

        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.28rem;
            border-radius: 999px;
            padding: 0.26rem 0.66rem;
            font-size: 0.76rem;
            font-weight: 900;
            border: 1px solid #cbddf1;
            background: #fff;
            color: #1f4a70;
        }

        .school-badge.is-attended {
            border-color: rgba(13, 148, 136, 0.32);
            color: #0d6d64;
            background: rgba(13, 148, 136, 0.12);
        }

        .school-badge.is-remaining {
            border-color: rgba(217, 119, 6, 0.32);
            color: #8d4f17;
            background: rgba(217, 119, 6, 0.12);
        }

        .school-body {
            padding: 0.7rem;
        }

        .committee-table {
            margin-bottom: 0;
            font-size: 0.86rem;
        }

        .committee-table thead th {
            background: #0f2f4f;
            color: #fff;
            font-weight: 800;
            border-color: #27496f;
            padding: 0.58rem 0.45rem;
            white-space: nowrap;
        }

        .committee-table tbody td {
            border-color: #dce9f7;
            padding: 0.5rem 0.45rem;
            vertical-align: middle;
            color: #1a4166;
            font-weight: 700;
        }

        .committee-table tbody tr:hover td {
            background: #f8fbff;
        }

        .mini-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            min-width: 70px;
            padding: 0.2rem 0.52rem;
            font-size: 0.74rem;
            font-weight: 900;
            border: 1px solid #ccdef1;
            background: #eef5ff;
            color: #17456f;
        }

        .committee-reps {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.25rem;
        }

        .committee-rep-pill {
            border-radius: 999px;
            border: 1px solid #d1e2f4;
            padding: 0.18rem 0.56rem;
            font-size: 0.72rem;
            font-weight: 800;
            color: #2a567e;
            background: #f5faff;
        }

        .report-link {
            color: #0b7b5d;
            font-weight: 900;
            text-decoration: none;
        }

        .report-link:hover {
            text-decoration: underline;
        }

        .empty-state {
            margin-top: 0.9rem;
            border: 1px dashed #bcd4ec;
            border-radius: 14px;
            background: #f7fbff;
            text-align: center;
            color: #5d7894;
            font-weight: 700;
            padding: 1.2rem 0.7rem;
        }

        @media (max-width: 991.98px) {
            .kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }

            .committee-overview-page {
                padding-top: 0.7rem;
            }
        }
    </style>

    <section class="committee-overview-page rtl">
        <div class="container-fluid committee-overview-shell">
            <div class="committee-hero">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h1 class="committee-hero-title">لوحة متابعة اللجان والمدارس</h1>
                        <p class="committee-hero-subtitle">هيكل واضح لمتابعة إجماليات الحضور وتفاصيل كل لجنة بطريقة سهلة وسريعة.</p>
                    </div>
                    <span class="committee-filter-chip">
                        <i class="fa-solid fa-building-columns"></i>
                        Committees Overview
                    </span>
                </div>

                <div class="committee-filter-panel">
                    <form action="{{ route('dashboard.committee.home') }}" class="w-100" method="get" id="school-form">
                        <label for="school" class="committee-filter-label">تصفية حسب المدرسة</label>
                        <select name="id" id="school" class="form-control committee-filter-select">
                            <option value="all" @selected((string) ($request->id ?? 'all') === 'all')>كل المدارس</option>
                            @foreach ($relations['schools'] as $sch)
                                <option value="{{ $sch->id }}" @selected((int) ($request->id ?? 0) === (int) $sch->id)>
                                    {{ $sch->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="kpi-grid">
                <article class="kpi-card">
                    <span class="kpi-label">إجمالي الناخبين</span>
                    <span class="kpi-value">{{ $totalVoters }}</span>
                </article>
                <article class="kpi-card is-success">
                    <span class="kpi-label">إجمالي الحضور</span>
                    <span class="kpi-value">{{ $totalAttended }}</span>
                </article>
                <article class="kpi-card is-warning">
                    <span class="kpi-label">إجمالي المتبقي</span>
                    <span class="kpi-value">{{ $totalRemaining }}</span>
                </article>
                <article class="kpi-card">
                    <span class="kpi-label">حضور الذكور</span>
                    <span class="kpi-value">{{ $maleAttended }} / {{ $maleVoters }}</span>
                </article>
                <article class="kpi-card">
                    <span class="kpi-label">حضور الإناث</span>
                    <span class="kpi-value">{{ $femaleAttended }} / {{ $femaleVoters }}</span>
                </article>
                <article class="kpi-card">
                    <span class="kpi-label">نسبة الإنجاز العامة</span>
                    <span class="kpi-value">{{ $attendanceRate }}%</span>
                </article>
            </div>

            <div class="committee-type-controls" id="typeFilterControls">
                <button type="button" class="type-filter-btn is-active" data-filter="all">الكل</button>
                <button type="button" class="type-filter-btn" data-filter="men">ذكور</button>
                <button type="button" class="type-filter-btn" data-filter="women">إناث</button>
            </div>

            @forelse ($schools as $school)
                @php
                    $isMen = (string) $school->type === 'ذكور';
                    $schoolFilterType = $isMen ? 'men' : 'women';

                    $schoolVoters = $school->committees->pluck('voters')->flatten();
                    $schoolTotalVoters = $schoolVoters->count();
                    $schoolAttended = $schoolVoters->where('status', 1)->count();
                    $schoolRemaining = max($schoolTotalVoters - $schoolAttended, 0);
                    $schoolRate = $schoolTotalVoters > 0 ? round(($schoolAttended / $schoolTotalVoters) * 100, 1) : 0;

                    $schoolNoContractorCount = 0;
                    $schoolContractorAttendedCount = 0;
                    $schoolContractorAttendedIds = [];

                    foreach ($school->committees as $schoolCommittee) {
                        $schoolNoContractorCount += $schoolCommittee->voters()->whereDoesntHave('contractors')->count();
                        $attendedContractorVoters = $schoolCommittee->voters()->where('status', 1)->whereHas('contractors')->pluck('id')->toArray();
                        $schoolContractorAttendedCount += count($attendedContractorVoters);
                        $schoolContractorAttendedIds = array_merge($schoolContractorAttendedIds, $attendedContractorVoters);
                    }

                    $schoolContractorAttendedIds = array_values(array_unique($schoolContractorAttendedIds));
                @endphp

                <article class="school-block" data-gender="{{ $schoolFilterType }}">
                    <header class="school-head {{ $isMen ? 'is-men' : 'is-women' }}">
                        <div>
                            <h2 class="school-title">{{ $school->name }} ({{ $school->type }})</h2>
                            <p class="school-subtitle">متابعة تفصيلية للجان المدرسة والمندوبين ونسب الإنجاز.</p>
                        </div>

                        <div class="school-badges">
                            <span class="school-badge">إجمالي: {{ $schoolTotalVoters }}</span>
                            <span class="school-badge is-attended">حضور: {{ $schoolAttended }}</span>
                            <span class="school-badge is-remaining">متبقي: {{ $schoolRemaining }}</span>
                            <span class="school-badge">نسبة: {{ $schoolRate }}%</span>
                            <span class="school-badge">بدون متعهد: {{ $schoolNoContractorCount }}</span>
                            <span class="school-badge">حضور المضمون: {{ $schoolContractorAttendedCount }}</span>
                        </div>
                    </header>

                    <div class="school-body">
                        <div class="table-responsive">
                            <table class="table committee-table text-center align-middle">
                                <thead>
                                    <tr>
                                        <th>اللجنة</th>
                                        <th>الحضور</th>
                                        <th>بدون متعهد</th>
                                        <th>حضور المضمون</th>
                                        <th>المندوبون</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($school->committees as $com)
                                        @php
                                            $committeeTotal = $com->voters->count();
                                            $committeeAttended = $com->voters->where('status', 1)->count();
                                            $committeeNoContractor = $com->voters()->whereDoesntHave('contractors')->count();
                                            $committeeContractorAttendedIds = $com->voters()->where('status', 1)->whereHas('contractors')->pluck('id')->toArray();
                                            $committeeContractorAttendedCount = count($committeeContractorAttendedIds);
                                        @endphp

                                        <tr>
                                            <td>
                                                <strong>{{ $com->name }}</strong>
                                                <div class="mt-1">
                                                    <span class="mini-chip">{{ $committeeAttended }} / {{ $committeeTotal }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $committeeAttended }}</td>
                                            <td>
                                                <a href="{{ route('voters.voters-attends', ['voters' => $com->voters()->whereDoesntHave('contractors')->pluck('id')->toArray()]) }}" class="report-link">
                                                    {{ $committeeNoContractor }}
                                                </a>
                                            </td>
                                            <td>
                                                @if (!empty($committeeContractorAttendedIds))
                                                    <a href="{{ route('dashboard.statement.show', ['voters' => $committeeContractorAttendedIds]) }}" class="report-link">
                                                        {{ $committeeContractorAttendedCount }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($com->representatives->isNotEmpty())
                                                    <div class="committee-reps">
                                                        @foreach ($com->representatives as $rep)
                                                            <span class="committee-rep-pill">{{ $rep->user?->name ?? 'غير متاح' }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">لا يوجد</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if (!empty($schoolContractorAttendedIds))
                            <div class="mt-2 text-end">
                                <a href="{{ route('dashboard.statement.show', ['voters' => $schoolContractorAttendedIds]) }}" class="report-link">
                                    عرض تقرير حضور المضمون للمدرسة بالكامل
                                </a>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-state">لا توجد بيانات لجان متاحة للعرض حسب الفلتر الحالي.</div>
            @endforelse
        </div>
    </section>
@endsection

@push('js')
    <script>
        (function () {
            var schoolSelect = document.getElementById('school');
            var schoolForm = document.getElementById('school-form');

            if (schoolSelect && schoolForm) {
                schoolSelect.addEventListener('change', function () {
                    schoolForm.submit();
                });
            }

            var filterButtons = Array.from(document.querySelectorAll('#typeFilterControls .type-filter-btn'));
            var schoolBlocks = Array.from(document.querySelectorAll('.school-block'));

            function applyGenderFilter(filterType) {
                schoolBlocks.forEach(function (block) {
                    var blockType = String(block.getAttribute('data-gender') || '');
                    var shouldShow = filterType === 'all' || blockType === filterType;
                    block.style.display = shouldShow ? '' : 'none';
                });

                filterButtons.forEach(function (button) {
                    var buttonFilter = String(button.getAttribute('data-filter') || 'all');
                    button.classList.toggle('is-active', buttonFilter === filterType);
                });
            }

            filterButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var filterType = String(button.getAttribute('data-filter') || 'all');
                    applyGenderFilter(filterType);
                });
            });

            applyGenderFilter('all');
        })();
    </script>
@endpush
