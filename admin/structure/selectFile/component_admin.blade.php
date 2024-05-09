<!--suppress HtmlUnknownAttribute, HtmlUnknownTag -->
@php
    /** @var \Confetti\Components\SelectFile $model */
    use Confetti\Helpers\ComponentStandard;
    $useLabelForRelative = $model->getComponent()->getDecoration('useLabelFor');
    $optionsValues = array_map(function ($option) {
        return [
            'source_path' => $option->getComponent()->source->getPath(),
            'label' => $option->getLabel(),
        ];
    }, $model->getOptions());
@endphp
<select-file-component
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-original="{{ $model->get() }}"
        data-default="{{  $model->getComponent()->getDecoration('default') }}"
        data-required="{{ $model->getComponent()->getDecoration('required') }}"
        data-use_label_for="{{ $useLabelForRelative ? ComponentStandard::mergeIds($model->getId(), $useLabelForRelative) : '' }}"
        data-options='@json($optionsValues)'
></select-file-component>

<select-file-children-templates>
@foreach($model->getOptions() as $pointerChild)
    @foreach($pointerChild->getChildren() as $grandChild)
        <template show_when="{{ $grandChild->getComponent()->source->getPath() }}">
            @include("admin.structure.{$grandChild->getComponent()->type}.component_admin", ['model' => $grandChild])
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
        import {Toolbar} from '/admin/assets/js/lim_editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('select-file-component', class extends HTMLElement {
            data

            connectedCallback() {
                this.data = reactive({
                    value: Storage.getFromLocalStorage(this.dataset.id) || this.dataset.original || '',
                });

                document.addEventListener('DOMContentLoaded', () => {
                    this.#checkStyle();
                    this.#useLabelFor();
                    this.#showChildren();
                });

                this.data.$on('value', value => {
                    Storage.removeLocalStorageItems(this.dataset.id);
                    if (value !== this.dataset.original) {
                        Storage.saveToLocalStorage(this.dataset.id, value);
                    }
                    this.#checkStyle();
                    this.#useLabelFor();
                    this.#showChildren();
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                const options = Object.values(JSON.parse(this.dataset.options));
                html`
                    <div><!-- @this, can this div be removed? -->
                        <div class="block text-bold text-xl mt-8 mb-4">
                            ${this.dataset.label}
                        </div>
                        <select class="w-full pr-5 pl-3 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50"
                                style="-webkit-appearance: none !important;-moz-appearance: none !important;" {{-- Remove default icon --}}
                name="${this.dataset.id}"
                                @change="${(e) => this.data.value = e.target.value}"
                        >
                            ${this.dataset.required === 'true' ? '' : html`
                    <option selected>Nothing selected</option>`}
                            ${options.map(option => html`
                                <option value="${option.source_path}"
                                        ${this.data.value === option.source_path ? 'selected' : ''}
                                >${option.label}</option>
                            `)}
                        </select>
                    </div>
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

            #checkStyle() {
                const select = this.querySelector('select');
                if (this.data.value === this.dataset.original) {
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
                if (!this.dataset.use_label_for) {
                    return;
                }

                const select = this.querySelector('select');
                window.dispatchEvent(new CustomEvent('value_pushed', {
                    detail: {
                        toId: this.dataset.use_label_for,
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