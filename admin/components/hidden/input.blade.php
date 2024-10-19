@php /** @var \Src\Components\HiddenComponent $model */ @endphp
        <!--suppress HtmlUnknownTag -->
<hidden-component
        data-id="{{ $model->getId() }}"
        data-original="{{ json_encode($model->get()) }}"
        data-component="{{ json_encode($model->getComponent()) }}"
        data-decorations='{{ json_encode($model->getComponent()->getDecorations()) }}'
></hidden-component>

@pushonce('end_of_body_hidden_component')
    <script type="module">
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('hidden-component', class extends HTMLElement {
            id
            original
            data
            decorations = {
                default: {default: null},
            }

            constructor() {
                super();
                this.id = this.dataset.id;
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
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                // Listen for value changes from other components. Other components
                // can push their value to this component using the value_pushed event:
                // window.dispatchEvent(new CustomEvent('value_pushed', {detail: {toId: '/model/banner/title', value: 'The title'}}));
                window.addEventListener('value_pushed', (event) => {
                    if (this.id !== event.detail['toId'] || event.detail['value'] === this.data.value) {
                        return;
                    }
                    this.data.value = event.detail['value'];
                });

                html`<input type="hidden" name="${this.id}" value="${() => this.data.value}"/>`(this)

                // We need to save some value (with component) to show it in the list
                if (this.data.value === null) {
                    this.data.value = this.decorations.default.default ? this.decorations.default.default : this.defaultWhenNoDefaultColor;
                }
            }
        });
    </script>
@endpushonce