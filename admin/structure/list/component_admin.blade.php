@php
    /** @var \Confetti\Components\List_ $model */
    /** @var \Confetti\Helpers\ComponentEntity $component */
    use Confetti\Components\List_;
    use Confetti\Components\Map;use Confetti\Helpers\ComponentStandard;
    $component = $model->getComponent();
    $columns = $component->getDecoration('columns') ?? List_::getDefaultColumns($model);
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
            import {content} from '/admin/assets/js/admin_service.mjs';
            import {html, reactive} from 'https://esm.sh/@arrow-js/core';
            @php
                $originalRows = array_map(fn(Map $row) => [
                    'id' => $row->getId(),
                    'data' => array_map(fn(ComponentStandard $child) => $child->get(), $row->getChildren())
                ], $model->get()->toArray())
            @endphp
            const parent = '{{ $model->getId() }}';
            const columns = @json($columns);
            const rowsRaw = @json($originalRows);
            const rows = [];
            for (const rowRaw of rowsRaw) {
                const data = {};
                for (const column of columns) {
                    // Use localstorage if available
                    const id = rowRaw.id + '/' + column.id;
                    if (localStorage.hasOwnProperty(id)) {
                        data[column.id] = localStorage.getItem(id);
                    } else {
                        data[column.id] = rowRaw.data[column.id];
                    }
                }
                rows.push({id: rowRaw.id, data});
            }
            // append new rows from local storage


            html`
            ${rows.map((row) => html`
            <tr class="border-b border-gray-200">
                ${Object.values(row.data).map((value) => html`
                    <td class="p-4">${value}</td>
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
                        <span class="hidden _list_item_badge" id="_list_item_badge-${row.id}">*</span>
                    </a>
                </td>
            </tr>
            `)}

            `(document.getElementById('{{ $model->getId() }}~'));
        </script>

{{--        @forelse($model->get() as $parentId => $row)--}}
{{--            <tr class="border-b border-gray-200">--}}
{{--                @foreach($columns as $column)--}}
{{--                    <td class="p-4">--}}
{{--                        {{ $row->getChildren()[$column['id']] }}--}}
{{--                    </td>--}}
{{--                @endforeach--}}
{{--                <td>--}}
{{--                    <button--}}
{{--                            @click="deleteRow"--}}
{{--                            name="{{ $row->getId() }}"--}}
{{--                            class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"--}}
{{--                    >--}}
{{--                        Delete--}}
{{--                    </button>--}}
{{--                    <a--}}
{{--                            href="/admin{{ $row->getId() }}"--}}
{{--                            class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"--}}
{{--                    >--}}
{{--                        Edit--}}
{{--                        <span class="hidden _list_item_badge" id="_list_item_badge-{{ $row->getId() }}">*</span>--}}
{{--                    </a>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--        @empty--}}
{{--            <tr>--}}
{{--                <td class="p-4">--}}
{{--                    {{ $component->getDecoration('label') }} not found. Click on "+--}}
{{--                    Add {{ $component->getDecoration('label') }}" to create one.--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--        @endforelse--}}
        </tbody>
    </table>
    <label class="m-2">
        <a
                class="float-right justify-between px-4 py-2 m-2 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                href="/admin{{ $model->getId() . newId() }}"
        >
            Add {{ $component->getDecoration('label') }} +
        </a>
    </label>
</div>
@pushonce('end_of_body_list')
    {{--    <script type="module">--}}
    {{--        import {content} from '/admin/assets/js/admin_service.mjs';--}}
    {{--        function updateBadges() {--}}
    {{--            document.querySelectorAll('._list_item_badge').forEach((el) => {--}}
    {{--                const exists = content.getLocalStorageItems(el.id.replace('_list_item_badge-', '')).length > 0;--}}
    {{--                el.classList.toggle('hidden', !exists);--}}
    {{--            });--}}
    {{--        }--}}
    {{--        updateBadges();--}}
    {{--        window.addEventListener('local_content_changed', () => updateBadges());--}}
    {{--    </script>--}}
@endpushonce
