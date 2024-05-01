@php /** @var \Confetti\Components\Select $model */ @endphp

<select-component
    data-name="{{ $model->getId() }}"
    data-original="{{ $model->get() }}"
    data-label="{{ $model->getComponent()->getLabel() }}"
    data-required="{{ $model->getComponent()->getDecoration('required') ? 'true' : ''}}"
    data-help="{{ $model->getComponent()->getDecoration('help') }}"
    data-options=@json($model->getComponent()->getDecoration('options'))
></select-component>

@pushonce('end_of_body_select_component')
    <script type="module">
        import {Toolbar} from '/admin/assets/js/lim_editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('select-component', class extends HTMLElement {
            connectedCallback() {
                const options = JSON.parse(this.dataset.options)
                let data = reactive({
                    value: Storage.getFromLocalStorage(this.dataset.name) || this.dataset.original || '',
                });

                data.$on('value', value => {
                    Storage.removeLocalStorageItems(this.dataset.name);
                    if (value !== this.dataset.original) {
                        Storage.saveToLocalStorage(this.dataset.name, value);
                    }
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.dataset.label}</label>
                    <select class="${() => `appearance-none pr-5 pl-3 py-3 bg-gray-50 border-2 ${data.value === this.dataset.original ? `border-gray-300` : `border-emerald-300`} outline-none text-gray-900 text-sm rounded-lg block w-full`}"
                            name="${this.dataset.name}"
                    @input="${e => data.value = e.target.value}">
                    ${this.dataset.required === 'true' ? '' : `<option value="">Nothing selected</option>`}
                    ${options.map(option =>
                            `<option value="${option.id}" ${option.id === data.value ? 'selected' : ''}>${option.label}</option>`
                    ).join('')}
                    </select>
                    ${this.dataset.help ? `<p class="mt-2 text-sm text-gray-500">${this.dataset.help}</p>` : ''}
                `(this)

                new Toolbar(this).init([{
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.querySelector('select').value = this.dataset.original;
                            this.querySelector('select').dispatchEvent(new Event('change'));
                            data.value = this.dataset.original;
                        }
                    }],
                );
            }
        }
    </script>
@endpushonce