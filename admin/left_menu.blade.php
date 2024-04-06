@php use Confetti\Components\Map; @endphp
@php([$currentContentId] = variables($variables))

@php /** @var string $currentContentId */ @endphp
@php($root = newRoot(new \model))

<div class="py-4 text-gray-500 dark:text-gray-400">
    <ul class="mt-6">
        @foreach($root->getChildren() as $firstChild)
            <li class="relative px-3 py-3">
                @php($component = $firstChild->getComponent())
                @php($isCurrent = $firstChild->getId() === $currentContentId || str_starts_with($currentContentId, $firstChild->getId() . '/'))
                @if($isCurrent)
                    <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-primary-300"
                          aria-hidden="true"></span>
                @endif
                <a
                        class="inline-flex items-center w-full text-sm font-semibold hover:text-gray-800 dark:hover:text-gray-200 @if($isCurrent)text-gray-800 dark:text-gray-100 @endif"
                        href="/admin{{ $firstChild->getId() }}"
                >
                    <span class="ml-4">{{ $component->getLabel()}}</span>
                </a>
                {{-- Where are only interested in the children that are maps (not a list or value field). --}}
                @php($children = method_exists($firstChild, 'getChildren') ? array_filter($firstChild->getChildren(), fn($c) => $c instanceof Map) : [])
                @if(!empty($children))
                    <ul
                            class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                            aria-label="submenu"
                    >
                        @foreach($children as $secondChild)
                            @if($secondChild instanceof Map)
                                <li class="relative pl-5 pr-1 py-1 hover:text-gray-800 dark:hover:text-gray-200">
                                    @if($secondChild->getId() === $currentContentId)
                                        <span class="absolute inset-y-0 -left-1 w-1 rounded-tr-lg rounded-br-lg bg-primary-600"
                                              aria-hidden="true">
                                        </span>
                                    @endif
                                    <a class="w-full"
                                       href="/admin{{ $secondChild->getId() }}">{{ $secondChild->getComponent()->getLabel() }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>
