<!--suppress HtmlUnknownAttribute -->
@php
    /** @var \Confetti\Components\SelectFile $model */
    $default = $model->getComponent()->getDecoration('default');
@endphp
<div class="block text-bold text-xl mt-8 mb-4">
    {{ $model->getComponent()->getLabel() }}
</div>
<select class="_select_file appearance-none bg-gray-50 border border-gray-300 outline-none text-gray-900 text-sm rounded-lg block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-emerald-700 dark:focus:border-emerald-700"
        name="{{ $model->getId() }}">
    @if(!$default)
        <option selected disabled>Choose an option</option>
    @endif
    @foreach($model->getOptions() as $child)
        <option value="{{ '/' . $child->getComponent()->source->getPath() }}"
                @if($default === '/' . $child->getComponent()->source->getPath()) selected @endif
        >{{ $child->getComponent()->getLabel() }}</option>
    @endforeach
</select>
@foreach($model->getOptions() as $pointerChild)
    @foreach($pointerChild->getChildren() as $grandChild)
        <div class="hidden"
             show_if="{{ $model->getId() }}"
             has_value="{{ '/' . $grandChild->getComponent()->source->getPath() }}">
            @include("admin.structure.{$grandChild->getComponent()->type}.component_admin", ['model' => $grandChild])
        </div>
    @endforeach
@endforeach
@pushonce('end_of_body_select')
    <script type="module">
        import {storage} from '/admin/assets/js/admin_service.mjs';
        // If value exists in local storage, set the value of the select element
        document.querySelectorAll('._select_file').forEach(select => {
            const value = storage.getFromLocalStorage(select.name);
            if (value) {
                select.value = value;
            }
        });
        document.querySelectorAll('._select_file').forEach(select => {
            select.addEventListener('change', () => {
                storage.saveToLocalStorage(select.name, select.value);
            });
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
