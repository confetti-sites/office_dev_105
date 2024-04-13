/** @see https://github.com/codex-team/icons */
import {IconEtcVertical} from 'https://esm.sh/@codexteam/icons';
import {html, reactive} from 'https://esm.sh/@arrow-js/core';


export class Toolbar {
    /** @type {HTMLElement} */
    constructor(component) {
        this.ankerElement = component;
    }

    /**
     * @param {ToolbarItem[]} settingItems
     * @param settingItems
     */
    init(settingItems) {
        const data = reactive({
            popoverOpen: false,
        })

        this.ankerElement.style.position = 'relative';

        const toolbar = html`
            <div style="position: absolute; right: 0; top: 0; margin-top: 2rem; display: flex; flex-direction: row; justify-content: flex-end; align-items: center; width: 100%; padding: 0 1.2rem;">
                <span class="ce-toolbar__settings-btn" style="cursor: pointer;" @click="${() => {
                    data.popoverOpen = !data.popoverOpen
                }}">${IconEtcVertical}</span>
                <div class="ce-settings" style="">
                    <div class="${() => `ce-popover__overlay ${!data.popoverOpen ? 'ce-popover__overlay--hidden' : ''}`}"
                         @click="${() => {
                             data.popoverOpen = false
                         }}"
                         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow: hidden;">
                    </div>
                    <div class="${() => `ce-popover ${data.popoverOpen ? 'ce-popover--opened' : ''}`}"
                         style="right: 15px; left: initial;">
                        ${settingItems.map(itemData => html`
                            <div class="ce-popover-item" @click="${() => {itemData.onActivate(); data.popoverOpen = false}}">
                                <div class="ce-popover-item__icon">${itemData.icon}</div>
                                <div class="ce-popover-item__title">${itemData.label}</div>
                            </div>
                        `)}
                    </div>
                </div>
            </div>
        `;

        return toolbar(this.ankerElement);
    }
}