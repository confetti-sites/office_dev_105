<!--suppress HtmlUnknownAttribute -->
@php
    /** @var \Confetti\Components\SelectFile $model */
    $default = $model->getComponent()->getDecoration('default');
    $original = $model->get();
    $required = false;
@endphp
<div>
    <div class="block text-bold text-xl mt-8 mb-4">
        {{ $model->getComponent()->getLabel() }}
    </div>
    <select class="_select_file w-full pr-5 pl-3 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50"
            style="-webkit-appearance: none !important;-moz-appearance: none !important;" {{-- Remove default icon --}}
            name="{{ $model->getId() }}"
            data-original="{{ $original }}">
        @if(!$required)
            <option selected>Nothing selected</option>
        @endif
        @foreach($model->getOptions() as $child)
            <option value="{{ $child->getComponent()->source->getPath() }}"
                    @if($original === $child->getComponent()->source->getPath()) selected @endif
            >{{ $child->getComponent()->getLabel() }}</option>
        @endforeach
    </select>
</div>
@foreach($model->getOptions() as $pointerChild)
    @foreach($pointerChild->getChildren() as $grandChild)
        <div class="hidden"
             show_if="{{ $model->getId() }}"
             has_value="{{ $grandChild->getComponent()->source->getPath() }}">
            @include("admin.structure.{$grandChild->getComponent()->type}.component_admin", ['model' => $grandChild])
        </div>
    @endforeach
@endforeach
@pushonce('end_of_body_select_file')
    <script type="module">
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {Toolbar} from '/admin/assets/js/lim_editor.mjs';
        /** @see https://github.com/codex-team/icons */
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';

        // If value exists in local storage, set the value of the select element
        document.querySelectorAll('._select_file').forEach(select => {
            const value = Storage.getFromLocalStorage(select.name);
            if (value) {
                select.value = value;
            }

            checkStyle();
            function checkStyle() {
                if (select.value === select.dataset.original) {
                    select.classList.remove('border-emerald-300');
                    select.classList.add('border-gray-200');
                } else {
                    // Mark the select element as dirty
                    select.classList.remove('border-gray-200');
                    select.classList.add('border-emerald-300');
                }
            }

            function updateLocalStorage() {
                // If the value is the same as the original value,
                // remove the item from local storage
                if (select.value === select.dataset.original) {
                    Storage.removeLocalStorageItems(select.name);
                } else {
                    Storage.saveToLocalStorage(select.name, select.value);
                }
                window.dispatchEvent(new Event('local_content_changed'));
            }

            select.addEventListener('change', checkStyle);
            select.addEventListener('change', updateLocalStorage);

            // Attach toolbar to the holder of the select element
            const component = select.closest('div');
            new Toolbar(component).init([
                    {
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            Storage.removeLocalStorageItems(select.name);
                            let value = Storage.hasLocalStorageItem(select.name);
                            if (!value) {
                                value = select.dataset.original;
                            }
                            select.value = value;
                            // fire event change for the select element
                            select.dispatchEvent(new Event('change'));
                        }
                    },
                ],
            );
        });
        // Loop over every dit with show_if attribute
        document.querySelectorAll('[show_if]').forEach(element => {
            // Get the value of the show_if attribute
            const showIf = element.getAttribute('show_if');
            // Get the value of the has_value attribute
            const hasValue = element.getAttribute('has_value');
            // Get the select element with the name of the show_if attribute
            const select = document.querySelector(`[name="${showIf}"]`);
            // Add an event listener to the select element
            select.addEventListener('change', check);
            // Initial check
            check();

            function check() {
                // If the value of the select element is equal to the has_value attribute
                if (select.value === hasValue) {
                    // Show the element
                    element.classList.remove('hidden');
                } else {
                    // Hide the element
                    element.classList.add('hidden');
                }
            }
        });
    </script>
@endpushonce
