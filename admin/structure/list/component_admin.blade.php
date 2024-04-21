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
                // For now, we can't handle columns that are normal components (not lists)
                // And filter out none column components
                array_filter($row->getChildren(), fn($child) =>
                    $child instanceof ComponentStandard &&
                    in_array($child->getRelativeId(), array_column($columns, 'id'))
                )
            ) + ['.' => $row->getValue()],
    ], $model->get()->toArray());
@endphp

<div class="block text-bold text-xl mt-8 mb-4">
    {{ $component->getLabel() }} List
</div>
<!-- border rounded -->
<div class="container grid border text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
    <table class="table-auto">
        <thead class="hidden sm:table-header-group text-left border-b border-gray-300">
            <tr>
                <th class="w-[20px]"></th>
                @php($i = 0)
                @foreach($columns as $column)
                    <th class="pt-4 pr-2 pb-4 pl-4">{{ $column['label'] }}</th>
                @endforeach
                <th class="w-[140px]"></th>
            </tr>
        </thead>
        <tbody id="t_body_{{ $model->getId() }}">
        <script type="module">
            import {storage} from '/admin/assets/js/admin_service.mjs';
            import LimList from '/admin/structure/list/lim_list.mjs';
            import {html, reactive} from 'https://esm.sh/@arrow-js/core';
            import {IconMenu as IconDrag} from 'https://esm.sh/@codexteam/icons';

            const service = new LimList('{{ $model->getId() }}', @json($columns), @json($originalRows));
            const rows = service.getRows();

            const tbodyContent = html`${rows.map((row) => {
                const sm = 640;
                let state = {
                    confirmDelete: false,
                    changed: storage.hasLocalStorageItems(row.id),
                }
                state = reactive(state);
                let columns = service.getColumns(row);
                window.addEventListener('local_content_changed', () => state.changed = storage.hasLocalStorageItems(row.id));

                let i = 0;
                return html`
                    <tr class="${() => 'border-t transition-all hover:bg-gray-100 relative border-b border-gray-200' + (state.changed ? ` border-x border-x-emerald-700` : ``)}"
                        content_id="${row.id}" index="${row.data['.']}">
                        <td class="hidden sm:table-cell sm:p-2 sm:pl-4">
                            <div class="flex flex-nowrap cursor-move _drag_grip">
                                ${IconDrag}
                            </div>
                        </td>
                        ${columns.map((value) => html`
                            <td class="${() => ` p-3 ms:pl-4` + (state.confirmDelete ? ` blur-sm` : ``) + (i++ >= 1 ? ` hidden sm:table-cell` : ``)}"
                                @click="${() => (window.innerWidth < sm) ? window.location.href = '/admin' + row.id : ''}">
                                <span class="line-clamp-2">${value ?? ''}</span>
                            </td>`
                        )}
                        <td class="hidden sm:table-cell sm:w-[140px]">
                            <div class="${() => `flex flex-nowrap float-right ` + (state.confirmDelete ? `collapse` : ``)}">
                                <a class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                   href="/admin${row.id}">
                                    Edit
                                </a>
                                <button class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                        @click="${() => state.confirmDelete = true}">
                                    Delete
                                </button>
                            </div>
                            <div class="${() => `absolute flex right-0 ` + (state.confirmDelete ? `` : `collapse`)}">
                                <div>
                                    <button class="px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                            @click="${() => state.confirmDelete = false}">
                                        Cancel
                                    </button>
                                    <button class="px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-red-500 hover:bg-red-600 border border-transparent rounded-md"
                                            @click="${(element) =>storage.delete('{{ getServiceApi() }}', row.id) && element.target.closest('tr').remove() && delete rows[row.id]}">
                                        Confirm
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>`;
            })}
            `(document.getElementById('t_body_{{ $model->getId() }}'))
            service.makeDraggable(tbodyContent);
        </script>

        </tbody>
    </table>

    <label class="m-2" id="add_button_{{ $model->getId() }}">
        <script type="module">
            import {storage} from '/admin/assets/js/admin_service.mjs';
            import {html} from 'https://esm.sh/@arrow-js/core';

            function createNew() {
                let newId = '{{$model->getId()}}' + storage.newId();
                localStorage.setItem(newId, JSON.stringify(Date.now()));
                return '/admin' + newId;
            }

            html`
        <a class="float-right justify-between px-2 py-1 m-2 ml-0 text-sm font-medium leading-5 cursor-pointer text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
           @click="${() => window.location.href = createNew()}">
            Add {{ $component->getLabel() }}
        </a>
            `(document.getElementById('add_button_{{ $model->getId() }}'))
        </script>
    </label>
</div>

