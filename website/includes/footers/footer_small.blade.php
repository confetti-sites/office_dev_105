@php($footer = extendModel($model))
<footer class="bg-white shadow-sm dark:bg-gray-800">
    <div class="w-full mx-auto max-w-(--breakpoint-xl) p-4 md:flex md:items-center md:justify-between">
      <span class="text-sm text-gray-500 sm:text-center">
          © {{ date('Y') }}
          <a href="/" class="hover:underline">
              {{ $footer->text('first_line')->default('Confetti™') }}
          </a>
    </span>
        <ul class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 sm:mt-0">
            <li>
                <a href="#" class="mr-4 hover:underline md:mr-6 ">
                    {{ $footer->text('text_link_1')->default('About') }}
                </a>
            </li>
            <li>
                <a href="#" class="mr-4 hover:underline md:mr-6">
                    {{ $footer->text('text_link_2')->default('Privacy Policy') }}
                </a>
            </li>
            <li>
                <a href="#" class="mr-4 hover:underline md:mr-6">
                    {{ $footer->text('text_link_3')->default('Licensing') }}
                </a>
            </li>
            <li>
                <a class="js-contact-e hover:underline"></a>
            </li>
        </ul>
    </div>
</footer>