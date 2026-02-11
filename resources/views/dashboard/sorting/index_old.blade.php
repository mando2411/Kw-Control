@extends('layouts.dashboard.app')

@section('content')
  <style>
    /* General styles */
    .btn-lock {
      z-index: 2500;
    }
  
    button {
      padding: 10px 20px;
      font-size: 18px;
      cursor: pointer;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      z-index: 3000;
      /* Ensure it's above overlay */
    }
  
    button:disabled {
      cursor: not-allowed;
      opacity: 0.6;
    }
  
    /* Lock overlay styles */
    #lock-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.9);
      display: none;
      /* Initially hidden */
      z-index: 1000;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }
  
    #lock-overlay .lock-icon {
      font-size: 100px;
      color: #fff;
      animation: lock-animation 1.5s ease-in-out infinite;
    }
  
    #lock-overlay .unlock-icon {
      font-size: 100px;
      color: #fff;
      display: none;
    }
  
    /* Lock animation */
    @keyframes lock-animation {
  
      0%,
      100% {
        transform: scale(1);
        opacity: 1;
      }
  
      50% {
        transform: scale(1.2);
        opacity: 0.7;
      }
    }
  
    /* Unlock animation */
    @keyframes unlock-animation {
      0% {
        transform: scale(1);
        opacity: 1;
      }
  
      100% {
        transform: scale(0.8) rotate(90deg);
        opacity: 0;
      }
    }
  </style>
  
  <div id="lock-overlay">
    <i class="fas fa-lock lock-icon"></i>
    <i class="fas fa-lock-open unlock-icon"></i>
  </div>
  
  <section class=" rtl">
    <div class="container mt-1">
      <div class="d-flex align-items-center pt-3">
        @if (auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('مرشح'))
        <form action="{{route('dashboard.sorting')}}" method="get" id="sorting-form" class="w-100 d-flex" style="z-index: 2500;">
          @csrf
          <label class="bg-warning py-2" for="committee">اللجنة</label>
          <select id="sorting-select" name="committee" id="committee" class="form-control">
            <option value="" hidden>أختر اللجنة</option>
            @foreach ($committees as $i=>$com )
            <option @if ($committee)
              @if ($committee->id == $com->id)
              selected
              @endif
              @endif value="{{$com->id}}"> {{$i+1}}-- {{$com->name}} ({{$com->type}})</option>
            @endforeach
          </select>
        </form>
        @endif
  
      </div>
      @if ($candidates)
  
  
      <div class="committeDetails text-center mx-auto my-3 rounded-3 overflow-hidden">
        <h3 class="bg-dark text-white border-bottom mb-0 border-danger-subtle h6 py-2">{{$committee->name}}</h3>
        <h5 class="border-bottom bg-body-secondary mb-0 py-2 border-dark-subtle">{{$committee->name}} ({{$committee->type}})</h5>
        <h5 class="border-bottom bg-body-secondary mb-0 py-2 border-dark-subtle">إجمالي الحضور باللجنة: <span class="totalAttending">{{$committee->voters()->count()}}</span></h5>
        <h5 class="border-bottom bg-body-secondary mb-0 py-2 border-dark-subtle">مجموع الأصوات المفروزة: <span class="totalSortingSound">{{$committee->candidates->sum('pivot.votes')}}</span></h5>
      </div>
      <div class="d-flex justify-content-center m-2">
        <form action="{{ route('committee.status', $committee->id ) }}" class="btn-lock" method="POST" id="CommStatus">
          @csrf
          <input type="hidden" name="status" id="status" value="{{ $committee->status }}">
          <button id="toggle-lock-button">
            <i id="icon" class="fa-solid fa-unlock"></i>
          </button>
        </form>
      </div>
      <div>
        <input type="text" placeholder="اسم المرشح" class="form-control" name="candidateName" id=""
          oninput="searchByCandidateName()"
          value="">
      </div>
  
  
      <div class="table-responsive mt-4">
        <table class="table font14  overflow-hidden rtl">
          <thead
            class="table-primary text-center border-0 border-dark border-bottom border-2">
            <tr>
              <th>#</th>
              <th>+</th>
              <th class="">الاسم</th>
              <th>-</th>
              <th>الأصوات</th>
              <th></th>
            </tr>
          </thead>
  
          <tbody class="resultcandidateName table-group-divider text-center">
            @foreach ($candidates as $index=>$can )
            <tr>
              <td>{{$index+1}}</td>
              <td>
                <form action="{{ route('dashboard.candidates.changeVotes', $can['id']) }}" method="POST" style="display:inline;">
                      @csrf
                      <input type="hidden" name="committee" value="{{$can['committee']}}" >
                      <input type="hidden" name="increment" value="1">
                      <button class="btn btn-success w-100 plusBtn sortBtn" data-message="{{'تاكيد عمليه اضافه صوت جديد'.$can['name']}}">+</button>
                  </form>
              </td>
              <td class="fullName">{{$can['name']}}</td>
              <td>
                <form action="{{ route('dashboard.candidates.changeVotes', $can['id']) }}" method="POST" style="display:inline;">
                      @csrf
                      <input type="hidden" name="committee" value="{{$can['committee']}}" >
                      <input type="hidden" name="increment" value="-1">
                      <button class="btn btn-danger w-100 minusBtn sortBtn" data-message="{{'تاكيد عمليه حذف صوت '.$can['name']}}" >-</button>
                  </form>
              </td>
              <td class="soundCount">{{$can['votes']}}</td>
              <input type="hidden" name="committee" id="committee" value="{{$can['committee']}}">
              <input type="hidden" name="id" id="candi" value="{{$can['id']}}">
              <td data-bs-toggle="modal" data-bs-target="#candidateSounds"><i class="fa fa-user-pen bg-secondary text-white p-2 rounded-2"></i></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </section>

  @if($candidates)
    <!-- Modal mota3ahedName-->
    <div class="modal modal-md rtl" id="candidateSounds" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">
              أصوات المرشح باللجنة
            </h1>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h5 class="modalCondidateName bg-secondary bg-opacity-75 py-2 pe-2 text-white"></h5>
            <div class="d-flex align-items-center mt-3">
              <label class="labelStyle" for="totalSound">مجموع الأصوات</label>
              <input type="number" name="totalSound" id="totalSound" value="" min="0" class="form-control py-1 fw-semibold ">
            </div>
            <input type="hidden" name="committee" id="Model_committee">
            <input type="hidden" name="id" id="Model_id">
            <button class="btn btn-primary w-100 my-3 editTotalSoundBtn">تعديل</button>
          </div>
    
        </div>
      </div>
    </div>
  @endif
@endsection

@push('js')
<script>
  $(document).ready(function() {
    //========================================================================
    $('#sorting-select').on('change', function() {
      $('#sorting-form').submit();
    });
    //========================================================================
    $('.sortBtn').on('click', function(event) {
        event.preventDefault(); // Prevent the default button action

        var message = $(this).data('message');
        console.log(message);

        Swal.fire({
            title: 'تاكيد ؟',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745', // Green color for the confirm button
            cancelButtonColor: '#d33', // Red color for the cancel button
            confirmButtonText: '<i class="fa fa-check"></i> نعم، متابعة', // Adding a checkmark icon
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء', // Adding an 'x' icon for cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).closest('form').submit();
            }
        });
    });
    //========================================================================
    checkLocked();
    //========================================================================
    $("#CommStatus").on('submit', function(e) {
      e.preventDefault()
      axios.post($(this).attr('action'), $(this).serialize()).then((res) => {
        $('#status').val(res.data.status);
        setTimeout(() => {
          checkLocked()
        }, 800)

      }).catch(error => {
        console.log(error);
        toastr.error(error.response.data.error ?? '{{ __('main.unexpected - error ') }}')
      }).finally();
    })
  });
  //========================================================================
  const toggleButton = document.getElementById("toggle-lock-button");
  const lockOverlay = document.getElementById("lock-overlay");
  const lockIcon = lockOverlay.querySelector(".lock-icon");
  const unlockIcon = lockOverlay.querySelector(".unlock-icon");
  let icon = $('#icon')
  let isLocked = false; // Initial status
  //========================================================================
  // Toggle lock and unlock
  function checkLocked() {
    let status = $('#status').val();
    console.log(status);

    if (status == 1) {
      unlockPage();
    } else if (status == 0) {
      lockPage();
    }

  }
  //========================================================================
  function lockPage() {
    isLocked = true;

    icon.addClass("fa-unlock")
    icon.removeClass("fa-lock")

    // Show the lock overlay and animate the lock
    lockOverlay.style.display = "flex";
    lockIcon.style.display = "block";
    lockIcon.style.animation = "lock-animation 1.5s ease-in-out infinite"
    unlockIcon.style.display = "none";
    document.body.style.overflow = 'hidden';
    $('#status').val(1);

  }
  //========================================================================
  function unlockPage() {
    isLocked = false;
    icon.addClass("fa-lock")
    icon.removeClass("fa-unlock")
    $('#status').val(0);


    // Animate the unlocking process
    lockIcon.style.animation = "none"; // Stop lock animation
    lockIcon.style.display = "none"; // Hide lock icon
    unlockIcon.style.display = "block";
    unlockIcon.style.animation = "unlock-animation 0.8s ease-in-out";
    document.body.style.overflow = '';

    // Remove overlay after animation
    setTimeout(() => {
      lockOverlay.style.display = "none";
      unlockIcon.style.animation = "none"; // Reset unlock animation
    }, 800); // Match the unlock animation duration
  }
  //========================================================================
</script>
@endpush