@php /** @var \Confetti\Components\Select $model */ @endphp

<div data-type="select"
     data-options=@json($model->getComponent()->getDecoration('options'))
     data-name="{{ $model->getId() }}"
     data-label="{{ $model->getComponent()->getLabel() }}"
     data-required="{{ $model->getComponent()->getDecoration('required') ? 'true' : ''}}"
     data-help="{{ $model->getComponent()->getDecoration('help') }}"
></div>

@pushonce('end_of_body_select')
    <script type="module">
        import {Component} from '/admin/assets/js/lim_editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';

        // Loop over all select components and render them
        for (const parent of document.querySelectorAll('[data-type="select"]')) {
            new Component(parent).render((html, reactive) => {
                let data = reactive({
                    value: Storage.getFromLocalStorage(parent.dataset.name) || '',
                });

                data.$on('value', value => {
                    Storage.saveToLocalStorage(parent.dataset.name, value);
                    document.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                return html`
                    <label class="block text-bold text-xl mt-8 mb-4">${parent.dataset.label}</label>
                    <select class="appearance-none bg-gray-50 border border-gray-300 outline-none text-gray-900 text-sm rounded-lg block w-full p-3"
                            name="${parent.dataset.name}"
                            @input="${e => data.value = e.target.value}"
                    >
                        ${parent.dataset.required === 'true' ? '' : `<option value="">Nothing selected</option>`}
                        ${JSON.parse(parent.dataset.options).map(option => `
                            <option value="${option.id}" ${option.id === data.value ? 'selected' : ''}>${option.label}</option>
                        `).join('')}
                    </select>
                    ${parent.dataset.help ? `<p class="mt-2 text-sm text-gray-500">${parent.dataset.help}</p>` : ''}
                    <em>${() => data.value}</em>
                `;

            });
        }
    </script>
@endpushonce

