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
    <table class="table-auto">
        <thead class="text-left border-b border-gray-300">
        <tr>
            <th class="w-[20px]"></th>
            @php($i = 0)
            @foreach($columns as $column)
                <th class="p-4 {{ $i++ >= 1 ? 'hidden sm:table-cell' : '' }}">{{ $column['label'] }}</th>
            @endforeach
            <th class="md:w-[120px]"></th>
        </tr>
        </thead>
        <tbody id="{{ $model->getId() }}">
        <script type="module">
            import {storage} from '/admin/assets/js/admin_service.mjs';
            import LimList from '/admin/structure/list/lim_list.mjs';
            import {html, reactive} from 'https://esm.sh/@arrow-js/core';
            import {IconEtcVertical, IconMenu as IconDrag} from 'https://esm.sh/@codexteam/icons';

            const service = new LimList('{{ $model->getId() }}', @json($columns), @json($originalRows));
            const rows = service.getRows();

            const tbodyContent = html`${rows.map((row) => {
                let state = {
                    conformDelete: false,
                    changed: storage.hasLocalStorageItems(row.id),
                    deleted: false,
                }
                state = reactive(state);

                window.addEventListener('local_content_changed', () => state.changed = storage.hasLocalStorageItems(row.id));
                let i = 0;
                return html`
                    <tr class="${() => 'border-t transition-all hover:bg-gray-100' + (state.deleted ? ` hidden` : ` relative border-b border-gray-200`) + (state.changed ? ` border-x border-x-cyan-500` : ``)}">
                        <td class="p-1 md:p-4 _drag_grip">
                            <div class="flex flex-nowrap cursor-move">
                                ${IconDrag}
                            </div>
                        </td>
                        ${Object.values(row.data).map((value) => html`
                            <td class="${() => `p-1 md:p-4` + (state.conformDelete ? ` blur-sm` : ``) + (i++ >= 1 ? ` hidden sm:table-cell` : ``)}">
                                <span class="line-clamp-2">${value ?? ''}</span>
                            </td>`
                        )}
                        <td class="md:w-[120px]">
<!--onclick hidden this, but show delete and edit-->
                            <div class="flex flex-nowrap float-right px-2 py-1 m-3 ml-0" onclick="this.classList.add('collapse'); this.nextElementSibling.classList.remove('hidden')">
                                ${IconEtcVertical}
                            </div>
                            <div class="${() => `absolute flex right-0 md:flex md:flex-nowrap md:float-right hidden ` + (state.conformDelete ? `collapse` : ``)}">
                                <a class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                                   href="/admin${row.id}">
                                    Edit
                                </a>
                                <button class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                                        @click="${() => state.conformDelete = true}">
                                    Delete
                                </button>
                            </div>
                            <div class="${() => `absolute flex right-0 ` + (state.conformDelete ? `` : `collapse`)}">
                                <div>
                                    <button class="px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                                            @click="${() => state.conformDelete = false}">
                                        Cancel
                                    </button>
                                    <button class="px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-red-500 hover:bg-red-600 border border-transparent rounded-md"
                                            @click="${() => storage.delete(row.id) && (state.deleted = true)}">
                                        Confirm deletion
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>`;
            })}
            `(document.getElementById('{{ $model->getId() }}'))
            service.makeDraggable(tbodyContent);
        </script>

        </tbody>
    </table>

    <label class="m-2">
        @php($newId = $model->getId() . newId())
        <a class="float-right justify-between px-4 py-2 m-2 ml-0 text-sm font-medium leading-5 cursor-pointer text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
           onclick="localStorage.setItem('{{ $newId }}', '{{ time() }}'); window.location.href = '/admin{{ $newId }}';">
            Add {{ $component->getDecoration('label') }} +
        </a>
    </label>
</div>

