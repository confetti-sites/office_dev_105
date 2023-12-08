@php
    $isAdmin = str_starts_with(request()->uri(), '/admin');
@endphp
@if(request()->uri() === '/auth/callback')
    @include('auth.callback')
@elseif(str_starts_with(request()->uri(), '/admin'))
    @include('admin.index')
@else
    @include('view.index')
@endif
