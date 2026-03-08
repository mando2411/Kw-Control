@extends('layouts.dashboard.app')

@section('content')
    <style>
        .item {
            position: relative; /* Required for transform */
            transition: transform 1.5s linear; /* Smooth animation */
        }
        .bg-success {
            background-color: #afead6 !important;
        }
        
        .bg-info {
            background-color: #fff2bf !important;
        }
    </style>
    <!-- Main Section for User Results -->
    <section class="userResult">
        <div class="container mt-4">
            <div class="rtl">
                <!-- Button to open the modal for displaying committee details -->
                <button data-bs-toggle="modal" data-bs-target="#displayData" class="btn btn-secondary w-100 mb-3 fs-5">
                    عرض تفاصيل اللجان
                </button>
                <!-- Header with contact information -->
                <h5 class="bg-warning py-1 pe-5 rounded-2 d-flex justify-content-center align-items-center">
                    <span class="fs-5">نظام كنترول الانتخابات</span>
                    <span class="text-danger p-2 fs-6">
                        للاستفسار
                        <i class="fa-brands fa-whatsapp"></i>
                        55150551
                    </span>
                </h5>
            </div>

            <!-- Modal to display committee details -->
            <div class="modal modal-xl rtl" id="displayData" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <!-- Modal Header with Close Button -->
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- Modal Body containing a responsive table -->
                        <div class="modal-body">
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered rtl overflow-hidden rounded-3 text-center">
                                    <!-- Table Headers -->
                                    <thead class="table-dark border-0 border-secondary border-bottom border-2">
                                        <tr>
                                            <!-- Empty Header Cell -->
                                            <th class="w150"></th>
                                            <!-- Loop through each school and display school name and type -->
                                            @foreach ($schools as $school)
                                                <th colspan="{{ $committees->count() / 2 + 1 }}">
                                                    {{ $school->name . ' ' . '(' . $school->type . ')' }}
                                                </th>
                                            @endforeach
                                            <th></th>
                                        </tr>
                                        <tr class="table-dark">
                                            <!-- Column Headers for Candidate Information and Total Votes -->
                                            <td>الأسم</td>
                                            <td>مج</td>
                                            <!-- Display Committees of Type MEN -->
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::MEN->value)
                                                    <td>{{ $committee->name }}</td>
                                                @endif
                                            @endforeach
                                            <td>مج</td>
                                            <!-- Display Committees of Type WOMEN -->
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::WOMEN->value)
                                                    <td>{{ $committee->name }}</td>
                                                @endif
                                            @endforeach
                                            <td>الأصوات</td>
                                        </tr>
                                    </thead>

                                    <!-- Table Body with Candidate and Vote Information -->
                                    <tbody>
                                        @foreach ($candidates as $candidate)
                                            @if ($candidate->committees->isNotEmpty())
                                                <tr data-candidate-row-id="{{ $candidate->id }}">
                                                    <!-- Candidate Name -->
                                                    <td>{{ $candidate->user->name }}</td>

                                                    <!-- Total Votes for MEN Committees -->
                                                    <td class="table-primary candidate-men-total">
                                                        {{ $candidate->committees->where('type', App\Enums\Type::MEN->value)->sum('pivot.votes') }}
                                                    </td>

                                                    <!-- Individual Votes for MEN Committees -->
                                                    @foreach ($candidate->committees->where('type', App\Enums\Type::MEN->value) as $committee)
                                                        <td data-candidate-committee-id="{{ $candidate->id }}" data-committee-id="{{ $committee->id }}">{{ $committee->pivot->votes }}</td>
                                                    @endforeach

                                                    <!-- Total Votes for WOMEN Committees -->
                                                    <td class="table-primary candidate-women-total">
                                                        {{ $candidate->committees->where('type', App\Enums\Type::WOMEN->value)->sum('pivot.votes') }}
                                                    </td>

                                                    <!-- Individual Votes for WOMEN Committees -->
                                                    @foreach ($candidate->committees->where('type', App\Enums\Type::WOMEN->value) as $committee)
                                                        <td data-candidate-committee-id="{{ $candidate->id }}" data-committee-id="{{ $committee->id }}">{{ $committee->pivot->votes }}</td>
                                                    @endforeach

                                                    <!-- Candidate's Overall Total Votes -->
                                                    <td class="table-danger candidate-total-votes">{{ $candidate->votes }}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <!-- Optional Total Row for All Committees -->
                                        <tr class="table-secondary">
                                            <td>** المجموع **</td>

                                            <!-- Total Votes for All MEN Committees -->
                                            <td class="table-primary" id="allResultMenTotalAll">
                                                {{ $committees->where('type', App\Enums\Type::MEN->value)->flatMap(function ($committee) {
                                                        return $committee->candidates;
                                                    })->sum('pivot.votes') }}
                                            </td>

                                            <!-- Individual Votes for Each MEN Committee -->
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::MEN->value)
                                                    <td data-total-committee-id="{{ $committee->id }}">{{ $committee->candidates->sum('pivot.votes') }}</td>
                                                @endif
                                            @endforeach

                                            <!-- Total Votes for All WOMEN Committees -->
                                            <td class="table-primary" id="allResultWomenTotalAll">
                                                {{ $committees->where('type', App\Enums\Type::WOMEN->value)->flatMap(function ($committee) {
                                                        return $committee->candidates;
                                                    })->sum('pivot.votes') }}
                                            </td>

                                            <!-- Individual Votes for Each WOMEN Committee -->
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::WOMEN->value)
                                                    <td data-total-committee-id="{{ $committee->id }}">{{ $committee->candidates->sum('pivot.votes') }}</td>
                                                @endif
                                            @endforeach

                                            <!-- Grand Total of All Votes -->
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

            <div class="row rtl pt-5 justify-content-center">
                @foreach ($candidates as $i => $can)
                    <div class="col-lg-2 col-md-3 col-sm-3 col-3 m-1 border p-1
                    @if ($i<4)
                    bg-success 
                    @elseif ($i==4)
                    bg-info
                    @endif
                    "

                        data-candidate-id="{{ $can->id }}">
                        <div class="text-center position-relative">
                            <!-- Candidate Image -->
                            <figure class="mb-1">
                                <img src="{{ $can->user->image ?? asset('assets/admin/images/images.png') }}" class="rounded-circle" alt="user image"
                                    style="height: 100px; width:100px" />
                            </figure>
                            <!-- Candidate Name and Vote Count -->
                            <figcaption>
                                <h6 style="color:#000 !important">{{ $can->user->name }}</h6>
                                <p>الاصوات <span class="soundNum fw-bold">{{ $can->votes }}</span></p>
                            </figcaption>
                            <!-- Candidate Position Number -->
                            <div class="numLayer position-absolute top-0 end-0">
                                <div class="rounded-circle bg-dark text-white p-2 py-1">{{ $i + 1 }}</div>
                            </div>
                        </div>
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

            function updateCandidatesGridByVotes() {
                const candidatesContainer = document.querySelector('.row.rtl.pt-5.justify-content-center');
                if (!candidatesContainer) {
                    return;
                }

                let cards = Array.from(candidatesContainer.children);

                const sortedCards = cards.slice().sort((a, b) => {
                    const votesA = parseInt((a.querySelector('.soundNum') || {}).innerText || '0', 10);
                    const votesB = parseInt((b.querySelector('.soundNum') || {}).innerText || '0', 10);
                    return votesB - votesA;
                });

                sortedCards.forEach((card, index) => {
                    const rankElement = card.querySelector('.numLayer .rounded-circle');
                    if (rankElement) {
                        rankElement.innerText = index + 1;
                    }
                });

                highlightTopCards(sortedCards);
                candidatesContainer.innerHTML = '';
                sortedCards.forEach((card) => candidatesContainer.appendChild(card));
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

                    var card = document.querySelector('[data-candidate-id="' + candidateId + '"]');
                    if (card) {
                        var soundNum = card.querySelector('.soundNum');
                        if (soundNum) {
                            soundNum.innerText = votes;
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
                }, 4000);
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

        function updateCandidates(candidate) {
            // Deprecated: kept for backward compatibility if called externally.
            if (!candidate || !candidate.id) {
                return;
            }
        
            const candidatesContainer = document.querySelector('.row.rtl.pt-5.justify-content-center');
            if (!candidatesContainer) {
                console.error("Candidates container not found!");
                return;
            }
        
            let cards = Array.from(candidatesContainer.children);
            let candidateFound = false;
    
            // Update the vote count for the specific candidate
            cards.forEach((card) => {
                const cardId = parseInt(card.dataset.candidateId, 10);
                if (cardId === candidate.id) {
                    const voteElement = card.querySelector(".soundNum");
                    if (voteElement) {
                        voteElement.innerText = candidate.votes;
                        candidateFound = true;
                    }
                }
            });

            if (!candidateFound) {
                console.warn("Candidate not found in DOM:", candidate.id);
                return;
            }


            // Get the initial positions of the cards
            const initialPositions = cards.map((card) => {
                const rect = card.getBoundingClientRect();
                return { card, top: rect.top, left: rect.left };
            });
        
            // Sort the cards by votes in descending order
            const sortedCards = cards.slice().sort((a, b) => {
                const votesA = parseInt(a.querySelector(".soundNum").innerText, 10);
                const votesB = parseInt(b.querySelector(".soundNum").innerText, 10);
                return votesB - votesA; // Descending order
            });
        
            // Calculate the target positions after sorting
            const targetPositions = sortedCards.map((card, index) => {
                const { top, left } = initialPositions[index];
                return { card, top, left };
            });

            highlightTopCards(sortedCards);
        
            // Apply transform to animate each card to its new position
            initialPositions.forEach((pos, index) => {
                const targetPos = targetPositions.find((t) => t.card === pos.card);
                if (targetPos) {
                    const deltaX = targetPos.left - pos.left;
                    const deltaY = targetPos.top - pos.top;
                    pos.card.style.transition = "transform 1.5s linear"; // Change 1.5s to a higher value
                    pos.card.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
                }
            });

            // Reset transforms and reorder DOM after animation completes
            setTimeout(() => {
                sortedCards.forEach((card, index) => {
                    card.style.transition = "";
                    card.style.transform = "";
                    const rankElement = card.querySelector(".numLayer .rounded-circle");
                    if (rankElement) {
                        rankElement.innerText = index + 1; // Update the rank
                    }
                });
        
                // Reorder the DOM elements to match the visual order
                candidatesContainer.innerHTML = "";
                sortedCards.forEach((card) => candidatesContainer.appendChild(card));
            }, 3000); // Match the transition duration
        }

        function highlightTopCards(sortedCards) {
            // Remove previous highlights
            sortedCards.forEach((card) => {
                card.classList.remove("bg-success", "bg-info");
            });
        
            // Add bg-success to the top 4 cards
            sortedCards.slice(0, 4).forEach((card) => {
                card.classList.add("bg-success");
            });
        
            // Add bg-info to the 5th card
            if (sortedCards[4]) {
                sortedCards[4].classList.add("bg-info");
            }
        }
    </script>
@endpush