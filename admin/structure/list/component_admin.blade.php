@php
    /** @var \Confetti\Components\List_ $model */
    /** @var \Confetti\Helpers\ComponentEntity $component */
    use Confetti\Components\List_;
    $component = $model->getComponent();
    $columns = $component->getDecoration('columns') ?? List_::getDefaultColumns($model);
@endphp

<div class="block text-bold text-xl mt-8 mb-4">
    {{ $component->getDecoration('label') }} List
</div>
<!-- border rounded -->
<div class="container grid border text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
    <table class="table-auto">
        <thead class="text-left border-b border-gray-300">
        <tr>
            @foreach($columns as $column)
                <th class="p-4">{{ $column['label'] }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody class="table-auto">
        @forelse($model->get() as $parentId => $row)
            <tr class="border-b border-gray-200">
                @foreach($columns as $column)
                    <td class="p-4">
                        {{ $row->getChildren()[$column['id']] }}
                    </td>
                @endforeach
                <td>
                    <button
                            @click="deleteRow"
                            name="{{ $row->getId() }}"
                            class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                    >
                        Delete
                    </button>
                    <a
                            href="/admin{{ $row->getId() }}"
                            class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                    >
                        Edit
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td class="p-4">
                    {{ $component->getDecoration('label') }} not found. Click on "+
                    Add {{ $component->getDecoration('label') }}" to create one.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <label class="m-2">
        <a
                class="float-right justify-between px-4 py-2 m-2 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                href="/admin{{ $model->getId() . newId() }}"
        >
            + Add {{ $component->getDecoration('label') }}
        </a>
    </label>
</div>
@pushonce('end_of_body_list')
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
        //     xhr.open("DELETE", "getServiceApiUrl()/confetti-cms/content/contents?id_prefix=" + idPrefix);
        //     xhr.setRequestHeader("Admin_service-Type", "application/json");
        //     xhr.setRequestHeader("Accept", "application/json");
        //     xhr.setRequestHeader("Authorization", "Bearer " + document.cookie.split('access_token=')[1].split(';')[0]);
        //     xhr.send();
        // }
    </script>
@endpushonce
