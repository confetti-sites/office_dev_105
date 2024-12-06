@php($hero = newRoot(new \model\homepage\hero)->label('Hero'))
<div class="flex items-center justify-center bg-white dark:bg-gray-900">
    <div
            class="container py-28 md:flex md:items-center"
    >
        <div class="md:w-1/2">
            <h1 class="text-6xl font-bold leading-tight dark:text-white text-gray-900">
                <span>{{ $hero->text('title')->min(1)->max(30)->default('Confetti CMS') }}</span>
            </h1>
            <p class="mt-4 text-xl dark:text-white text-gray-900">
                The quickest way to create a CMS using only HTML and CSS
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
                            href="/docs"
                            class="inline-block bg-secondary dark:bg-gray-800 text-white px-6 py-3 rounded-lg"
                    >Learn More</a
                    >
                </div>
            </div>
        </div>
        <div class="md:w-1/2 mt-8 md:mt-0 md:ml-14 relative">
            {!! $hero->image('image')->getPicture(class: 'w-full h-full object-cover rounded-lg shadow-md') !!}
        </div>
    </div>
</div>
