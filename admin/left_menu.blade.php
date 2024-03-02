@php([$currentContentId] = variables($variables))

@php /** @var string $currentContentId */ @endphp
@php($root = model(new \model))
<div class="py-4 text-gray-500 dark:text-gray-400">
    <ul class="mt-6">
        @foreach($root->getChildren() as $firstChild)
            <li class="relative px-3 py-3">
                @php($isCurrent = $firstChild->getFullId() === $currentContentId)
                @php($component = $firstChild->getComponent())
                @if($isCurrent)
                    <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-primary-600"
                          aria-hidden="true"></span>
                @endif
                <a
                        class="inline-flex items-center w-full text-sm font-semibold hover:text-gray-800 dark:hover:text-gray-200 @if($isCurrent)text-gray-800 dark:text-gray-100 @endif"
                        href="/admin{{ $firstChild->getFullId() }}"
                >
                    <span class="ml-4">{{ $firstChild->guessLabel()}}</span>
                </a>

                @php($children = method_exists($firstChild, 'getChildren') ? $firstChild->getChildren() : [])
                @if(!empty($children))
                    <ul
                            class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                            aria-label="submenu"
                    >
                        @foreach($children as $component)
                            @if($component instanceof \Confetti\Components\Map)
                                <li class="pl-5 pr-1 py-1 hover:text-gray-800 dark:hover:text-gray-200">
                                    <a class="w-full"
                                       href="/admin{{ $component->getFullId() }}">{{ $component->guessLabel() }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>

@php(exit('asdfadsfadsfds'))


{{--                @if(!key_exists($firstChild->key, $subItems))--}}
{{--                    --}}{{-- @todo component key by id --}}
{{--                    @php($isCurrent = $firstChild->key === $currentContentId)--}}
{{--                    @if($isCurrent)--}}
{{--                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-primary-600"--}}
{{--                              aria-hidden="true"></span>--}}
{{--                    @endif--}}
{{--                    <a--}}
{{--                            class="inline-flex items-center w-full text-sm font-semibold hover:text-gray-800 dark:hover:text-gray-200 @if($isCurrent)text-gray-800 dark:text-gray-100 @endif"--}}
{{--                            href="/admin{{ $firstChild->key }}"--}}
{{--                    >--}}
{{--                        <span class="ml-4">{{ $firstChild->getDecoration('label')['value'] }}</span>--}}
{{--                    </a>--}}
{{--                @else--}}
{{--                    <button--}}
{{--                            class="inline-flex items-center justify-between w-full text-sm font-semibold hover:text-gray-800 dark:hover:text-gray-200"--}}
{{--                            @click="togglePagesMenu"--}}
{{--                            aria-haspopup="true"--}}
{{--                    >--}}
{{--                        <span class="inline-flex items-center">--}}
{{--                          <span class="ml-4">{{ $firstChild->getDecoration('label')['value'] }}</span>--}}
{{--                        </span>--}}
{{--                        <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">--}}
{{--                            <path--}}
{{--                                    fill-rule="evenodd"--}}
{{--                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"--}}
{{--                                    clip-rule="evenodd"--}}
{{--                            ></path>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                    <template x-if="isPagesMenuOpen">--}}
{{--                        <ul--}}
{{--                                class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"--}}
{{--                                aria-label="submenu"--}}
{{--                        >--}}
{{--                            @foreach($subItems[$firstChild->key] as $component)--}}
{{--                                <li class="pl-5 pr-1 py-1 hover:text-gray-800 dark:hover:text-gray-200">--}}
{{--                                    <a class="w-full"--}}
{{--                                       href="/admin{{ $component->key }}">{{ $component->getDecoration('label')['value'] }}</a>--}}
{{--                                </li>--}}
{{--                            @endforeach--}}
{{--                        </ul>--}}
{{--                    </template>--}}
{{--                @endif--}}
{{--            </li>--}}
{{--        @endforeach--}}
{{--    </ul>--}}
{{--</div>--}}
