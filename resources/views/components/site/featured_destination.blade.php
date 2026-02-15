    <div class="single-upconing-card">
        <div class="shadow" style="background-image: url({{ $des->featured_image }});">
            <img src="{{ asset('assets/site/img/tour/2.png') }}" alt="flag">
        </div>

        <div class="content text-center">
            <h3 class="title"><a href="{{ route('des-details', $des->translateOrDefault()->slug ?? $des->id ) }}">
                    {{ $des->translateOrDefault(app()->getLocale())->title }}</a>
            </h3>
            {!! $des->translateOrDefault(app()->getLocale())->description !!}

        </div>
    </div>
