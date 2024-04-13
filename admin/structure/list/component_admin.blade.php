@php
    /** @var \Confetti\Components\List_ $model */
    /** @var \Confetti\Helpers\ComponentEntity $component */
    use Confetti\Components\List_;
    use Confetti\Components\Map;
    use Confetti\Helpers\ComponentStandard;
    $component = $model->getComponent();
    $columns = List_::getColumns($model);
    $originalRows = array_map(fn(Map $row) => [
        'id' => $row->getId(),
        'data' => array_map(
            fn(ComponentStandard $child) => $child->get(),
                // For now, we can't handle columns that are lists
                array_filter($row->getChildren(), fn($child) => $child instanceof ComponentStandard)
            ),
    ], $model->get()->toArray())
@endphp

<div class="block text-bold text-xl mt-8 mb-4">
    {{ $component->getDecoration('label') }} List
</div>
<!-- border rounded -->
<div class="container grid border text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
    <table class="table-auto" id="{{ $model->getId() }}~">
        <thead class="text-left border-b border-gray-300">
        <tr>
            @foreach($columns as $column)
                <th class="p-4">{{ $column['label'] }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody class="table-auto">
        <script type="module">
            import LimList from '/admin/structure/list/lim_list.mjs';
            import {html} from 'https://esm.sh/@arrow-js/core';
            const rows = new LimList('{{ $model->getId() }}', @json($columns), @json($originalRows)).getRows();

            html`
                ${rows.map((row) => html`
                    <tr class="border-b border-gray-200 _list_item_changed_style" id="_list_item_changed_style-${row.id}">
                        ${Object.values(row.data).map((value) => html`
                            <td class="p-4">${value ?? ''}</td>
                        `)}
                        <td>
                            <button
                                    @click="deleteRow"
                                    name="${row.id}"
                                    class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                            >
                                Delete
                            </button>
                            <a
                                    href="/admin${row.id}"
                                    class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                            >
                                Edit
                            </a>
                        </td>
                    </tr>
                `)}

            `(document.getElementById('{{ $model->getId() }}~'));
        </script>

        </tbody>
    </table>
    <label class="m-2">
        @php($newId = $model->getId() . newId())
        <a
                class="float-right justify-between px-4 py-2 m-2 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                onclick="localStorage.setItem('{{ $newId }}', '{{ time() }}'); window.location.href = '/admin{{ $newId }}';"
        >
            Add {{ $component->getDecoration('label') }} +
        </a>
    </label>
</div>
@pushonce('end_of_body_list')
    <script type="module">
        import {storage} from '/admin/assets/js/admin_service.mjs';
        function updateChangeStyle() {
            document.querySelectorAll('._list_item_changed_style').forEach((el) => {
                const exists = storage.getLocalStorageItems(el.id.replace('_list_item_changed_style-', '')).length > 0;
                el.classList.toggle('border-x', exists);
                el.classList.toggle('border-x-cyan-500', exists);
            });
        }
        updateChangeStyle();
        window.addEventListener('local_content_changed', () => updateChangeStyle());
    </script>
@endpushonce
