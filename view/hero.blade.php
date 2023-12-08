@php($hero = section('homepage/hero')->label('Hero'))
<div class="flex items-center justify-center bg-white dark:bg-gray-900 pt-[70px]">
    <div
      class="container py-28 md:flex md:items-center"
    >
      <div class="md:w-1/2" x-intersect="$el.classList.add('slide-in-top')">
        <h1 class="text-6xl font-bold leading-tight dark:text-white text-gray-900">
          <span>{!! $hero->text('title')->min(1)->max(20)->default('Confetti CMS') !!}</span>
        </h1>
        <p class="mt-4 text-xl dark:text-white text-gray-900">
          A developer first framework to build your websites blazing fast
        </p>
        <div class="flex">
          <div class="mt-8">
            <a
              href="/docs"
              class="inline-block bg-primary text-white px-6 py-3 rounded-lg"
              >Get Started</a
            >
          </div>
          <div class="mt-8 ml-4">
            <a
              href="#"
              class="inline-block bg-secondary dark:bg-gray-800 text-white px-6 py-3 rounded-lg"
              >Learn More</a
            >
          </div>
        </div>
      </div>
      <div class="md:w-1/2 mt-8 md:mt-0 md:ml-14 relative">
        <div @click="toggleShowVideo" class="opacity-0 transition duration-[1500ms]" x-intersect="$el.classList.add('opacity-100')">
          <div>
            <div class="absolute w-20 h-20 bg-white top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rounded-full cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" />
              </svg>
            </div>
            <img
              src="{{ $hero->image('image')->height(300)->width(400) }}"
              alt=""
              class="w-full h-full object-cover rounded-lg shadow-md"
            />
          </div>
        </div>
      </div>
    </div>
  </div>

  <div x-auto-animate>
    <template x-if="videoIsOpen">
        <div @keydown.escape="toggleShowVideo" id="popup-modal" class="fixed top-0 left-0 right-0 z-50 overflow-x-hidden overflow-y-auto md:inset-0 h-screen max-h-full bg-black/80 flex items-center justify-center">
            <div class="relative rounded-xl p-4">
                <div class="relative bg-white rounded-xl shadow p-2 h-5/6 dark:bg-gray-700 aspect-video">
                    <div class="absolute right-[-17px] -top-5 w-11 h-11 rounded-full bg-white cursor-pointer" @click="toggleShowVideo">
                        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                    </div>
                    <iframe class="rounded-xl w-full h-full aspect-video" src="https://www.youtube.com/embed/TmWIrBPE6Bc" title="YouTube video player" autoplay=1 frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture;" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </template>
</div>
