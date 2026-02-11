@props([
    'labelTitle'=> '',
    'id'=> Str::random(),
    'name',
    'value',
    'required',
    'required',
    'resourceName' => '',
    'resourceDesc' => 'Enable',
])
<div class="form-group row">
    <label for="{{ Str::kebab($id) }}" class="col-xl-3 col-md-4">{{ $labelTitle }}</label>
    <div class="col-xl-8 col-md-7">
        <div class="checkbox checkbox-primary ">
            <input type="checkbox"
                   id="{{ Str::kebab($id) }}"
                   name="{{ $name }}"
                   data-original-title="{{ $labelTitle }}"
                   title="{{ $labelTitle }}"
                   @checked($value ?? old($errorKey))
                   @isset($required) required @endif >
            <label for="{{ Str::kebab($id) }}">
                @if(!empty($resourceName))
                    {{$resourceDesc}} This {{ $resourceName }}
                @else
                    {{$resourceDesc}}
                @endif
            </label>
        </div>
    </div>
</div>
