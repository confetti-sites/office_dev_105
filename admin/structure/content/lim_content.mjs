// noinspection JSUnusedGlobalSymbols

/** @see https://github.com/codex-team/icons */
import {IconEtcVertical, IconUndo} from 'https://esm.sh/@codexteam/icons';

export class Toolbar {
    /**
     * @type {Editor}
     */
    editor;

    /**
     * @param {Editor} editor
     */
    constructor(editor) {
        console.log(editor);
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
        let toSave = null;
        // If blocks are empty, we need to set it to null
        if (value.blocks.length !== 0) {
            // Use JSON.stringify to encode special characters
            toSave = JSON.stringify(value);
        }
        localStorage.setItem(this.editor.configuration.id, toSave);
    }

    /**
     * @returns {Toolbar}
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
        let component = this.editor.configuration.element;
        this.#createToolbar(component);

        // Ensure that the value is updated when the page is loaded
        this.updateValueChangedStyle();

        return this
    }

    async onChange(api, events) {
        // if not array, make an array
        if (!Array.isArray(events)) {
            events = [events];
        }

        for (const event of events) {
            if (event.type !== 'block-changed') {
                continue;
            }

            // Update data
            this.storageData = await api.saver.save();
            // Update the style
            this.updateValueChangedStyle();
        }
    }

    renderSettings() {
        return [
            {
                label: 'Revert to saved value',
                icon: IconUndo,
                closeOnActivate: true,
                onActivate: async () => {
                    this.editor.blocks.render(this.editor.configuration.originalData);
                    this.storageData = this.editor.configuration.originalData;
                    this.updateValueChangedStyle();
                }
            },
        ];
    }

    updateValueChangedStyle() {
        const inputHolder = this.editor.configuration.element.querySelector('._input');
        if (this.#isChanged()) {
            inputHolder.classList.remove('border-gray-200');
            inputHolder.classList.add('border-cyan-300');
        } else {
            inputHolder.classList.remove('border-cyan-300');
            inputHolder.classList.add('border-gray-200');
        }
    }

    #isChanged() {
        let original = '';
        let changed = '';
        // The Value can be null, when it's not set in local storage.
        if (this.editor.configuration.originalData !== null) {
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

    // @todo step 1 move cfreateToolbar to mjs file

    #createToolbar(component) {

        // Component needs to be relative
        component.style.position = 'relative';

        // Create the toolbar
        let toolbar = document.createElement('div');
        toolbar.style.position = 'absolute';
        toolbar.style.right = '0';
        toolbar.style.top = '0';
        toolbar.style.marginTop = '2rem';
        toolbar.style.className = 'ce-toolbar__actions';
        // Align the toolbar to the right
        toolbar.style.display = 'flex';
        toolbar.style.flexDirection = 'row';
        toolbar.style.justifyContent = 'flex-end';
        toolbar.style.alignItems = 'center';
        toolbar.style.width = '100%';
        toolbar.style.padding = '0 1.2rem';

        // Button with icon 3 dots
        let settingBtn = document.createElement('span');
        settingBtn.className = 'ce-toolbar__settings-btn';
        settingBtn.style.cursor = 'pointer';
        settingBtn.style.zIndex = '103';
        settingBtn.innerHTML = IconEtcVertical;

        // Create the settings popover
        let settings = document.createElement('div');
        settings.className = 'ce-settings';
        settings.style.zIndex = '100';

        // Create the overlay
        let overlay = document.createElement('div');
        overlay.className = 'ce-popover__overlay';
        overlay.style.position = 'fixed';
        overlay.style.display = 'none';
        overlay.style.top = '0px';
        overlay.style.left = '0px';
        overlay.style.right = '0px';
        overlay.style.bottom = '0px';
        overlay.style.zIndex = '101';
        overlay.style.overflow = 'hidden';
        settings.appendChild(overlay);

        let popover = document.createElement('div');
        popover.className = 'ce-popover';
        popover.style.right = '15px';
        popover.style.left = 'auto';
        popover.style.left = 'initial';
        popover.style.zIndex = '104';

        let items = document.createElement('div');
        items.className = 'ce-popover__items';

        let itemsData = this.renderSettings();
        for (let itemData of itemsData) {
            let item = document.createElement('div');
            item.className = 'ce-popover-item';
            // Call onActivate when clicked
            item.addEventListener('click', itemData.onActivate);
            // Close the popover when clicked
            if (itemData.closeOnActivate) {
                item.addEventListener('click', () => {
                    popover.classList.remove('ce-popover--opened');
                });
            }
            let icon = document.createElement('div');
            icon.className = 'ce-popover-item__icon';
            icon.innerHTML = itemData.icon;
            let title = document.createElement('div');
            title.className = 'ce-popover-item__title';
            title.innerText = itemData.label;
            item.appendChild(icon);
            item.appendChild(title);
            items.appendChild(item);
        }

        popover.appendChild(items);
        overlay.addEventListener('click', () => {
            popover.classList.remove('ce-popover--opened');
        });

        // if popover is opened, set show overlay by set display block
        // if popover is closed, set display none
        toolbar.addEventListener('click', () => {
            if (popover.classList.contains('ce-popover--opened')) {
                overlay.style.display = 'block';
            } else {
                overlay.style.display = 'none';
            }
        });

        settings.appendChild(popover);
        toolbar.appendChild(settings);

        // Open the settings popover
        settingBtn.addEventListener('click', () => {
            popover.classList.toggle('ce-popover--opened');
        });

        toolbar.appendChild(settingBtn);
        toolbar.appendChild(settings);
        component.prepend(toolbar);
    }
}