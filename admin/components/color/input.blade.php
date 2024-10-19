@php /** @var \Src\Components\ColorComponent $model */ @endphp
        <!--suppress HtmlUnknownTag -->
<color-component
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-decorations='{{ json_encode($model->getComponent()->getDecorations()) }}'
        data-original="{{ json_encode($model->get()) }}"
        data-component="{{ json_encode($model->getComponent()) }}"
></color-component>

@pushonce('end_of_body_color_component')
    <style>
        color-component {
            & input[type=color] {
                height: 48px;
            }

            & input[type=color]::-webkit-color-swatch-wrapper {
                padding: 0;
            }

            & input[type=color]::-webkit-color-swatch {
                /*border: solid 1px #000; !*change color of the swatch border here*!*/
                border-width: 2px;
                border-radius: 0.5rem;
            }
        }
    </style>
    <script type="module">
        import {Toolbar} from '/admin/assets/js/editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('color-component', class extends HTMLElement {
            defaultWhenNoDefaultColor = '#ffffff';
            id
            label
            original
            data
            decorations = {
                help: {help: null},
                default: {default: null},
            }

            constructor() {
                super();
                this.id = this.dataset.id;
                this.label = this.dataset.label;
                this.original = JSON.parse(this.dataset.original);
                this.decorations = JSON.parse(this.dataset.decorations);
                this.data = reactive({
                    // If no value is given, we will save defaultWhenNoDefaultColor when the element is loaded
                    value: Storage.getFromLocalStorage(this.id) || this.original
                });
            }

            connectedCallback() {
                this.data.$on('value', value => {
                    Storage.removeLocalStorageModels(this.id);
                    if (value !== this.original) {
                        Storage.saveLocalStorageModel(this.id, value, this.dataset.component);
                    }
                    this.#checkStyle();
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.label}</label>
                    <input class="${() => ` block w-full ${this.data.value === this.original ? `border-gray-300` : `border-emerald-300`} outline-none text-gray-900 text-sm rounded-lg`}"
                           type="color"
                           name="${this.id}"
                           value="${() => this.data.value}"
                           @input="${(e) => this.data.value = e.target.value}">
                    ${this.decorations.help.help ? `<p class="mt-2 text-sm text-gray-500">${this.decorations.help.help}</p>` : ''}
                `(this)

                new Toolbar(this).init([{
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.querySelector('input').value = this.original;
                            this.querySelector('input').dispatchEvent(new Event('change'));
                            this.data.value = this.original || this.defaultWhenNoDefaultColor;
                        }
                    }],
                );

                // We need to save some value (with component) to show it in the list
                if (this.data.value === null) {
                    this.data.value = this.decorations.default.default ? this.decorations.default.default : this.defaultWhenNoDefaultColor;
                }
            }

            #checkStyle() {
                const input = this.querySelector('input');
                if (this.data.value === this.original) {
                    input.classList.remove('border-emerald-300');
                    input.classList.add('border-gray-200');
                } else {
                    // Mark the input element as dirty
                    input.classList.remove('border-gray-200');
                    input.classList.add('border-emerald-300');
                }
            }
        });
    </script>
@endpushonce