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
        const data = reactive({popoverOpen: false})

        this.ankerElement.style.position = 'relative';
        this.ankerElement.style.display = 'block';

        // If the user presses the escape key, the popover will close.
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                data.popoverOpen = false;
            }
        });

        const toolbar = html`
            <div class="absolute right-0 top-0 flex flex-row items-center p-2">
                <!-- Icon -->
                <span class="cursor-pointer" @click="${() => {
                    data.popoverOpen = !data.popoverOpen
                }}">${IconEtcVertical}</span>
                <div>
                    <!-- Overlay -->
                    <div class="${() => `fixed top-0 left-0 right-0 bottom-0 overflow-hidden z-40 ${data.popoverOpen ? '' : 'hidden'}`}"
                         @click="${() => {data.popoverOpen = false}}">
                    </div>
                    <!-- Popover -->
                    <div class="${() => `fixed sm:absolute top-auto sm:top-0 left-0 z-10 sm:left-auto right-0 bottom-0 sm:bottom-auto m-5 sm:mt-8 p-2 sm:p-1 border rounded-md sm:w-[270px] bg-white shadow-lg z-50 ${data.popoverOpen ? '' : 'hidden'}`}">
                        <!-- Items -->
                        ${settingItems.map(itemData => html`
                            <button type="button" class="flex p-2 cursor-pointer select-none inline-flex items-center line-clamp-2" 
                                 @click="${() => {itemData.onActivate(); data.popoverOpen = false}}">
                                <div style="padding-right:12px">${itemData.icon}</div>
                                <div class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-800">${itemData.label}</div>
                            </button>
                        `)}
                    </div>
                </div>
            </div>
        `;

        return toolbar(this.ankerElement);
    }
}
