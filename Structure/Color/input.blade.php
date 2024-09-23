@php /** @var \Confetti\Components\Hidden $model */ @endphp
<!--suppress HtmlUnknownTag -->
<color-component
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-help="{{ $model->getComponent()->getDecoration('help') }}"
        data-decorations='@json($model->getComponent()->getDecorations())'
        data-original="{{ $model->get() }}"
></color-component>

@pushonce('end_of_body_color_component')
    <style>
        color-component {
            & input[type=color]{
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
        import {Toolbar} from '/admin/assets/js/lim_editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('color-component', class extends HTMLElement {
            data

            connectedCallback() {
                this.data = reactive({
                    value: Storage.getFromLocalStorage(this.dataset.id) || this.dataset.original || '',
                });

                this.data.$on('value', value => {
                    console.log('color-component', value);
                    Storage.removeLocalStorageItems(this.dataset.id);
                    if (value !== this.dataset.original) {
                        Storage.saveToLocalStorage(this.dataset.id, value);
                    }
                    this.#checkStyle();
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.dataset.label}</label>
                    <input class="${() => ` block w-full ${this.data.value === this.dataset.original ? `border-gray-300` : `border-emerald-300`} outline-none text-gray-900 text-sm rounded-lg`}"
                            type="color"
                            name="${this.dataset.id}"
                            value="${() => this.data.value}"
                            @input="${(e) => this.data.value = e.target.value}">

                    ${this.dataset.help ? `<p class="mt-2 text-sm text-gray-500">${this.dataset.help}</p>` : ''}
                `(this)

                new Toolbar(this).init([{
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.querySelector('input').value = this.dataset.original;
                            this.querySelector('input').dispatchEvent(new Event('change'));
                            this.data.value = this.dataset.original;
                        }
                    }],
                );
            }

            #checkStyle() {
                const input = this.querySelector('input');
                if (this.data.value === this.dataset.original) {
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