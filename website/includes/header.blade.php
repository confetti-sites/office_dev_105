@php($header = newRoot(new \model\homepage\header)->label('Header'))

<header class="z-50 bg-white/80 backdrop-blur border-b border-gray-100 dark:border-gray-700/30 dark:bg-gray-900/80 w-full">
    <nav class="relative">
        <div class="flex items-center justify-between px-4 py-2">
            <!-- Logo Container -->
            <div id="logo" class="flex items-center p-2">
                <a href="/" aria-label="logo" class="flex items-center space-x-4">
                    <img src="/website/assets/confetti_cms_logo.png" class="h-10">
                    <span class="text-xl" id="brand-title">Confetti CMS</span>
                </a>
            </div>
            <div></div>
            <div class="flex justify-end  md:justify-end">
                <!-- Hamburger Icon -->
                <button id="menu-toggle"
                        type="button"
                        aria-label="Toggle Navigation"
                        class="text-gray-600 dark:text-gray-300 focus:outline-none md:hidden"
                >
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>

                <div class="js-menu hidden flex-col space-y-2 px-4 py-2 bg-white dark:bg-gray-900 dark:border-gray-700 md:flex md:flex-row md:space-y-0 md:space-x-4 md:border-none md:py-0">
                    <a href="/" class="block md:hidden transition hover:text-primary dark:hover:text-primaryLight px-4 py-2 md:py-2">
                        Home
                    </a>
                    <a href="/pricing" class="block transition hover:text-primary dark:hover:text-primaryLight px-4 py-2 md:py-2">
                        Pricing
                    </a>
                    <a href="/docs" class="block transition hover:text-primary dark:hover:text-primaryLight px-4 py-2 md:py-2">
                        Docs
                    </a>
                    @guest
                        <a href="https://tally.so/r/mK5kgK"
                           class="relative ml-auto flex h-10 w-full items-center justify-center before:absolute before:inset-0 before:rounded-full before:bg-primary before:transition-transform before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95 dark:before:border-gray-700 dark:before:bg-primaryLight px-4"
                        >
                        <span class="relative text-sm font-semibold text-white dark:text-gray-900">
                            Join<span class="hidden sm:contents"> the waiting list</span>
                        </span>
                        </a>
                    @endguest
                </div>
                <!-- Navigation Links -->
            </div>
        </div>
    </nav>
</header>

<script>
    // Toggle menu visibility
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementsByClassName('js-menu')[0];
    const logo = document.getElementById('logo');

    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('hidden');
        menu.classList.toggle('hidden');
        logo.classList.toggle('hidden');
    });
</script>


