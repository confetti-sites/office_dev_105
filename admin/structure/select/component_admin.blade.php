@php /** @var \Confetti\Components\Select $model */ @endphp

<div data-type="select"
     data-options=@json($model->getComponent()->getDecoration('options'))
     data-original="{{ $model->get() }}"
     data-name="{{ $model->getId() }}"
     data-label="{{ $model->getComponent()->getLabel() }}"
     data-required="{{ $model->getComponent()->getDecoration('required') ? 'true' : ''}}"
     data-help="{{ $model->getComponent()->getDecoration('help') }}"
></div>


@pushonce('end_of_body_select')
    <script type="module">
        import {Component, Toolbar} from '/admin/assets/js/lim_editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';

        // Loop over all select components and render them
        for (const parent of document.querySelectorAll('[data-type="select"]')) {
            new Component(parent).render((html, reactive) => {
                const select = parent.querySelector('select');
                let data = reactive({
                    value: Storage.getFromLocalStorage(parent.dataset.name) || parent.dataset.original || '',
                });

                parent.addEventListener('change', () => data.value = select.value);
                data.$on('value', value => {
                    Storage.removeLocalStorageItems(parent.dataset.name);
                    if (value !== parent.dataset.original) {
                        Storage.saveToLocalStorage(parent.dataset.name, value);
                    }
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                new Toolbar(parent).init([{
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            select.value = parent.dataset.original;
                            select.dispatchEvent(new Event('change'));
                        }
                    }],
                );

                return html`
                    <label class="block text-bold text-xl mt-8 mb-4">${parent.dataset.label}</label>
                    <select class="${`appearance-none bg-gray-50 border border-gray-300 outline-none text-gray-900 text-sm rounded-lg block w-full p-3 focus:ring-0 focus:ring-offset-0`}
                            name=" ${parent.dataset.name}"
                    @input="${e => data.value = e.target.value}"
                    >
                    ${parent.dataset.required === 'true' ? '' : `<option value="">Nothing selected</option>`}
                    ${JSON.parse(parent.dataset.options).map(option => `
                            <option value="${option.id}" ${option.id === data.value ? 'selected' : ''}>${option.label}</option>
                        `).join('')}
                    </select>
                    ${parent.dataset.help ? `<p class="mt-2 text-sm text-gray-500">${parent.dataset.help}</p>` : ''}
                `;

            });
        }
    </script>
@endpushonce

