@extends('layouts.dashboard.app')

@section('content')
    <style>
        .results-pro-page {
            --rp-font-main: "Changa", "Cairo", sans-serif;
            --rp-ink: #11263d;
            --rp-muted: #5a6f87;
            --rp-surface: #ffffff;
            --rp-border: #d8e5f2;
            --rp-primary: #006c67;
            --rp-primary-soft: #d9f3f1;
            --rp-accent: #d97706;
            --rp-top: #2f855a;
            --rp-fifth: #8b5cf6;
            --rp-shadow: 0 18px 44px rgba(17, 38, 61, 0.14);
            --rp-shadow-soft: 0 10px 24px rgba(17, 38, 61, 0.1);

            position: relative;
            min-height: 100%;
            font-family: var(--rp-font-main);
            color: var(--rp-ink);
            padding: 1rem 0 2rem;
            overflow: hidden;
        }

        .results-pro-page::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(circle at 8% 16%, rgba(0, 108, 103, 0.14), transparent 42%),
                radial-gradient(circle at 92% 12%, rgba(217, 119, 6, 0.14), transparent 36%),
                linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
        }

        .results-shell {
            position: relative;
            z-index: 1;
        }

        .results-hero {
            border: 1px solid var(--rp-border);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: var(--rp-shadow);
            padding: 1rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .results-title {
            margin: 0;
            font-size: clamp(1.12rem, 2.3vw, 1.58rem);
            font-weight: 800;
            letter-spacing: 0.01em;
            color: #153655;
        }

        .results-subtitle {
            margin: 0.32rem 0 0;
            color: var(--rp-muted);
            font-size: 0.92rem;
            font-weight: 600;
        }

        .results-meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 999px;
            padding: 0.4rem 0.82rem;
            font-weight: 700;
            font-size: 0.82rem;
            color: #0e4f4b;
            background: var(--rp-primary-soft);
        }

        .results-contact {
            border-radius: 14px;
            background: linear-gradient(135deg, #fff8d8 0%, #ffefb0 100%);
            color: #7b5603;
            font-weight: 700;
            font-size: 0.86rem;
            padding: 0.55rem 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .results-open-details {
            border: 0;
            min-height: 46px;
            border-radius: 13px;
            padding: 0.42rem 1rem;
            font-size: 0.95rem;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, var(--rp-primary) 0%, #008b84 100%);
            box-shadow: 0 11px 23px rgba(0, 108, 103, 0.28);
            transition: transform 0.2s ease, filter 0.2s ease;
        }

        .results-open-details:hover {
            transform: translateY(-1px);
            filter: brightness(0.97);
            color: #fff;
        }

        .results-cards-grid {
            margin-top: 1rem;
            row-gap: 0.72rem;
        }

        .result-card-col {
            will-change: transform;
            width: 100%;
        }

        .candidate-rank-card {
            position: relative;
            border-radius: 18px;
            border: 1px solid #ecd9a4;
            background: linear-gradient(165deg, #fffdf6 0%, #fff7e2 100%);
            box-shadow: var(--rp-shadow-soft);
            min-height: 122px;
            padding: 0.82rem 0.9rem;
            text-align: right;
            overflow: hidden;
            transition: box-shadow 0.22s ease, transform 0.22s ease, border-color 0.22s ease;
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .candidate-rank-card > * {
            position: relative;
            z-index: 2;
        }

        .candidate-rank-card::before {
            content: "";
            position: absolute;
            top: -58%;
            left: -45%;
            width: 34%;
            height: 220%;
            background: linear-gradient(110deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.06) 36%,
                    rgba(255, 248, 214, 0.74) 50%,
                    rgba(255, 255, 255, 0.08) 64%,
                    rgba(255, 255, 255, 0) 100%);
            transform: translateX(-160%) skewX(-18deg);
            opacity: 0;
            pointer-events: none;
            z-index: 1;
        }

        .candidate-rank-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 102% -8%, rgba(217, 119, 6, 0.15), transparent 43%);
            pointer-events: none;
        }

        .candidate-rank-card:hover {
            transform: translateY(-2px);
            border-color: #bdd8f5;
            box-shadow: 0 16px 30px rgba(17, 38, 61, 0.14);
        }

        .candidate-rank-card.rank-gold {
            border-color: rgba(196, 144, 9, 0.66);
            background: linear-gradient(160deg, #fff8db 0%, #ffe9a7 100%);
            box-shadow: 0 14px 34px rgba(168, 120, 8, 0.26);
        }

        .candidate-rank-card.rank-gold::before {
            opacity: 0.82;
            animation: goldCardShimmer 3.6s ease-in-out infinite;
        }

        .candidate-rank-card.rank-silver {
            border-color: rgba(126, 141, 158, 0.6);
            background: linear-gradient(160deg, #f9fbfd 0%, #e6edf6 100%);
            box-shadow: 0 14px 30px rgba(89, 105, 124, 0.18);
        }

        .candidate-rank-card.rank-bronze {
            border-color: rgba(176, 109, 63, 0.62);
            background: linear-gradient(160deg, #fff2e7 0%, #f7d3b8 100%);
            box-shadow: 0 14px 30px rgba(150, 92, 53, 0.19);
        }

        .candidate-rank-card.rank-elite-4 {
            border-color: rgba(27, 109, 96, 0.44);
            background: linear-gradient(160deg, #ffffff 0%, #e5f8f5 100%);
        }

        .candidate-rank-card.rank-elite-5 {
            border-color: rgba(95, 73, 198, 0.42);
            background: linear-gradient(160deg, #ffffff 0%, #efe8ff 100%);
        }

        .candidate-rank-card.is-moving {
            box-shadow: 0 18px 42px rgba(0, 108, 103, 0.22);
        }

        .rank-frame {
            position: absolute;
            top: 0.62rem;
            left: 0.62rem;
            border-radius: 999px;
            padding: 0.2rem 0.52rem;
            border: 1px solid #cbddf3;
            background: #edf4ff;
            color: #134374;
            font-weight: 800;
            font-size: 0.74rem;
            letter-spacing: 0.01em;
            min-width: 56px;
            text-align: center;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.24rem;
        }

        .rank-medal-icon {
            display: none;
            font-size: 0.72rem;
            line-height: 1;
        }

        .candidate-rank-card.rank-gold .rank-frame {
            border-color: #b17f02;
            background: linear-gradient(135deg, #ffe79a 0%, #e5b944 100%);
            color: #5e3f00;
            box-shadow: 0 7px 14px rgba(177, 127, 2, 0.28);
        }

        .candidate-rank-card.rank-gold .rank-medal-icon {
            display: inline-flex;
            color: #7a4e00;
            text-shadow: 0 1px 0 rgba(255, 239, 188, 0.7);
        }

        .candidate-rank-card.rank-silver .rank-frame {
            border-color: #8e9cac;
            background: linear-gradient(135deg, #f4f7fb 0%, #bcc8d6 100%);
            color: #425467;
            box-shadow: 0 7px 14px rgba(99, 117, 138, 0.24);
        }

        .candidate-rank-card.rank-silver .rank-medal-icon {
            display: inline-flex;
            color: #566578;
        }

        .candidate-rank-card.rank-bronze .rank-frame {
            border-color: #a46134;
            background: linear-gradient(135deg, #fdd8bb 0%, #cf8352 100%);
            color: #5d2f12;
            box-shadow: 0 7px 14px rgba(164, 97, 52, 0.26);
        }

        .candidate-rank-card.rank-bronze .rank-medal-icon {
            display: inline-flex;
            color: #6c3718;
        }

        .candidate-rank-card.rank-elite-4 .rank-frame {
            border-color: rgba(27, 109, 96, 0.44);
            background: rgba(27, 109, 96, 0.14);
            color: #12584e;
        }

        .candidate-rank-card.rank-elite-5 .rank-frame {
            border-color: rgba(95, 73, 198, 0.44);
            background: rgba(95, 73, 198, 0.14);
            color: #432ea8;
        }

        .candidate-photo {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 8px 18px rgba(17, 38, 61, 0.16);
            object-fit: cover;
            background: #eaf1fb;
            flex-shrink: 0;
        }

        .candidate-details {
            min-width: 0;
            width: 100%;
            padding-inline-start: 0.28rem;
        }

        .candidate-name {
            margin: 0 0 0.35rem;
            font-size: 1.04rem;
            font-weight: 800;
            color: #123150;
            min-height: 0;
            line-height: 1.45;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .candidate-votes-line {
            margin: 0;
            color: #4e647f;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .candidate-meta-line {
            margin: 0.22rem 0 0;
            color: #607792;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .soundNum {
            display: inline-flex;
            min-width: 56px;
            justify-content: center;
            align-items: center;
            border-radius: 999px;
            padding: 0.24rem 0.52rem;
            margin-inline-start: 0.25rem;
            background: rgba(0, 108, 103, 0.14);
            color: #005b56;
            font-size: 0.94rem;
            font-weight: 900;
            transition: transform 0.22s ease, background-color 0.22s ease;
        }

        .soundNum.is-updated {
            transform: scale(1.08);
            background: rgba(217, 119, 6, 0.2);
            color: #8f4b05;
        }

        .results-modal .modal-content {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 20px 44px rgba(17, 38, 61, 0.2);
            overflow: hidden;
        }

        .results-modal .modal-header {
            border-bottom-color: #dbe8f8;
            background: linear-gradient(140deg, #f5f9ff 0%, #ffffff 100%);
        }

        .results-modal .table {
            margin-bottom: 0;
            font-size: 0.89rem;
        }

        .results-modal .table thead th,
        .results-modal .table thead td {
            background: #102f4f;
            color: #fff;
            font-weight: 800;
            border-color: #2a4d71;
            white-space: nowrap;
            vertical-align: middle;
            padding: 0.55rem 0.5rem;
        }

        .results-modal .table tbody td {
            border-color: #dce8f6;
            font-weight: 700;
            color: #1a3d61;
            padding: 0.46rem;
            vertical-align: middle;
        }

        .results-modal .table tbody tr:hover td {
            background: #f7fbff;
        }

        @media (max-width: 767.98px) {
            .results-hero {
                border-radius: 16px;
                padding: 0.85rem;
            }

            .results-open-details {
                width: 100%;
            }

            .candidate-rank-card {
                min-height: 112px;
                padding: 0.68rem 0.74rem;
                gap: 0.62rem;
            }

            .candidate-photo {
                width: 76px;
                height: 76px;
            }

            .candidate-name {
                font-size: 0.92rem;
            }

            .candidate-votes-line {
                font-size: 0.86rem;
            }

            .candidate-meta-line {
                font-size: 0.76rem;
            }
        }

        @keyframes goldCardShimmer {
            0% {
                transform: translateX(-160%) skewX(-18deg);
            }

            54% {
                transform: translateX(370%) skewX(-18deg);
            }

            100% {
                transform: translateX(370%) skewX(-18deg);
            }
        }
    </style>

    <section class="results-pro-page">
        <div class="container-fluid results-shell">
            <div class="results-hero rtl">
                <div>
                    <h1 class="results-title">النتائج العامة المباشرة</h1>
                    <p class="results-subtitle">تابع الآن نتائج الانتخابات مع تحديث لحظي مباشر ودقة عالية.</p>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="results-meta-badge">
                        <i class="fa-solid fa-signal"></i>
                        <span>Live Ranking</span>
                    </span>

                    <span class="results-contact">
                        <i class="fa-brands fa-whatsapp"></i>
                        55150551
                    </span>

                    <button data-bs-toggle="modal" data-bs-target="#displayData" class="results-open-details">
                        عرض تفاصيل اللجان
                    </button>
                </div>
            </div>

            <div class="modal modal-xl rtl results-modal" id="displayData" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="mb-0 fw-bold">تفاصيل أصوات اللجان</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered rtl overflow-hidden rounded-3 text-center">
                                    <thead class="border-0 border-secondary border-bottom border-2">
                                        <tr>
                                            <th class="w150"></th>
                                            @foreach ($schools as $school)
                                                <th colspan="{{ $committees->count() / 2 + 1 }}">
                                                    {{ $school->name . ' ' . '(' . $school->type . ')' }}
                                                </th>
                                            @endforeach
                                            <th></th>
                                        </tr>

                                        <tr>
                                            <td>الاسم</td>
                                            <td>مج</td>
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::MEN->value)
                                                    <td>{{ $committee->name }}</td>
                                                @endif
                                            @endforeach
                                            <td>مج</td>
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::WOMEN->value)
                                                    <td>{{ $committee->name }}</td>
                                                @endif
                                            @endforeach
                                            <td>الأصوات</td>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($candidates as $candidate)
                                            @if ($candidate->committees->isNotEmpty())
                                                <tr data-candidate-row-id="{{ $candidate->id }}">
                                                    <td>{{ $candidate->user->name }}</td>

                                                    <td class="table-primary candidate-men-total">
                                                        {{ $candidate->committees->where('type', App\Enums\Type::MEN->value)->sum('pivot.votes') }}
                                                    </td>

                                                    @foreach ($candidate->committees->where('type', App\Enums\Type::MEN->value) as $committee)
                                                        <td data-candidate-committee-id="{{ $candidate->id }}" data-committee-id="{{ $committee->id }}">{{ $committee->pivot->votes }}</td>
                                                    @endforeach

                                                    <td class="table-primary candidate-women-total">
                                                        {{ $candidate->committees->where('type', App\Enums\Type::WOMEN->value)->sum('pivot.votes') }}
                                                    </td>

                                                    @foreach ($candidate->committees->where('type', App\Enums\Type::WOMEN->value) as $committee)
                                                        <td data-candidate-committee-id="{{ $candidate->id }}" data-committee-id="{{ $committee->id }}">{{ $committee->pivot->votes }}</td>
                                                    @endforeach

                                                    <td class="table-danger candidate-total-votes">{{ $candidate->votes }}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr class="table-secondary">
                                            <td>** المجموع **</td>

                                            <td class="table-primary" id="allResultMenTotalAll">
                                                {{ $committees->where('type', App\Enums\Type::MEN->value)->flatMap(function ($committee) {
                                                        return $committee->candidates;
                                                    })->sum('pivot.votes') }}
                                            </td>

                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::MEN->value)
                                                    <td data-total-committee-id="{{ $committee->id }}">{{ $committee->candidates->sum('pivot.votes') }}</td>
                                                @endif
                                            @endforeach

                                            <td class="table-primary" id="allResultWomenTotalAll">
                                                {{ $committees->where('type', App\Enums\Type::WOMEN->value)->flatMap(function ($committee) {
                                                        return $committee->candidates;
                                                    })->sum('pivot.votes') }}
                                            </td>

                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::WOMEN->value)
                                                    <td data-total-committee-id="{{ $committee->id }}">{{ $committee->candidates->sum('pivot.votes') }}</td>
                                                @endif
                                            @endforeach

                                            <td class="table-danger" id="allResultGrandTotalAll">
                                                {{ $committees->sum(function ($committee) {
                                                    return $committee->candidates->sum('pivot.votes');
                                                }) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row rtl justify-content-center results-cards-grid" id="allResultsCardsGrid">
                @foreach ($candidates as $i => $can)
                    @php
                        $rankClass = $i === 0
                            ? 'rank-gold'
                            : ($i === 1
                                ? 'rank-silver'
                                : ($i === 2
                                    ? 'rank-bronze'
                                    : ($i === 3
                                        ? 'rank-elite-4'
                                        : ($i === 4 ? 'rank-elite-5' : ''))));
                    @endphp
                    <div class="col-12 result-card-col" data-candidate-id="{{ $can->id }}">
                        <article class="candidate-rank-card {{ $rankClass }}">
                            <div class="rank-frame">
                                <i class="fa-solid fa-medal rank-medal-icon" aria-hidden="true"></i>
                                <span class="rank-label">{{ $i + 1 }}</span>
                            </div>

                            <img src="{{ $can->user->image ?? asset('assets/admin/images/images.png') }}" class="candidate-photo" alt="candidate image" />

                            <div class="candidate-details">
                                <h6 class="candidate-name">{{ $can->user->name }}</h6>
                                <p class="candidate-votes-line">الأصوات <span class="soundNum">{{ $can->votes }}</span></p>
                                <p class="candidate-meta-line">المركز الحالي: <span class="rank-label-inline">{{ $i + 1 }}</span></p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        (function () {
            var electionId = @json((int) ($election_id ?? 0));
            var liveStatsUrl = @json(route('all.results.live-stats'));
            var fallbackTimer = null;
            var realtimeChannelName = null;
            var inFlight = false;
            var cardsGrid = document.getElementById('allResultsCardsGrid');

            function ordinal(rank) {
                if (rank % 100 >= 11 && rank % 100 <= 13) {
                    return rank + 'th';
                }

                switch (rank % 10) {
                    case 1: return rank + 'st';
                    case 2: return rank + 'nd';
                    case 3: return rank + 'rd';
                    default: return rank + 'th';
                }
            }

            function voteFromCardCol(cardCol) {
                var voteNode = cardCol.querySelector('.soundNum');
                return parseInt((voteNode ? voteNode.innerText : '0'), 10) || 0;
            }

            function markRankClasses(sortedCols) {
                var rankClasses = ['rank-gold', 'rank-silver', 'rank-bronze', 'rank-elite-4', 'rank-elite-5'];

                sortedCols.forEach(function (cardCol, index) {
                    var card = cardCol.querySelector('.candidate-rank-card');
                    var rankLabel = cardCol.querySelector('.rank-label');
                    var rankLabelInline = cardCol.querySelector('.rank-label-inline');
                    if (!card) {
                        return;
                    }

                    rankClasses.forEach(function (className) {
                        card.classList.remove(className);
                    });

                    if (index < rankClasses.length) {
                        card.classList.add(rankClasses[index]);
                    }

                    if (rankLabel) {
                        rankLabel.innerText = ordinal(index + 1);
                    }

                    if (rankLabelInline) {
                        rankLabelInline.innerText = ordinal(index + 1);
                    }
                });
            }

            function animateGridReorder(sortedCols) {
                if (!cardsGrid) {
                    return;
                }

                var firstRects = new Map();
                Array.from(cardsGrid.children).forEach(function (col) {
                    firstRects.set(col, col.getBoundingClientRect());
                });

                sortedCols.forEach(function (col) {
                    cardsGrid.appendChild(col);
                });

                sortedCols.forEach(function (col) {
                    var first = firstRects.get(col);
                    var last = col.getBoundingClientRect();

                    if (!first || !last) {
                        return;
                    }

                    var dx = first.left - last.left;
                    var dy = first.top - last.top;

                    if (dx || dy) {
                        col.style.transition = 'none';
                        col.style.transform = 'translate(' + dx + 'px, ' + dy + 'px)';

                        void col.offsetWidth;

                        col.style.transition = 'transform 760ms cubic-bezier(0.2, 0.85, 0.2, 1)';
                        col.style.transform = 'translate(0, 0)';

                        var card = col.querySelector('.candidate-rank-card');
                        if (card) {
                            card.classList.add('is-moving');
                            setTimeout(function () {
                                card.classList.remove('is-moving');
                            }, 850);
                        }
                    }
                });

                markRankClasses(sortedCols);
            }

            function updateCandidatesGridByVotes() {
                if (!cardsGrid) {
                    return;
                }

                var currentCols = Array.from(cardsGrid.children);
                var sortedCols = currentCols.slice().sort(function (a, b) {
                    return voteFromCardCol(b) - voteFromCardCol(a);
                });

                var changedOrder = sortedCols.some(function (col, idx) {
                    return col !== currentCols[idx];
                });

                if (changedOrder) {
                    animateGridReorder(sortedCols);
                } else {
                    markRankClasses(sortedCols);
                }
            }

            function applyUpdatedStyle(node) {
                if (!node) {
                    return;
                }

                node.classList.add('is-updated');
                setTimeout(function () {
                    node.classList.remove('is-updated');
                }, 420);
            }

            function applyAllResultsStats(payload) {
                if (!payload || payload.success !== true || !Array.isArray(payload.candidates)) {
                    return;
                }

                payload.candidates.forEach(function (candidate) {
                    var candidateId = parseInt(candidate.id, 10) || 0;
                    if (!candidateId) {
                        return;
                    }

                    var votes = parseInt(candidate.votes, 10) || 0;
                    var menTotal = parseInt(candidate.men_total, 10) || 0;
                    var womenTotal = parseInt(candidate.women_total, 10) || 0;

                    var cardCol = document.querySelector('[data-candidate-id="' + candidateId + '"]');
                    if (cardCol) {
                        var soundNum = cardCol.querySelector('.soundNum');
                        if (soundNum) {
                            var oldVotes = parseInt(soundNum.innerText, 10) || 0;
                            soundNum.innerText = votes;
                            if (oldVotes !== votes) {
                                applyUpdatedStyle(soundNum);
                            }
                        }
                    }

                    var row = document.querySelector('tr[data-candidate-row-id="' + candidateId + '"]');
                    if (row) {
                        var menCell = row.querySelector('.candidate-men-total');
                        var womenCell = row.querySelector('.candidate-women-total');
                        var totalCell = row.querySelector('.candidate-total-votes');

                        if (menCell) menCell.innerText = menTotal;
                        if (womenCell) womenCell.innerText = womenTotal;
                        if (totalCell) totalCell.innerText = votes;
                    }

                    var committeeVotes = candidate.committee_votes || {};
                    Object.keys(committeeVotes).forEach(function (committeeId) {
                        var committeeValue = parseInt(committeeVotes[committeeId], 10) || 0;
                        var selector = '[data-candidate-committee-id="' + candidateId + '"][data-committee-id="' + committeeId + '"]';
                        var committeeCell = document.querySelector(selector);
                        if (committeeCell) {
                            committeeCell.innerText = committeeValue;
                        }
                    });
                });

                var committeeTotals = payload.committee_totals || {};
                Object.keys(committeeTotals).forEach(function (committeeId) {
                    var totalValue = parseInt(committeeTotals[committeeId], 10) || 0;
                    var totalCells = document.querySelectorAll('[data-total-committee-id="' + committeeId + '"]');
                    totalCells.forEach(function (cell) {
                        cell.innerText = totalValue;
                    });
                });

                var menTotalAll = document.getElementById('allResultMenTotalAll');
                if (menTotalAll) {
                    menTotalAll.innerText = parseInt(payload.men_total_all, 10) || 0;
                }

                var womenTotalAll = document.getElementById('allResultWomenTotalAll');
                if (womenTotalAll) {
                    womenTotalAll.innerText = parseInt(payload.women_total_all, 10) || 0;
                }

                var grandTotalAll = document.getElementById('allResultGrandTotalAll');
                if (grandTotalAll) {
                    grandTotalAll.innerText = parseInt(payload.grand_total_all, 10) || 0;
                }

                updateCandidatesGridByVotes();
            }

            function fetchAllResultsStats() {
                if (inFlight || !electionId) {
                    return;
                }

                inFlight = true;

                axios.get(liveStatsUrl, {
                    params: {
                        election_id: electionId,
                    },
                    headers: {
                        'Accept': 'application/json',
                    }
                }).then(function (response) {
                    applyAllResultsStats(response.data || {});
                }).catch(function () {
                    // Silent fail for background refresh.
                }).finally(function () {
                    inFlight = false;
                });
            }

            function startRealtime() {
                if (!electionId) {
                    return;
                }

                fetchAllResultsStats();
                updateCandidatesGridByVotes();

                if (window.Echo && typeof window.Echo.channel === 'function') {
                    realtimeChannelName = 'results.' + electionId;
                    window.Echo.channel(realtimeChannelName).listen('.sorting.realtime.updated', function () {
                        fetchAllResultsStats();
                    });
                }

                if (fallbackTimer) {
                    clearInterval(fallbackTimer);
                }

                fallbackTimer = setInterval(function () {
                    if (!document.hidden) {
                        fetchAllResultsStats();
                    }
                }, 2500);
            }

            window.addEventListener('beforeunload', function () {
                if (realtimeChannelName && window.Echo && typeof window.Echo.leave === 'function') {
                    window.Echo.leave(realtimeChannelName);
                }

                if (fallbackTimer) {
                    clearInterval(fallbackTimer);
                }
            });

            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    fetchAllResultsStats();
                }
            });

            startRealtime();
        })();
    </script>
@endpush
