@php
    use Confetti\Helpers\ComponentStandard;use Confetti\Helpers\ContentStore;
    /** @var string $currentId given value from parent view */
    // Calculate all breadcrumbs
    $ids = [];
    $id = '';
    foreach (array_filter(explode('/', $currentId)) as $part) {
        $id .= '/' . $part;
        // Pointer child is part of the parent
        if (str_ends_with($id, '-') || $part === 'model') {
            continue;
        }
        $ids[$id] = $id;
    }
    // Get all component classes by ids
    $store = new ContentStore('/model', 'breadcrumb');
    $crumbs = ComponentStandard::componentClassNamesByIds($ids, $store);
@endphp

@if($currentId != '/model')
    <nav class="flex px-5 py-4 text-gray-700 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700"
         aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/admin"
                   class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-800 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                         viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                </a>
            </li>
            @foreach($crumbs as $currentId => $item)
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="/admin{{ $currentId }}"
                           class="ms-1 text-sm font-medium text-gray-700 hover:text-emerald-800 md:ms-2 dark:hover:text-white">{{ (new $item)->getComponent()->getLabel() }}</a>
                    </div>
                </li>
            @endforeach
        </ol>
    </nav>
@endif