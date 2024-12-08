@php($hero = newRoot(new \model\homepage\hero)->label('Hero'))

<div class="md:flex md:items-center md:justify-center bg-white mt-4">
        <div class="container md:w-1/2 mb-8 flex flex-col items-center justify-center">
            <h1 class="mt-4 text-xl dark:text-white text-gray-900">
                The quickest way to create a CMS
            </h1>
            <div class="flex items-center">
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

        <text-demo>
            <!-- skeleton loader -->
        </text-demo>
</div>

@pushonce('end_of_body_hero')
    <script type="module" defer>
        import textDemo from '/website/assets/mjs/text_demo.mjs';
        customElements.define('text-demo', textDemo);
    </script>
@endpushonce

















