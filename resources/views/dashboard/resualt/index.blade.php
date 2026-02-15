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
        Pusher.logToConsole = true;
        var pusher = new Pusher('abd70d55894bcd00f5cb', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('votes');
        channel.bind('my-event', function(e) {
            updateCandidates(e.message);
        });

        function updateCandidates(candidate) {
    console.log("Received candidate data:", candidate);

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

    <script>
           Pusher.logToConsole = true;
        var pusher = new Pusher('abd70d55894bcd00f5cb', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('committee');
        channel.bind('event', function(e) {
            updateCommittee(e.committee)
        });
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

    </script>
@endpush
