@php /** @var \Src\Components\BoolComponent $model */ @endphp
        <!--suppress HtmlUnknownTag -->
<bool-component
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-decorations='{{ json_encode($model->getComponent()->getDecorations()) }}'
        data-original="{{ json_encode($model->get()) }}"
        data-component="{{ json_encode($model->getComponent()) }}"
></bool-component>

@pushonce('end_of_body_bool_component')
    <script type="module">
        import {Toolbar} from '/admin/assets/js/editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('bool-component', class extends HTMLElement {
            id
            label
            original
            data
            decorations = {
                help: {help: null},
                default: {default: null},
                labelsOnOff: {on: '', off: ''}
            }

            constructor() {
                super();
                this.id = this.dataset.id;
                this.label = this.dataset.label;
                this.original = JSON.parse(this.dataset.original);
                this.decorations = JSON.parse(this.dataset.decorations);
                this.data = reactive({
                    // If no value is given, we will save 'false' when the element is loaded
                    value: Storage.getFromLocalStorage(this.id) || this.original || null
                });
            }

            connectedCallback() {
                this.data.$on('value', value => {
                    Storage.removeLocalStorageModels(this.id);
                    if (value !== this.original) {
                        Storage.saveLocalStorageModel(this.id, value, this.dataset.component);
                    }
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                html`
                    <div class="flex items center mt-8 mb-4 flex items center space-x-2" @click="${() => this.data.value = !this.data.value}">
                        <div class="relative inline-block w-12 h-7 align-middle select-none toggle-label block overflow-hidden rounded-full cursor-pointer bg-gray-200 border-2 border-gray-200">
                            <input type="checkbox" name="${this.id}" id="${this.id}"
                                   class="${() => 'absolute block w-6 h-6 rounded-full appearance-none cursor-pointer transition-all duration-1000 ease-in-out border-1 border-gray-100 ' + (this.data.value === true ? 'bg-emerald-600 right-0' : 'bg-gray-500')}"
                                   checked="${() => this.data.value}"
                            />
                        </div>
                        <span class="text-bold text-l mt-1">${() => this.#getLabel()}</span>
                    </div>
                    ${this.decorations.help.help ? `<p class="mt-2 text-sm text-gray-500">${this.decorations.help.help}</p>` : ''}
                `(this);

                new Toolbar(this).init([{
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.querySelector('input').value = this.original;
                            this.querySelector('input').dispatchEvent(new Event('change'));
                            this.data.value = this.original || null;
                        }
                    }],
                );

                // We need to save some value (with component) to show it in the list
                if (this.data.value === null) {
                    this.data.value = this.decorations.default.default ? this.decorations.default.default : false;
                    Storage.saveLocalStorageModel(this.id, this.data.value, this.dataset.component);
                }
            }

            #getLabel() {
                if (this.decorations.labelsOnOff !== undefined) {
                    return this.data.value ? this.decorations.labelsOnOff.on : this.decorations.labelsOnOff.off;
                }
                return this.label;
            }
        });
    </script>
@endpushonce