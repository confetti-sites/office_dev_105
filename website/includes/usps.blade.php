@php($usps = newRoot(new \model\homepage\usps))
<div class="bg-gray-50 dark:bg-gray-900/80">
    <div
            class="container pb-12 md:flex md:items-center pt-12"
    >
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($usps->list('usp')->columns(['title', 'description'])->max(3)->get() as $usp)
                <div
                        class="bg-white dark:bg-gray-900 rounded-lg shadow-lg overflow-hidden"
                >
                    <div class="p-4">
                        <div class="flex items-center gap-3">
                            <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                    class="w-8 h-8 text-blue-600 dark:text-white"
                            >
                                <path
                                        fill-rule="evenodd"
                                        d="M5.5 17a4.5 4.5 0 01-1.44-8.765 4.5 4.5 0 018.302-3.046 3.5 3.5 0 014.504 4.272A4 4 0 0115 17H5.5zm3.75-2.75a.75.75 0 001.5 0V9.66l1.95 2.1a.75.75 0 101.1-1.02l-3.25-3.5a.75.75 0 00-1.1 0l-3.25 3.5a.75.75 0 101.1 1.02l1.95-2.1v4.59z"
                                        clip-rule="evenodd"
                                ></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $usp->text('title')->max(50) }}
                            </h3>
                        </div>
                        <p class="mt-2 text-base text-gray-500 dark:text-white font-body">
                            @include('website.includes.blocks.index', ['model' => $usp->content('content')])
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>