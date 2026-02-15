@extends('layouts.dashboard.app')

@section('content')

<style>
    #load-more {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 50px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    #load-more:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    #load-more:active {
        transform: scale(0.95);
    }

    #load-more.loading::after {
        content: '';
        width: 20px;
        height: 20px;
        border: 3px solid white;
        border-top: 3px solid transparent;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        animation: spinner 0.8s linear infinite;
    }

    @keyframes spinner {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

</style>

<section class=" statistics rtl">
    <div class="container mt-5">
        @if (auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('مرشح'))
        <form action="{{route('dashboard.attending')}}" method="get" id="sorting-form" class="w-100 d-flex" >
            @csrf
            <label class="bg-warning py-2 bg-opacity-75 px-3"  for="committee">اللجنة</label>
            <select id="sorting-select" name="committee" id="committee" class="form-control">
              <option value="" hidden>أختر اللجنة</option>
              @foreach ($committees as $i=>$com )
              <option
                @if (request('committee') == $com->id )
                    selected
                @endif
              value="{{$com->id}}"> {{$i+1}}-- {{$com->name}} ({{$com->type}})</option>
              @endforeach
            </select>
            </form>
        @endif

        @if (request('committee') || $voters)
        @php
                $committee=App\Models\Committee::where('id',request('committee'))->first();
        @endphp
        <div class="rtl text-center d-flex justify-content-center flex-wrap my-2">
                <span class="bg-dark text-white px-4 py-2 rounded-end-3">الناخبين</span>
                <span class="bg-secondary bg-opacity-50 fw-semibold px-3 py-2 rounded-start-3 me-3">{{counts($committee->type)}}</span>
                <span class="bg-dark text-white px-4 py-2 rounded-end-3">الحضور</span>
                <span class="bg-secondary bg-opacity-50 fw-semibold px-3 py-2 rounded-start-3 me-3">

                    @if(auth()->user()->representatives()->exists())
                        {{auth()->user()->representatives()->first()->attendance}}
                    @else
                        {{$committee->voters()->count()}}
                    @endif
                </span>
        </div>

        <form action="{{route('dashboard.attending')}}" method="get" id=""  >
            <div class="d-flex align-items-center">
            <input type="text" placeholder="الاسم" class="form-control mx-1" name="name" value="" >
            <input type="hidden" name="committee" value="{{request('committee')}}">
            <input type="text" placeholder="رقم الصندوق" class="form-control mx-1" name="box" id="box" value="" >
            <button  type="submit" class="btn btn-dark mx-2 fw-semibold">عرض</button>
            <i class="fa fa-gear bg-secondary text-white p-2 rounded-2"></i>
        </div>
    </form>


        @endif
@if ($voters)


<div class="table-responsive mt-4">
    <table class="table  font14 table-striped rtl overflow-hidden">
        <thead class="table-dark">
            <tr>
                <th>القيد</th>
                <th>الاسم</th>
                <th>الحاله</th>
                <th>تغير الحاله</th>
            </tr>
        </thead>
        <tbody class="table-group-divider" id="voter-list">
            @include('dashboard.attendance.component.voters_list')
        </tbody>
    </table>

    <div class="text-center">

    </div>
</div>

@endif
    </div>
</section>



@endsection
@push('js')

<script>
    $(document).ready(function() {
            $('#sorting-select').on('change', function() {
                $('#sorting-form').submit();
            });
    });
</script>

<script>
    $(document).ready(function() {
        $('#load-more').on('click', function() {
            var nextPage = this.getAttribute('data-next-page');
            var loadMoreButton = this;

            // Serialize any form data you need to send (if applicable)
            var formData = {!! json_encode(request()->all()) !!};

            if (nextPage) {
                var $loadMoreBtn = $('#load-more');
                $loadMoreBtn.addClass('loading').prop('disabled', true);  // Add loading class and disable button

                $.ajax({
                    url: nextPage,  // Page URL for the next page
                    type: 'GET',
                    data: formData,  // Send the last request data along with the page request
                    success: function(response) {
                        var $newRows = $(response.html);
                        $newRows.hide();  // Hide new rows initially
                        $('#voter-list').append($newRows);  // Append the new rows to the voter list
                        $newRows.fadeIn(800);  // Fade in the new rows for a smooth effect

                        // Update the next page URL
                        loadMoreButton.setAttribute('data-next-page', response.nextPageUrl);

                        // Hide button if there are no more pages
                        if (!response.hasMorePages) {
                            loadMoreButton.style.display = 'none';
                        }

                        $loadMoreBtn.removeClass('loading').prop('disabled', false).text('المزيد');  // Remove loading class, re-enable button, and reset text
                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                        $loadMoreBtn.removeClass('loading').prop('disabled', false).text('المزيد');  // Remove loading class and re-enable button on error
                    }
                });
            }
        });
    });
</script>

    <script>
            $(document).ready(function() {
    $('.attend_btn').on('click', function(event) {
        event.preventDefault(); // Prevent the default button action

        var message = $(this).data('message');
        console.log(message);

        // Use SweetAlert instead of confirm
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
});

    </script>
@endpush
