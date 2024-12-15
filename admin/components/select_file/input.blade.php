<!--suppress HtmlUnknownAttribute, HtmlUnknownTag, PhpParamsInspection -->
@php
    /** @var \Src\Components\SelectFileComponent $model */
    use Confetti\Helpers\ComponentStandard;
    $useLabelForRelative = $model->getComponent()->getDecoration('useLabelFor');
    $optionsValues = array_map(function ($option) {
        return [
            'source_path' => $option->getComponent()->source->getPath(),
            'label' => $option->getLabel(),
        ];
    }, $model->getOptions());
@endphp
<!-- suppress HtmlUnknownTag -->
<select-file-component
        data-component="{{ json_encode($model->getComponent()) }}"
        data-decorations='{{ json_encode($model->getComponent()->getDecorations()) }}'
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-options="{{ json_encode($optionsValues) }}"
        data-original="{{ json_encode($model->get()) }}"
        data-source="{{ (string) $model->getComponent()->source }}"
        data-use_label_for="{{ $useLabelForRelative ? ComponentStandard::mergeIds($model->getId(), $useLabelForRelative) : '' }}"
></select-file-component>

<select-file-children-templates>
    @foreach($model->getOptions() as $pointerChild)
        @foreach($pointerChild->getChildren() as $grandChild)
            <template show_when="{{ $grandChild->getComponent()->source->getPath() }}">
                @include($grandChild->getViewAdminInput(), ['model' => $grandChild])
            </template>
        @endforeach
    @endforeach
</select-file-children-templates>
<template-result></template-result>

@pushonce('end_of_body_select_file_component')
    <style>
        select-file-component {
            /* Remove the default focus-visible border */

            & ._select_file:focus {
                outline: none;
            }
        }
    </style>
    <script type="module">
        import {Toolbar} from '/admin/assets/js/editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('select-file-component', class extends HTMLElement {
            id
            label
            data
            original
            source
            decorations = {
                help: {help: null},
                default: {default: null},
                match: {matches: null},
                required: null,
            }
            use_label_for
            options = {
                source_path: '',
                label: '',
            }

            constructor() {
                super();
                this.id = this.dataset.id;
                this.label = this.dataset.label;
                this.original = JSON.parse(this.dataset.original);
                this.source = this.dataset.source;
                this.decorations = JSON.parse(this.dataset.decorations);
                this.use_label_for = this.dataset.use_label_for;
                this.options = Object.values(JSON.parse(this.dataset.options));
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
                // We need to wait for the element to be rendered
                setTimeout(() => {
                    this.#checkStyle();
                    this.#useLabelFor();
                    this.#showChildren();
                }, 1);

                this.data.$on('value', value => {
                    Storage.removeLocalStorageModels(this.id);
                    if (value !== this.original) {
                        Storage.saveLocalStorageModel(this.id, value, this.dataset.component);
                    }
                    this.#checkStyle();
                    this.#useLabelFor();
                    this.#showChildren();
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                html`<div class="block text-bold text-xl mt-8 mb-4">
                        ${this.label}
                    </div>
                    <select class="w-full pr-5 pl-3 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50"
                            style="-webkit-appearance: none !important;-moz-appearance: none !important;" {{-- Remove default icon --}}
                name="${this.id}"
                            @change="${(e) => this.data.value = e.target.value}"
                    >
                        ${this.decorations.required !== undefined && this.decorations.required.required ? '' : html`
                    <option selected>Nothing selected</option>`}
                        ${this.options.map(option => html`
                    <option value="${option.source_path}" ${this.data.value === option.source_path ? 'selected' : ''}
                    >${option.label}
                    </option>
                `)}
                    </select>
                    ${this.decorations.match.matches !== null ? '' : html`
                    <p class="mt-2 text-sm text-red-500">Error for developer: ⚠ No decorator \`match\` found. Please add \`->match(['/website/includes/*.blade.php'])\` in ${this.source}</p>`}
                `(this)

                new Toolbar(this).init([{
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.querySelector('select').value = this.original || this.decorations.default.default;
                            this.querySelector('select').dispatchEvent(new Event('change'));
                            data.value = this.original || this.decorations.default.default;
                        }
                    }],
                );
            }

            #checkStyle() {
                const select = this.querySelector('select');
                if (this.data.value === this.original) {
                    select.classList.remove('border-emerald-300');
                    select.classList.add('border-gray-200');
                } else {
                    // Mark the select element as dirty
                    select.classList.remove('border-gray-200');
                    select.classList.add('border-emerald-300');
                }
            }

            // With the use_label_for attribute, we can send
            // the value of the select element to another component
            #useLabelFor() {
                if (!this.use_label_for) {
                    return;
                }

                const select = this.querySelector('select');
                window.dispatchEvent(new CustomEvent('value_pushed', {
                    detail: {
                        toId: this.use_label_for,
                        value: select.options[select.selectedIndex].innerHTML,
                    }
                }));
            }

            #showChildren() {
                const select = this.querySelector('select');
                // Get all the children of the select element (template-results)
                const templates = this.nextElementSibling.children;
                const result = this.nextElementSibling.nextElementSibling;

                result.innerHTML = '';
                // Loop through all the children
                for (let template of templates) {
                    // If the value of the select element is equal to the show_when attribute
                    if (select.value === template.getAttribute('show_when')) {
                        result.appendChild(template.content.cloneNode(true));
                    }
                }
            }
        })
    </script>
@endpushonce
