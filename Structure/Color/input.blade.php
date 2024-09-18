@php /** @var \Confetti\Components\Hidden $model */ @endphp
<!--suppress HtmlUnknownTag -->
<hidden-component
        data-id="{{ $model->getId() }}"
        data-original="{{ $model->get() }}"
></hidden-component>

@pushonce('end_of_body_hidden_component')
    <script type="module">
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('hidden-component', class extends HTMLElement {
            connectedCallback() {
                let data = reactive({
                    value: Storage.getFromLocalStorage(this.dataset.id) || this.dataset.original || '',
                });

                data.$on('value', value => {
                    Storage.removeLocalStorageItems(this.dataset.id);
                    if (value !== this.dataset.original) {
                        Storage.saveToLocalStorage(this.dataset.id, value);
                    }
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                // Listen for value changes from other components. Other components
                // can push their value to this component using the value_pushed event:
                // window.dispatchEvent(new CustomEvent('value_pushed', {detail: {toId: '/model/banner/title', value: 'The title'}}));
                window.addEventListener('value_pushed', (event) => {
                    if (this.dataset.id !== event.detail['toId'] || event.detail['value'] === data.value) {
                        return;
                    }
                    data.value = event.detail['value'];
                });

                html`<input type="hidden" name="${this.dataset.id}" value="${() => data.value}"/>`(this)
            }
        });
    </script>
@endpushonce