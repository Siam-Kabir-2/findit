@props(['name', 'bag' => null])

@php
    $messages = $bag
        ? $errors->{$bag}->get($name)
        : $errors->get($name);
@endphp

@if (! empty($messages))
    <p {{ $attributes->merge(['class' => 'field-error']) }} role="alert">{{ $messages[0] }}</p>
@endif
