@extends('website.layouts.blank')

@section('content')
    <!-- This has to be here to prevent the page from jumping -->
    <!-- The next screen will be on Auth0 -->
<div class="container flex h-screen font-body">
    <div class="absolute bottom-[40rem] -left-4 w-64 h-64 bg-yellow-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
    <div class="absolute bottom-[30rem] right-0 w-72 h-72 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
    <div class="absolute bottom-[7rem] left-20 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
</div>
@endsection
@pushonce('end_of_body_waitlist')
<script>
    // Get redirect url
    let xhr = new XMLHttpRequest();
    xhr.open('GET', '{{ getServiceApi() }}/confetti-cms/auth/login', true);
    xhr.responseType = 'json';
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.onload = function () {
        let status = xhr.status;
        if (status === 200) {
            let date = new Date();
            date.setTime(date.getTime() + (10 * 60 * 1000));
            let expires = "; expires=" + date.toUTCString();
            document.cookie = "state=" + xhr.response["state"] + expires + "; path=/";
            // set cookie to redirect to this page after login
            document.cookie = "redirect_after_login=/waiting-callback; path=/";
            window.location.href = xhr.response["redirect_url"];
        } else {
            console.error(status, xhr.response);
        }
    };
    xhr.send()
    console.log("request send");
</script>
@endpushonce