// noinspection JSUnusedGlobalSymbols

/** @see https://github.com/codex-team/icons */
import {IconEtcVertical, IconUndo} from 'https://esm.sh/@codexteam/icons';
import {Toolbar} from "/admin/assets/js/editor.mjs";

export default class LimContent {
    /**
     * @type {Editor}
     */
    editor;

    /**
     * @param {Editor} editor
     */
    constructor(editor) {
        this.editor = editor;
    }

    /**
     * E.g. {"time":1712349766517,"blocks":[{"id":"1Z7S3FP926","type":"paragraph","data":{"text":"The cool blog title"}}],"version":"2.29.1"}
     * @returns {Data}
     */
    get storageData() {
        return JSON.parse(localStorage.getItem(this.editor.configuration.id));
    }

    /**
     * If the value is null, it will be null in local storage
     * and removed from the database.
     * @param {Data|null} value
     */
    set storageData(value) {
        // if value is same as original value, remove it from local storage
        if (value === this.editor.configuration.originalData) {
            localStorage.removeItem(this.editor.configuration.id);
            window.dispatchEvent(new Event('local_content_changed'));
            return;
        }
        let toSave = null;
        // If blocks are empty, we need to set it to null
        if (value.blocks.length !== 0) {
            // Use JSON.stringify to encode special characters
            toSave = JSON.stringify(value);
        }
        localStorage.setItem(this.editor.configuration.id, toSave);
        // Store the component entity in local storage
        // We need to know which component is saved in the local storage
        // That way we can show the data in the list view
        localStorage.setItem('/component' + this.editor.configuration.id, this.editor.configuration.config.componentEntity);
        // Trigger an event to let the other code know that the content has changed
        window.dispatchEvent(new Event('local_content_changed'));
    }

    /**
     * @returns {LimContent}
     */
    init() {
        // Replace the default editor.js 6-dot settings icon with a 3-dot icon
        // Icons aren't loaded yet, so we need to wait a bit.
        setTimeout(() => {
            const holder = this.editor.configuration.holder;
            const element = this.editor.configuration.element;
            element.querySelector('#' + holder).querySelector('.ce-toolbar__settings-btn').innerHTML = IconEtcVertical;
        }, 20);

        // Add the toolbar to the editor
        new Toolbar(this.editor.configuration.element).init([{
            label: 'Remove unpublished changes',
            icon: IconUndo,
            closeOnActivate: true,
            onActivate: async () => {
                this.editor.blocks.render(this.editor.configuration.originalData || this.editor.configuration.defaultData);
                this.storageData = this.editor.configuration.originalData || this.editor.configuration.defaultData;
                this.updateValueChangedStyle();
            }}],
        );

        // Ensure that the value is updated when the page is loaded
        this.updateValueChangedStyle();

        return this
    }

    async onChange(api, events) {
        // if not array, make an array
        if (!Array.isArray(events)) {
            events = [events];
        }

        if (events.length === 0) {
            return;
        }

        // Update data
        this.storageData = await api.saver.save();
        // Update the style
        this.updateValueChangedStyle();
    }

    updateValueChangedStyle() {
        const inputHolder = this.editor.configuration.element.querySelector('._input');
        if (this.#isChanged()) {
            inputHolder.classList.remove('border-gray-200');
            inputHolder.classList.add('border-emerald-300');
        } else {
            inputHolder.classList.remove('border-emerald-300');
            inputHolder.classList.add('border-gray-200');
        }
    }

    #isChanged() {
        // check if key is present in local storage, without checking on null
        if (!localStorage.hasOwnProperty(this.editor.configuration.id)) {
            return false;
        }

        let original = '';
        let changed = '';
        // The Value can be null, when it's not set in local storage.
        if (this.editor.configuration.originalData !== null && this.editor.configuration.originalData.blocks !== undefined) {
            // Foreach over blocks.*.data and add to string, to check original and changed
            for (const block of this.editor.configuration.originalData.blocks) {
                original += JSON.stringify(block.data);
            }
        }
        if (this.storageData !== null) {
            // Foreach over blocks.*.data and add to string, to check original and changed
            for (const block of this.storageData.blocks) {
                changed += JSON.stringify(block.data);
            }
        }
        return original !== changed;
    }
}