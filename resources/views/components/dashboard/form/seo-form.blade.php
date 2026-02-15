<div class="card tab2-card">
    <div class="card-body needs-validation">
        <x-dashboard.form.language-multi-tab-card tab-id="seo" title="SEO">
            @foreach(config('translatable.supported_locales') as $localKey => $local)
                <div @class(['tab-pane fade', 'active show' => $localKey == config('app.locale')])
                     id="{{ 'seo-'.$localKey }}" role="tabpanel"
                     aria-labelledby="{{  'seo-'.$localKey }}-tab">
                    <x-dashboard.form.input-text :value="$seo->translateOrNew($localKey)?->meta_title"
                                                 error-key="seo.{{$localKey}}.meta_title"
                                                 name="seo[{{$localKey}}][meta_title]" id="seo.{{$localKey}}-meta-title"
                                                 label-title="Meta Title"/>
                    <x-dashboard.form.input-text :value="$seo->translateOrNew($localKey)?->meta_description"
                                                 error-key="seo.{{$localKey}}.meta_description"
                                                 name="seo[{{$localKey}}][meta_description]"
                                                 id="seo.{{$localKey}}-meta-description"
                                                 label-title="Meta Description"/>
                    <x-dashboard.form.input-text :value="$seo->translateOrNew($localKey)?->meta_keywords"
                                                 error-key="seo.{{$localKey}}.meta_keywords"
                                                 name="seo[{{$localKey}}][meta_keywords]"
                                                 id="seo.{{$localKey}}-meta-keywords" label-title="Meta Keywords"
                                                 class="tags-input"/>
                    <x-dashboard.form.input-text :value="$seo->translateOrNew($localKey)?->og_title"
                                                 error-key="seo.{{$localKey}}.og_title"
                                                 name="seo[{{$localKey}}][og_title]"
                                                 id="seo.{{$localKey}}-og-title"
                                                 label-title="OpenGraph Title"/>
                    <x-dashboard.form.input-text :value="$seo->translateOrNew($localKey)?->og_description"
                                                 error-key="seo.{{$localKey}}.og_description"
                                                 name="seo[{{$localKey}}][og_description]"
                                                 id="seo.{{$localKey}}-og-description"
                                                 label-title="OpenGraph Description"/>
                </div>
            @endforeach


        </x-dashboard.form.language-multi-tab-card>

        <x-dashboard.form.media
            name="seo[og_image]"
            title="Add Open Graph Image"
            :images="$seo->og_image"
        ></x-dashboard.form.media>
        <x-dashboard.form.submit-button/>
    </div>
</div>

