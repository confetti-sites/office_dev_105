@php
    $ref = isset($ref) ? 'x-ref="'.$ref.'"' : null;
    $required = isset($required) && $required ? "required" : null;
    $placeholder = isset($placeholder) ? 'placeholder="'.$placeholder.'"' : null;
    $classes = isset($classes) ? $classes : null;
@endphp

<input
    type="{{ $type }}"
    {!! $ref ? $ref : "" !!}
    {{ $required }}
    {!! $placeholder ? $placeholder : "" !!}
    class="{{$classes}} block w-full p-2 pl-5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
>
