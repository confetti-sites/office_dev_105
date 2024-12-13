@php($steps = newRoot(new \model\homepage\steps)->label('Steps'))
<section class="text-gray-600 body-font">
    <div class="container py-24 mx-auto flex flex-wrap">
        <div class="flex relative py-10 sm:items-center md:w-2/3">
            <div class="h-full w-6 absolute inset-0 flex items-center justify-center">
                <div class="h-full w-1 bg-gray-200 pointer-events-none"></div>
            </div>
            <div class="flex-grow md:pl-8 pl-6 flex sm:items-center items-start flex-col sm:flex-row">
                <h2 class="text-3xl title-font text-gray-900">{{ $steps->text('setup_title')->max(50) }}</h2>
            </div>
        </div>
        @foreach($steps->list('step')->columns(['title'])->sortable()->max(10)->get() as $i => $step)
            <div class="flex relative py-10 sm:items-center md:w-2/3">
                <div class="h-full w-6 absolute inset-0 flex items-center justify-center">
                    <div class="h-full w-1 bg-gray-200 pointer-events-none"></div>
                </div>
                <div class="flex-shrink-0 w-6 h-6 rounded-full mt-10 sm:mt-0 inline-flex items-center justify-center bg-indigo-500 text-white relative z-10 title-font font-medium text-sm">
                    <span>{{ $i + 1 }}</span>
                </div>
                <div class="flex-grow pl-6 flex sm:items-center items-start flex-col sm:flex-row">
                    <div class="flex-shrink-0 w-24 h-24 bg-indigo-100 text-indigo-500 rounded-full inline-flex items-center justify-center">
                        {{-- @todo Fix; load .svg files --}}
                        {{-- @todo Fix; load files with - in the name --}}
                        <h1 class="title-font text-gray-900 text-lg font-medium">{{ $step->text('title')->max(50) }}</h1>
                        @php($icon = $step->selectFile('icon')->match(["/website/includes/icons/*.blade.php"]))
                        @if($icon->getView())
                            @include($icon->getView(), ['model' => $icon])
                        @endif
                    </div>
                    <div class="flex-grow sm:pl-6 mt-6 sm:mt-0">
                        <h2 class="font-medium title-font text-gray-900 mb-1 text-xl">{{ $step->text('title2')->max(50) }}</h2>
                        <p class="leading-relaxed font-body">{{ $step->text('description')->max(400) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

