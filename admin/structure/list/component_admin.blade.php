@php
    /** @var \Confetti\Components\List_ $model */
    /** @var \Confetti\Helpers\ComponentEntity $component */
    use Confetti\Components\List_;
    use Confetti\Components\Map;
    use Confetti\Helpers\ComponentStandard;
    $component = $model->getComponent();
    [$columns, $originalRows] = List_::getColumnsAndRows($model);
@endphp

<!--suppress HtmlUnknownAttribute -->
<list-component
        data-name="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-sortable="{{ $model->getComponent()->getDecoration('sortable') ? 'true' : '' }}"
        data-columns='@json($columns)'
        data-original_rows='@json($originalRows)'
        data-can_edit_children="{{ $canEditChildren ?? true }}"
        data-serviceApi="{{ getServiceApi() }}"
></list-component>

@pushonce('end_of_body_list_component')
    <script type="module">
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import LimList from '/admin/structure/list/lim_list.mjs';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';
        import {IconMenu as IconDrag} from 'https://esm.sh/@codexteam/icons';

        customElements.define('list-component', class extends HTMLElement {
            name;
            label;
            sortable;
            columns;
            originalRows;
            canEditChildren;
            service;
            serviceApi;

            constructor() {
                super();
                this.name = this.dataset.name;
                this.label = this.dataset.label;
                this.sortable = this.dataset.sortable;
                this.columns = JSON.parse(this.dataset.columns);
                this.originalRows = JSON.parse(this.dataset.original_rows);
                this.canEditChildren = this.dataset.can_edit_children;
                this.service = new LimList(this.dataset.name, this.columns, this.originalRows);
                this.serviceApi = this.dataset.serviceapi;
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
                                    <td colspan="${this.columns.length + 2}" class="p-4 p-12 text-center">
                                        <span>${this.canEditChildren ? `No items found, click 'Add ${this.label}' to add a new item.` : `Publish this first so you can add "${this.label}" items.`}</span>
                                    </td>
                                </tr>
                            ` : html`${rows.map(row => {
                                let state = {
                                    confirmDelete: false,
                                    changed: Storage.hasLocalStorageItems(row.id),
                                };
                                state = reactive(state);
                                let columns = this.service.getColumns(row);
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
                                        @click="${() => (window.innerWidth < 640) ? window.location.href = '/admin' + row.id : ''}">
                                        <span class="line-clamp-2">${value ?? ''}</span>
                                    </td>`)}
                                    <td class="hidden sm:table-cell sm:w-[140px]">
                                        <div class="${()=>`flex flex-nowrap float-right ` + (state.confirmDelete ? `collapse` : ``)}">
                                            ${this.canEditChildren ? html`
                                            <a class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                                  href="/admin${row.id}">
                                                Edit
                                            </a>
                                            <button class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                                    @click="${() => state.confirmDelete = true}">
                                                Delete
                                            </button>
                                            ` : `
                                            <div class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white cursor-not-allowed bg-gray-700 border border-transparent rounded-md">Edit</div>
                                            <div class="float-right justify-between px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white cursor-not-allowed bg-gray-700 border border-transparent rounded-md">Delete</div>
                                            `}

                                        </div>
                                        <div class="${()=>`absolute flex right-0 ` + (state.confirmDelete ? `` : `collapse`)}">
                                            <div>
                                                <button class="px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                                        @click="${() => state.confirmDelete = false}">Cancel</button>
                                                <button class="px-2 py-1 m-3 ml-0 text-sm font-medium leading-5 text-white bg-red-500 hover:bg-red-600 border border-transparent rounded-md"
                                                        @click="${element => this.#removeItem(element, rows, row)}">
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
                    <div>
                    ${this.canEditChildren ? html`
                        <label class="m-2 h-10 block">
                            <a class="float-right justify-between px-2 py-1 m-2 ml-0 text-sm font-medium leading-5 cursor-pointer text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                               @click="${() => this.#redirectToNew()}">
                                Add ${this.label}
                            </a>
                        </label>
                    ` : `
                        <label class="m-2 h-10 block">
                            <a class="float-right justify-between px-2 py-1 m-2 ml-0 text-sm font-medium leading-5 cursor-not-allowed text-white bg-gray-700 border border-transparent rounded-md">Add ${this.label}</a>
                            ${rows.length > 0 ? `<div class="m-2 text-red-600">For now, you can only view the list. Publish this to make changes to the list.</div>` : ''}
                        </label>
                    `}
                    <div>`(this)
                this.#renderedCallback();
            }

            #renderedCallback() {
                if (this.sortable) {
                    this.service.makeDraggable(this.getElementsByTagName('tbody')[0]);
                }
            }

            #redirectToNew() {
                let newId = this.name + Storage.newId();
                localStorage.setItem(newId, JSON.stringify(Date.now()));
                window.location.href = '/admin' + newId;
            }

            #removeItem(element, rows, row) {
                Storage.delete(this.serviceApi, row.id);
                element.target.closest('tr').remove();
                delete rows[row.id];
            }
        });
    </script>
@endpushonce
