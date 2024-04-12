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
                        <span class="hidden _list_item_badge" id="_list_item_badge-{{ $row->getId() }}">*</span>
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
            Add {{ $component->getDecoration('label') }} +
        </a>
    </label>
</div>
@pushonce('end_of_body_list')
    <script type="module">
        import {content} from '/admin/assets/js/admin_service.mjs';
        function updateBadges() {
            document.querySelectorAll('._list_item_badge').forEach((el) => {
                const exists = content.getLocalStorageItems(el.id.replace('_list_item_badge-', '')).length > 0;
                el.classList.toggle('hidden', !exists);
            });
        }
        updateBadges();
        window.addEventListener('local_content_changed', () => updateBadges());
    </script>
@endpushonce
