@props([
    'disabled' => false,
])

<input
    type="text"
    class="form-control mb-1"
    {{ $disabled ? 'disabled' : '' }}
>
