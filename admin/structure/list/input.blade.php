@php
    /** @var \\Src\Structure\List\ListComponent $model */
    /** @var \Confetti\Helpers\ComponentEntity $component */
    use \Src\Structure\List\ListComponent;
    use Confetti\Components\Map;
    use Confetti\Helpers\ComponentStandard;
    $component = $model->getComponent();
    [$columns, $originalRows] = ListComponent::getColumnsAndRows($model);
@endphp

<!--suppress HtmlUnknownAttribute, HtmlUnknownTag -->
<list-component
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-sortable="{{ $model->getComponent()->getDecoration('sortable') ? 'true' : '' }}"
        data-columns='@json($columns)'
        data-original_rows='@json($originalRows)'
        data-service_api="{{ getServiceApi() }}"
></list-component>

<div class="mx-auto h-16 w-16 rounded-lg bg-blue-500">Todo let this style by tailwind</div>


@pushonce('end_of_body_list_component')
    <script type="module">
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import LimList from '/admin/view/list/lim_list.mjs';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';
        import {IconMenu as IconDrag} from 'https://esm.sh/@codexteam/icons';

        customElements.define('list-component', class extends HTMLElement {
            id;
            label;
            sortable;
            columns;
            originalRows;
            service;
            serviceApi;

            constructor() {
                super();
                this.id = this.dataset.id;
                this.label = this.dataset.label;
                this.sortable = this.dataset.sortable;
                this.columns = JSON.parse(this.dataset.columns);
                this.originalRows = JSON.parse(this.dataset.original_rows);
                this.service = new LimList(this.dataset.id, this.columns, this.originalRows);
                this.serviceApi = this.dataset.service_api;
            }

            connectedCallback() {
                const rows = this.service.getRows();

                html`
                    <div class="block text-bold text-xl mt-8 mb-4">${this.label} List</div>

                    <!-- border rounded -->
                    <div class="container grid border text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50">
                        <table class="table-auto">
                            ${this.columns.length > 1 ? html`
                                <thead class="hidden sm:table-header-group text-left border-b border-gray-300">
                                <tr>
                                    ${this.sortable ? html`
                                        <th class="w-[20px]"></th>` : ''}
                                    ${this.columns.map(column => html`
                                        <th class="pt-4 pr-2 pb-4 pl-4">${column.label}</th>`)}
                                    <th class="w-[140px]"></th>
                                </tr>
                                </thead>` : ''}
                            <tbody>
                            ${rows.length === 0 ? `
                                <tr>
                                    <td colspan="${this.columns.length + 2}" class="p-4 p-12 text-center">No items found, click 'Add ${this.label}' to add a new item.
                                    </td>
                                </tr>
                            ` : html`${rows.map(row => {
                                let state = {
                                    confirmDelete: false,
                                    changed: Storage.hasLocalStorageItems(row.id),
                                };
                                state = reactive(state);
                                let columns = this.service.getColumns(row);
                                console.log('columns', columns);
                                window.addEventListener('local_content_changed', () => state.changed = Storage.hasLocalStorageItems(row.id));
                                let i = 0;
                                return html`
                                <tr class="${()=>'border-t transition-all hover:bg-gray-100 relative border-b border-gray-200' + (state.changed ? ` border-x border-x-emerald-700` : ``)}"
                                    content_id="${row.id}" index="${row.data['.']}">
                                    ${this.sortable ? `
                                    <td class="hidden sm:table-cell sm:p-2 sm:pl-4 w-[20px]">
                                        <div class="flex flex-nowrap cursor-move _drag_grip">
                                            ${IconDrag}
                                        </div>
                                    </td>` : ''}
                                    ${columns.map(value => html`
                                    <td class="${()=>` p-3 sm:pl-4` + (state.confirmDelete ? ` blur-sm` : ``) + (i++ >= 1 ? ` hidden sm:table-cell` : ``)}"
                                        @mousedown="${() => (window.innerWidth < 640) ? window.location.href = '/admin' + row.id : ''}">
                                        <div class="h-16 w-16 rounded-full bg-blue-500">hello</div>
                                        <span class="line-clamp-2">${value}</span>
                                    </td>`)}
                                    <td class="hidden sm:table-cell sm:w-[140px]">
                                        <div class="${()=>`flex flex-nowrap float-right ` + (state.confirmDelete ? `collapse` : ``)}">
                                            <a class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 cursor-pointer text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                               @mousedown="${() => this.#editItem(row.id)}">
                                                Edit
                                            </a>
                                            <button class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                                    @mousedown="${() => state.confirmDelete = true}">
                                                Delete
                                            </button>
                                        </div>
                                        <div class="${()=>`absolute flex right-0 ` + (state.confirmDelete ? `` : `collapse`)}">
                                            <div>
                                                <button class="px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                                        @mousedown="${() => state.confirmDelete = false}">Cancel</button>
                                                <button class="px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-red-500 hover:bg-red-600 border border-transparent rounded-md"
                                                        @mousedown="${element => this.#removeItem(element, rows, row)}">
                                                    Confirm
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>`;
                            })}`}
                            </tbody>
                        </table>
                    </div>
                    <label class="m-2 h-10 block">
                        <a class="float-right justify-between px-2 py-1 m-2 ml-0 text-sm font-medium leading-5 cursor-pointer text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                           @mousedown="${() => this.#redirectToNew()}">
                            Add ${this.label}
                        </a>
                    </label>
                `(this)
                this.#renderedCallback();
            }

            #renderedCallback() {
                if (this.sortable) {
                    this.service.makeDraggable(this.getElementsByTagName('tbody')[0]);
                }
            }

            #redirectToNew() {
                this.#savePointers(this.id);
                let newId = this.id + Storage.newId();
                localStorage.setItem(newId, JSON.stringify(Date.now()));
                // Open in new tab if ctrl or cmd key is pressed
                if (event.ctrlKey || event.metaKey) {
                    window.open('/admin' + newId, '_blank');
                } else {
                    window.location.href = '/admin' + newId;
                }
            }

            #editItem(id) {
                this.#savePointers(id);
                if (event.ctrlKey || event.metaKey) {
                    window.open('/admin' + id, '_blank');
                } else {
                    window.location.href = '/admin' + id;
                }
            }

            #removeItem(element, rows, row) {
                Storage.delete(this.serviceApi, row.id);
                element.target.closest('tr').remove();
                delete rows[row.id];
            }

            // Before we can edit the child of a pointer, we need
            // to save the pointers if present in the local storage
            async #savePointers(childId) {
                // If child id is `/model/template-/page` then the pointer key is `/model/template-`
                // explode id to ids
                const ids = childId.split('/');
                // Remove the last element
                ids.pop();
                // Join the ids
                const key = ids.join('/');
                // If key has suffix `-` save it
                if (key.endsWith('-')) {
                    await Storage.saveFromLocalStorage(this.serviceApi, key, true);
                }
                if (ids.length > 2) {
                    await this.#savePointers(key);
                }
            }
        });
    </script>
@endpushonce
