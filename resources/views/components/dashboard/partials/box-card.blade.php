@props([
    'title',
    'count'=>0,
    'icon'=>'box',
    'color'=>'secondary'

])
<div class="col-xxl-3 col-md-6 xl-50">
    <div class="card o-hidden widget-cards">
        <div class="{{ $color }}-box card-body">
            <div class="media static-top-widget align-items-center">
                <div class="icons-widgets">
                    <div class="align-self-center text-center">
                        <i data-feather="{{ $icon }}" class="font-secondary"></i>
                    </div>
                </div>
                <div class="media-body media-doller">
                    <span class="m-0">{{ $title }}</span>
                    <h3 class="mb-0">
                        <span @class(['counter'=> $count > 4])>{{ $count }}</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
