<div class="relative overflow-x-auto py-3">
    <table class="{{ $block['data']['stretched'] ? 'w-full' : '' }} pt-3 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <tbody>
        @foreach($block['data']['content'] as $row)
            @if($block['data']['withHeadings'] && $loop->first)
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    @foreach($row as $cell)
                        <th scope="col" class="px-6 py-3">
                            {!! $cell !!}
                        </th>
                    @endforeach
                </tr>
                </thead>
            @else
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    @foreach($row as $cell)
                        <td class="px-6 py-4">
                            {!! $cell !!}
                        </td>
                    @endforeach
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>