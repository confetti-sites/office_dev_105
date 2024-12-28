@php /** @var \Src\Components\SelectComponent $model */ @endphp
        <!--suppress HtmlUnknownTag, HtmlUnknownAttribute, JSUnresolvedReference -->
<select-component
        data-component="{{ json_encode($model->getComponent()) }}"
        data-decorations='{{ json_encode($model->getComponent()->getDecorations()) }}'
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-original="{{ json_encode($model->get()) }}"
        data-source="{{ $model->getComponent()->source }}"
></select-component>

@pushonce('end_of_body_select_component')
    <style>
        /* Remove the default focus-visible border */
        select-component:focus {
            outline: none;
        }
    </style>
    <script type="module">
        import {Toolbar} from '/admin/assets/js/editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('select-component', class extends HTMLElement {
            id
            label
            data
            original
            source
            decorations = {
                help: {help: null},
                default: {default: null},
                options: {options: null},
            }

            constructor() {
                super();
                this.id = this.dataset.id;
                this.label = this.dataset.label;
                this.original = JSON.parse(this.dataset.original);
                this.source = this.dataset.source;
                this.decorations = JSON.parse(this.dataset.decorations);
                this.data = reactive({
                    // If no value is given, we will save defaultWhenNoDefaultColor when the element is loaded
                    value: Storage.getFromLocalStorage(this.id) || this.original
                });
                // If no value is saved in the local storage, we will save the default value
                if (this.data.value === null) {
                    this.data.value = this.decorations.default.default ? this.decorations.default.default : '';
                    Storage.saveLocalStorageModel(this.id, this.data.value, this.dataset.component);
                }
            }

            connectedCallback() {
                this.data.$on('value', value => {
                    Storage.removeLocalStorageModels(this.id);
                    if (value !== this.original) {
                        Storage.saveLocalStorageModel(this.id, value, this.dataset.component);
                    }
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                const options = this.decorations.options.options;

                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.label}</label>
                    <select class="${() => `appearance-none pr-5 pl-3 py-3 bg-gray-50 border-2 ${this.data.value === this.original ? `border-gray-300` : `border-emerald-300`} outline-none text-gray-900 text-sm rounded-lg block w-full`}"
                            name="${this.id}"
                            @input="${e => this.data.value = e.target.value}">
                        ${this.decorations.required.required === true ? '' : `<option value="">Nothing selected</option>`}
                        ${this.decorations.options.options === null ? '' : options.map(option =>
                    `<option value="${option.id}" ${option.id === this.data.value ? 'selected' : ''}>${option.label}</option>`
                )}
                    </select>
                    ${options === null ? html`<p class="mt-2 text-sm text-red-500">Error for developer: ⚠ No decorator \`options\` found. Please add \`->options(['first', 'second'])\` in ${this.source}</p>` : ''}
                    ${this.decorations.help !== undefined ? `<p class="mt-2 text-sm text-gray-500">${this.decorations.help.help}</p>` : ''}
                `(this)

                new Toolbar(this).init([{
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.querySelector('select').value = this.original;
                            this.querySelector('select').dispatchEvent(new Event('change'));
                            this.data.value = this.original || this.decorations.default.default;
                        }
                    }],
                );
            }
        });
    </script>
@endpushonce