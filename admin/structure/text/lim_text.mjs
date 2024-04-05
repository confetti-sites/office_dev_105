// noinspection JSUnusedGlobalSymbols
/** @see https://github.com/editor-js/paragraph/blob/master/src/index.js */
import Paragraph from 'https://esm.sh/@editorjs/paragraph@^2';
/** @see https://github.com/codex-team/icons */
import {IconEtcVertical, IconUndo} from 'https://esm.sh/@codexteam/icons';

/**
 * @typedef {object} Config
 * @property {string} contentId
 * @property {string} originalValue
 * @property {HTMLElement} component
 * @property {object} decorations
 * @property {function[]} validators
 * @property {array} renderSettings
 */

/**
 * @typedef {object} Api
 * @property {object} blocks
 * @property {function} blocks.getBlockByIndex
 * @property {function} blocks.delete
 * @property {function} blocks.update
 * @property {function} blocks.render
 * @property {object} caret
 * @property {function} caret.setToBlock
 * @property {object} events
 * @property {object} listeners
 * @property {object} notifier
 * @property {object} sanitizer
 * @property {object} saver
 * @property {function} saver.save
 * @property {object} selection
 * @property {object} styles
 * @property {object} toolbar
 * @property {object} inlineToolbar
 * @property {object} tooltip
 * @property {object} i18n
 * @property {object} readOnly
 * @property {object} ui
 */

/**
 * We are grateful to the CodeX team for all their hard work.
 * For more information, see the links above or go to https://editorjs.io/
 *
 * This is a custom implementation of the paragraph block.
 * We extend the paragraph block for the following reasons:
 * - It's a workaround to ensure that there is only one block.
 * - To ensure that the value is stored.
 * - The user can revert to the saved value.
 * - Validate the input and show an error message.
 */
export class LimText extends Paragraph {
    /**
     * Render plugin's main Element and fill it with saved data
     *
     * @param {object} params - constructor params
     * @param {object} params.data - previously saved data
     * @param {Config} params.config - user config for Tool
     * @param {Api} params.api - editor.js api
     * @param {boolean} readOnly - read only mode flag
     */

    constructor({data, config, api, readOnly}) {
        super({data, config, api, readOnly});
        this.config = config;
    }

    /**
     * @return {string}
     */
    get storageValue() {
        return localStorage.getItem(this.config.contentId);
    }

    /**
     * @param {string} value
     */
    set storageValue(value) {
        // Use JSON.stringify to encode special characters
        localStorage.setItem(this.config.contentId, value);
    }

    /**
     * So we can use `block.call('setStorageValue', 'value')`
     * @param value
     */
    setStorageValue(value) {
        this.storageValue = value;
    }

    initStyles() {
        const pr = '#' + this.config.component.id;
        let e = document.createElement("style");
        /* Hide the toolbar items so the user can't add new blocks */
        let css = ``
        css += `${pr} .ce-toolbar__plus, ${pr} .cdx-search-field,${pr} .ce-popover-item[data-item-name="move-up"],${pr} .ce-popover-item[data-item-name="delete"],${pr} .ce-popover-item[data-item-name="move-down"] {display: none;}`;
        /* With a big screen, the text is indeed to the right */
        css += `${pr} .ce-block__content, ${pr} .ce-toolbar__content {max-width: unset;}`;
        /* Remove default editor.js padding */
        css += `${pr} .cdx-block {padding: 0;}`;
        /* Add padding to the inline tools */
        css += `${pr} .ce-inline-tool {padding: 12px;}`;
        e.appendChild(document.createTextNode(css));
        document.head.appendChild(e);
    }

    /**
     * @param {string} value
     */
    updateValueChangedStyle(value) {
        const inputHolder = this.config.component.querySelector('._input');
        const message = this._validateWithMessage(value);
        if (message != null) {
            inputHolder.classList.add('border-red-200');
            this.config.component.getElementsByClassName('_error')[0].innerHTML = message;
            return;
        }
        // Remove the error message
        this.config.component.getElementsByClassName('_error')[0].innerText = '';
        inputHolder.classList.remove('border-red-200');
        // Value can be null, when it's not set in local storage.
        if (value !== null && value !== this.config.originalValue) {
            inputHolder.classList.remove('border-gray-200');
            inputHolder.classList.add('border-cyan-300');
        } else {
            inputHolder.classList.remove('border-cyan-300');
            inputHolder.classList.add('border-gray-200');
        }
    }

    render() {
        this.initStyles();
        // Set the correct style corresponding to the value
        this.updateValueChangedStyle(this.storageValue);

        setTimeout(() => {
            /* Replace the default editor.js 6 dots settings icon with a 3-dots icon */
            this.config.component.querySelector('.ce-toolbar__settings-btn').innerHTML = IconEtcVertical;
        }, 100);

        // Set current value
        this._data.text = localStorage.getItem(this.config.contentId) ?? this.config.originalValue;

        // Call the original render function
        return super.render();
    }

    /**
     * @returns {*&{closeOnActivate: boolean, onActivate: function(): Promise<void>, icon: *, label: string}}
     */
    renderSettings() {
        // merge the default settings with this.config.renderSettings
        let defaultSetting = {
            label: 'Revert to saved value',
            icon: IconUndo,
            closeOnActivate: true,
            onActivate: async () => {
                // Save the value in local storage
                let block = this.api.blocks.getBlockByIndex(0);
                block.call('setStorageValue', this.config.originalValue);

                // Render the original value
                this.api.blocks.render({
                    blocks: [{
                        type: "paragraph", data: {
                            text: this.config.originalValue
                        }
                    }]
                });
            }
        };
        console.log(this.config);

        return [...this.config.renderSettings, defaultSetting];
    }

    static async onChange(api, events) {
        // if not array, make an array
        if (!Array.isArray(events)) {
            events = [events];
        }
        const component = await api.saver.save()

        // Ensure that the value is updated when the user types
        LimText._changed(api, events, component);

        // Ensure that there is only one block
        LimText._ensureOneBlock(api, events, component);
    }

    /**
     * Every time the user types, this function is called.
     *
     * @param {Api} api
     * @param {Array} events
     * @param {object} component
     */
    static _changed(api, events, component) {
        for (const event of events) {
            if (event.type !== 'block-changed') {
                continue;
            }
            // If there are more than one block, first the
            // block-added code needs to flatten the blocks.
            if (component.blocks.length > 1) {
                break;
            }
            let value = '';
            if (component.blocks.length > 0) {
                value = component.blocks[0].data.text;
            }
            let block = api.blocks.getBlockByIndex(0);
            // Save the value in local storage
            block.call('setStorageValue', value);
            // Update the style
            block.call('updateValueChangedStyle', value);
        }
    }


    /**
     * This is a workaround to ensure that there is only one block.
     * Because Editor.js doesn't allow easy configuration to have only one block.
     *
     * @param {Api} api
     * @param {Array} events
     * @param {object} component
     */
    static _ensureOneBlock(api, events, component) {
        // Compose the text of all blocks
        let text = '';
        component.blocks.forEach((block) => {
            text += ' ' + block.data.text;
        });

        let blockAdded = false;
        for (const event of events) {
            // We are only interested in the blocks that are added.
            // And we only want to remove the block if it's not the first block.
            if (event.type !== 'block-added' || event.detail.index === 0) {
                continue;
            }
            api.blocks.delete();
            blockAdded = true;
        }
        if (blockAdded) {
            const firstBlock = component.blocks[0]
            api.blocks.update(firstBlock.id, {text: text})
            setTimeout(() => {
                api.caret.setToBlock(0, 'end')
            }, 20);
        }
    }


    /**
     * We don't use the default validation, because we want to interact with the ui.
     * @param {string} value
     */
    _validateWithMessage(value) {
        // Convert html entities in one function. Otherwise, the value length is wrong.
        // For example &nbsp; is one character, but the length is 6.
        value = new DOMParser().parseFromString(value, 'text/html').body.textContent;
        for (const validator of this.config.validators) {
            const message = validator(this.config, value);
            if (message != null) {
                return message;
            }
        }
    }
}

export class Validators {
    /**
     * @param {object} config
     * @param {string} value
     * @return {string|null}
     */
    static validateMinLength(config, value) {
        if (config.decorations.min === undefined || value.length === 0 || value.length >= config.decorations.min.min) {
            return null;
        }
        return `The value must be at least ${config.decorations.min.min} characters long.`;
    }

    /**
     * @param {object} config
     * @param {string} value
     * @return {string|null}
     */
    static validateMaxLength(config, value) {
        if (config.decorations.max === undefined || value.length <= config.decorations.max.max) {
            return null;
        }
        // Cut the value to the max length, and get the rest
        let toMuch = value.substring(config.decorations.max.max);
        let suffix = '';
        if (toMuch.length > 26) {
            toMuch = toMuch.substring(0, 26);
            suffix = '(...)';
        }
        return `The value must be at most ${config.decorations.max.max} characters long.<br>Therefore you cannot use: <span class="text-red-500 underline">${toMuch}</span> ${suffix}`
    }
}
