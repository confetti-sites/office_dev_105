@extends('website.layouts.blank')
@guest()
    @include('website.waitlist')
@else
    @section('content')
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
            <div class="flex items-center justify-center w-full">
                <dotlottie-player
                        id="confetti-animation"
                        class="w-[500px] h-[500px] sm:w-[800px] sm:h-[800px] md:w-[1000px] md:h-[1000px] lg:w-[1200px] lg:h-[1200px] z-50"
                        style="transition: opacity 2s;"
                        src="https://lottie.host/2d8ae4d0-8e58-4146-baed-91052b10d9d2/MN7dytT7L1.lottie" background="transparent" speed="1" autoplay></dotlottie-player>
            </div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-center w-full h-screen">
                <div class="flex flex-col items-center justify-center w-full h-full max-w-2xl px-4 mx-auto text-center">
                    <div class="absolute -mt-10 right-4 w-32 h-32 md:w-[400px] md:h-[400px] lg:w-[600px] lg:h-[600px] bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
                    <div class="absolute mt-[200px] -left-4 md:left-64 w-64 h-64 lg:w-[400px] lg:h-[400px] bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
                    <div class="absolute mt-[400px] left-20 md:left-32 w-64 h-64 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">You are now on the waitlist!</h1>
                    <p class="mt-4 text-gray-700 text-base font-body font-bold">We send you an email when you can start using Confetti
                        CMS.</p>
                    <a href="/docs/philosophy"
                       class="bg-primary border-primary block z-10 w-full lg:w-1/2 rounded-md border mt-8 p-4 text-center text-base font-semibold text-white transition hover:bg-opacity-90">
                        Why We Do What We Do
                    </a>
                </div>
            </div>
        </div>
    @endsection
    @pushonce('end_of_body_waitlist_callback')
        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
        <script>
            setTimeout(() => {
                document.getElementById("confetti-animation").style.zIndex = 0;
            }, 1500);
            setTimeout(() => {
                if (document.getElementById("confetti-animation").offsetHeight < window.innerHeight) {
                    document.getElementById("confetti-animation").style.opacity = 0;
                }
            }, 5000);
        </script>
    @endpushonce
@endguest
