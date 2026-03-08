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
.flip-icon {
    display: inline-block;
    animation: flip-icon 0.6s forwards;
}

@keyframes flip-icon {
    0% {
        transform: rotateY(0deg);
    }
    50% {
        transform: rotateY(90deg);
        opacity: 0;
    }
    100% {
        transform: rotateY(180deg);
        opacity: 1;
    }
}

    </style>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

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
                                                <tr>
                                                    <!-- Candidate Name -->
                                                    <td>{{ $candidate->user->name }}</td>

                                                    <!-- Total Votes for MEN Committees -->
                                                    <td class="table-primary">
                                                        {{ $candidate->committees->where('type', App\Enums\Type::MEN->value)->sum('pivot.votes') }}
                                                    </td>

                                                    <!-- Individual Votes for MEN Committees -->
                                                    @foreach ($candidate->committees->where('type', App\Enums\Type::MEN->value) as $committee)
                                                        <td>{{ $committee->pivot->votes }}</td>
                                                    @endforeach

                                                    <!-- Total Votes for WOMEN Committees -->
                                                    <td class="table-primary">
                                                        {{ $candidate->committees->where('type', App\Enums\Type::WOMEN->value)->sum('pivot.votes') }}
                                                    </td>

                                                    <!-- Individual Votes for WOMEN Committees -->
                                                    @foreach ($candidate->committees->where('type', App\Enums\Type::WOMEN->value) as $committee)
                                                        <td>{{ $committee->pivot->votes }}</td>
                                                    @endforeach

                                                    <!-- Candidate's Overall Total Votes -->
                                                    <td class="table-danger">{{ $candidate->votes }}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <!-- Optional Total Row for All Committees -->
                                        <tr class="table-secondary">
                                            <td>** المجموع **</td>

                                            <!-- Total Votes for All MEN Committees -->
                                            <td class="table-primary">
                                                {{ $committees->where('type', App\Enums\Type::MEN->value)->flatMap(function ($committee) {
                                                        return $committee->candidates;
                                                    })->sum('pivot.votes') }}
                                            </td>

                                            <!-- Individual Votes for Each MEN Committee -->
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::MEN->value)
                                                    <td>{{ $committee->candidates->sum('pivot.votes') }}</td>
                                                @endif
                                            @endforeach

                                            <!-- Total Votes for All WOMEN Committees -->
                                            <td class="table-primary">
                                                {{ $committees->where('type', App\Enums\Type::WOMEN->value)->flatMap(function ($committee) {
                                                        return $committee->candidates;
                                                    })->sum('pivot.votes') }}
                                            </td>

                                            <!-- Individual Votes for Each WOMEN Committee -->
                                            @foreach ($committees as $committee)
                                                @if ($committee->type == App\Enums\Type::WOMEN->value)
                                                    <td>{{ $committee->candidates->sum('pivot.votes') }}</td>
                                                @endif
                                            @endforeach

                                            <!-- Grand Total of All Votes -->
                                            <td class="table-danger">
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

            <div class="table-responsive mt-4">
                <table class="table table-bordered rtl overflow-hidden rounded-3 text-center">
                    <!-- Table Headers -->
                    <thead class="table-dark border-0 border-secondary border-bottom border-2">
                        <tr>
                            <!-- Empty Header Cell -->
                            <!-- Loop through each school and display school name and type -->
                            @foreach ($schools as $school)
                                <th colspan="{{ $school->committees()->count() }}" class="@if ($school->committees()->count() == 0)
                                    d-none
                                @endif">
                                    {{ $school->name . ' ' . '(' . $school->type . ')' }}
                                </th>
                            @endforeach
                        </tr>
                        <tr class="table-dark">
                            <!-- Display Committees of Type MEN -->
                            @foreach ($committees as $committee)
                                @if ($committee->type == App\Enums\Type::MEN->value)
                                    <td>{{ $committee->name }}</td>
                                @endif
                            @endforeach
                            <!-- Display Committees of Type WOMEN -->
                            @foreach ($committees as $committee)
                                @if ($committee->type == App\Enums\Type::WOMEN->value)
                                    <td>{{ $committee->name }}</td>
                                @endif
                            @endforeach
                        </tr>
                    </thead>

                    <!-- Table Body with Candidate and Vote Information -->
                    <tbody>
                        @foreach ($committees as $committee)
                        @if ($committee->type == App\Enums\Type::MEN->value)
                            <td data-committee-id="{{ $committee->id }}">
                                @if ($committee->status == 1)
                                <i class="bi bi-x-circle-fill fs-4 text-danger"></i>
                                @else
                                <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                                @endif

                            </td>
                        @endif
                    @endforeach
                    <!-- Display Committees of Type WOMEN -->
                    @foreach ($committees as $committee)
                        @if ($committee->type == App\Enums\Type::WOMEN->value)
                            <td data-committee-id="{{ $committee->id }}">
                                @if ($committee->status == 1)
                                <i class="bi bi-x-circle-fill fs-4 text-danger"></i>
                                @else
                                <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                                @endif
                            </td>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </section>
@endsection
@push('js')
    <script>
        var legacyAnimationTimer = null;
        var legacyAnimationInProgress = false;
        var legacyAnimationQueued = false;

        function getCandidatesContainer() {
            return document.querySelector('.row.rtl.pt-5.justify-content-center');
        }

        function setCandidateVoteInDom(candidateId, votes) {
            var candidatesContainer = getCandidatesContainer();
            if (!candidatesContainer) {
                return false;
            }

            var card = candidatesContainer.querySelector('[data-candidate-id="' + candidateId + '"]');
            if (!card) {
                return false;
            }

            var voteElement = card.querySelector('.soundNum');
            if (!voteElement) {
                return false;
            }

            var oldVotes = parseInt(voteElement.innerText, 10) || 0;
            var nextVotes = parseInt(votes, 10) || 0;
            if (oldVotes === nextVotes) {
                return false;
            }

            voteElement.innerText = nextVotes;
            return true;
        }

        function scheduleLegacyRankingAnimation() {
            if (legacyAnimationInProgress) {
                legacyAnimationQueued = true;
                return;
            }

            if (legacyAnimationTimer) {
                clearTimeout(legacyAnimationTimer);
                legacyAnimationTimer = null;
            }

            legacyAnimationTimer = setTimeout(function () {
                runLegacyRankingAnimation();
            }, 120);
        }

        function runLegacyRankingAnimation() {
            var candidatesContainer = getCandidatesContainer();
            if (!candidatesContainer) {
                return;
            }

            var cards = Array.from(candidatesContainer.children || []);
            if (cards.length <= 1) {
                return;
            }

            var sortedCards = cards.slice().sort(function (a, b) {
                var votesA = parseInt((a.querySelector('.soundNum') || {}).innerText || '0', 10) || 0;
                var votesB = parseInt((b.querySelector('.soundNum') || {}).innerText || '0', 10) || 0;
                return votesB - votesA;
            });

            var orderChanged = sortedCards.some(function (card, index) {
                return card !== cards[index];
            });

            highlightTopCards(sortedCards);

            if (!orderChanged) {
                sortedCards.forEach(function (card, index) {
                    var rankElement = card.querySelector('.numLayer .rounded-circle');
                    if (rankElement) {
                        rankElement.innerText = index + 1;
                    }
                });
                return;
            }

            legacyAnimationInProgress = true;

            var firstRects = new Map();
            cards.forEach(function (card) {
                firstRects.set(card, card.getBoundingClientRect());
            });

            sortedCards.forEach(function (card) {
                candidatesContainer.appendChild(card);
            });

            sortedCards.forEach(function (card, index) {
                var first = firstRects.get(card);
                var last = card.getBoundingClientRect();
                var rankElement = card.querySelector('.numLayer .rounded-circle');

                if (rankElement) {
                    rankElement.innerText = index + 1;
                }

                if (!first || !last) {
                    return;
                }

                var dx = first.left - last.left;
                var dy = first.top - last.top;

                if (dx || dy) {
                    card.style.transition = 'none';
                    card.style.transform = 'translate(' + dx + 'px, ' + dy + 'px)';

                    void card.offsetWidth;

                    card.style.transition = 'transform 900ms cubic-bezier(0.2, 0.85, 0.2, 1)';
                    card.style.transform = 'translate(0, 0)';
                }
            });

            setTimeout(function () {
                sortedCards.forEach(function (card) {
                    card.style.transition = '';
                    card.style.transform = '';
                });

                legacyAnimationInProgress = false;

                if (legacyAnimationQueued) {
                    legacyAnimationQueued = false;
                    scheduleLegacyRankingAnimation();
                }
            }, 980);
        }

        function updateCandidates(candidate) {
    if (!candidate || !candidate.id) {
        return;
    }

    var changed = setCandidateVoteInDom(candidate.id, candidate.votes);
    if (changed) {
        scheduleLegacyRankingAnimation();
    }
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

    <script>
        var legacyRealtimeConfig = @json([
            'key' => config('broadcasting.connections.pusher.key'),
            'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        ]);
        var legacyResultsLiveStatsUrl = @json(route('all.results.live-stats'));
        var legacyResultsElectionId = @json((int) (auth()->user()->election_id ?? 0));

        function updateCommittee(committee) {
    let iconElement = $(`[data-committee-id='${committee.id}'] i`); // Select the icon inside

    // Add the flip animation class
    iconElement.addClass('flip-icon');

    // Wait for the first half of the flip to complete, then update the icon
    setTimeout(() => {
        // Determine the new icon based on status
        let newIconClass = committee.status == 1
            ? 'bi bi-x-circle-fill fs-4 text-danger'
            : 'bi bi-check-circle-fill fs-4 text-success';

        // Update the icon's class
        iconElement.attr('class', newIconClass);
    }, 300); // Half the duration of the flip animation (0.6s / 2)

    // Remove the flip animation class after the animation completes
    setTimeout(() => {
        iconElement.removeClass('flip-icon');
    }, 600); // Full duration of the animation
}

        function handleLegacyVoteEvent(payload) {
            var candidate = payload && payload.message ? payload.message : payload;
            if (!candidate || !candidate.id) {
                return;
            }

            updateCandidates(candidate);
        }

        function handleLegacyCommitteeEvent(payload) {
            var committee = payload && payload.committee ? payload.committee : payload;
            if (!committee || !committee.id) {
                return;
            }

            updateCommittee(committee);
        }

        function subscribeLegacyRealtime() {
            // Prefer Echo so this page follows the same runtime used by newer realtime pages.
            if (window.Echo && typeof window.Echo.channel === 'function') {
                window.Echo.channel('votes').listen('.my-event', function (payload) {
                    handleLegacyVoteEvent(payload || {});
                });

                window.Echo.channel('committee').listen('.event', function (payload) {
                    handleLegacyCommitteeEvent(payload || {});
                });

                return;
            }

            var key = String((legacyRealtimeConfig && legacyRealtimeConfig.key) || '');
            if (!key || typeof Pusher === 'undefined') {
                return;
            }

            try {
                Pusher.logToConsole = true;
                var pusherClient = new Pusher(key, {
                    cluster: (legacyRealtimeConfig && legacyRealtimeConfig.cluster) || 'mt1',
                    forceTLS: true,
                });

                pusherClient.subscribe('votes').bind('my-event', function (payload) {
                    handleLegacyVoteEvent(payload || {});
                });

                pusherClient.subscribe('committee').bind('event', function (payload) {
                    handleLegacyCommitteeEvent(payload || {});
                });
            } catch (error) {
                console.error('Legacy realtime subscription failed:', error);
            }
        }

        function syncLegacyResultsFromLiveStats(payload) {
            if (!payload || payload.success !== true || !Array.isArray(payload.candidates)) {
                return;
            }

            var hasAnyChange = false;

            payload.candidates.forEach(function (candidate) {
                if (!candidate || !candidate.id) {
                    return;
                }

                var changed = setCandidateVoteInDom(
                    parseInt(candidate.id, 10) || 0,
                    parseInt(candidate.votes, 10) || 0
                );

                hasAnyChange = hasAnyChange || changed;
            });

            if (hasAnyChange) {
                scheduleLegacyRankingAnimation();
            }
        }

        function startLegacyResultsFallbackPolling() {
            if (!legacyResultsElectionId || typeof axios === 'undefined') {
                return;
            }

            setInterval(function () {
                if (document.hidden) {
                    return;
                }

                axios.get(legacyResultsLiveStatsUrl, {
                    params: {
                        election_id: legacyResultsElectionId,
                    },
                    headers: {
                        'Accept': 'application/json',
                    }
                }).then(function (response) {
                    syncLegacyResultsFromLiveStats(response.data || {});
                }).catch(function () {
                    // Silent fallback polling.
                });
            }, 2500);
        }

        subscribeLegacyRealtime();
        startLegacyResultsFallbackPolling();

    </script>
@endpush
