   <div class="col-lg-6">
       <div onclick="window.location = '{{ route('tour_details', $tour->translateOrDefault(app()->getLocale())->slug)}}'" class="single-destinations-list style-four">
           <div class="blur-thumb" style="background-image:url('{{ $tour->featured_image }}');">
           </div>
           <div class="details">
               <div class="tp-review-meta">
                   <i class="ic-yellow fa fa-star"></i>
                   <i class="ic-yellow fa fa-star"></i>
                   <i class="ic-yellow fa fa-star"></i>
                   <i class="ic-yellow fa fa-star"></i>
                   <i class="fa fa-star"></i>
                   <span>4.9</span>
               </div>
               @if ($tour->destinations)
                   @foreach ($tour->destinations as $des)
                       <p class="location"><img src="{{ asset('assets/site/img/icons/1.png') }}" alt="map">
                           {{ $des['title'] }}
                       </p>
                   @endforeach
               @endif
               <h4 class="title"><a href="{{ route('tour_details', $tour->translateOrDefault(app()->getLocale())->slug)}}">{{ $tour->translateOrDefault(app()->getLocale())->title }}</a></h4>
               <p class="content">{{ strip_tags($tour->overview) }}</p>
               <div class="list-price-meta">
                   <ul class="tp-list-meta d-inline-block">
                       <li><i class="fa fa-clock-o"></i> {{ $tour->duration }}</li>
                       <li><i class="fa fa-star"></i> 4.3</li>
                   </ul>
                   <div class="tp-price-meta d-inline-block">
                       <p>{{ __('main.price') }}</p>
                       <h2>{{ price_with_currency($tour->start_from) }} <span></span></h2>
                   </div>
               </div>
           </div>
       </div>
   </div>
