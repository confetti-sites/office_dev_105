@php /** @var \Confetti\Components\List_ $model */ @endphp
@php use Confetti\Components\List_; @endphp
@php($component = $model->getComponent())

<!-- border rounded -->
<div class="container px-6 py-4 m-10 mx-auto grid border border-purple-600 rounded-lg">
    <table class="table-auto">
        <thead class="text-left border-b border-purple-300">
        <tr>
            @foreach($component->getDecoration('columns') ?? List_::getDefaultColumns($model) as $column)
                <th class="p-3">{{ $column['label'] }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody class="table-auto">
        {{--        @php(@todo !!!!!!!!!!!!!!)--}}
        @php($rows = [])
        @forelse($rows as $parentId => $row)
            <tr class="border-b border-purple-300">
                @foreach($row as $content)
                    <td class="p-3">
                        {{ $content->value }}
                    </td>
                @endforeach
                <td>
                    <button
                            @click="deleteRow"
                            name="{{ $parentId }}"
                            class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                    >
                        Delete
                    </button>
                    <a
                            href="/admin{{ $parentId }}"
                            class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                    >
                        Edit
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td class="p-2">
                    {{ $component->getDecoration('label') }} not found. Click on "+
                    Add {{ $component->getDecoration('label') }}" to create one.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <label class="block mt-4">
        <a
                class="float-right justify-between px-2 py-1 m-2 ml-0 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
{{--                getId() on model is needed to get the correct id for the model, when list is in other list (with id) --}}
                href="/admin{{ $model->getId(). newId() }}"
        >
            + Add {{ $component->getDecoration('label') }}
        </a>
    </label>
</div>
@pushonce('script_list')
    <script>
        // function deleteRow(e) {
        //     let idPrefix = e.target.attributes.name.value;
        //
        //     let xhr = new XMLHttpRequest();
        //     xhr.withCredentials = true;
        //     xhr.addEventListener("readystatechange", function () {
        //         if (this.status >= 300) {
        //             console.log("Error: " + this.responseText);
        //             return;
        //         }
        //         e.target.parentNode.parentNode.remove();
        //     });
        //     xhr.open("DELETE", Alpine.store('config').getApiUrl() + "/confetti-cms/content/contents?id_prefix=" + idPrefix);
        //     xhr.setRequestHeader("Content-Type", "application/json");
        //     xhr.setRequestHeader("Accept", "application/json");
        //     xhr.setRequestHeader("Authorization", "Bearer " + document.cookie.split('access_token=')[1].split(';')[0]);
        //     xhr.send();
        // }
    </script>
@endpushonce
