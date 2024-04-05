// noinspection JSUnusedGlobalSymbols

/** @see https://github.com/codex-team/icons */
import {IconUndo} from 'https://esm.sh/@codexteam/icons';

export default class Toolbar {
    /**
     * @param {Api} api
     * @param {object} config
     */
    api;
    config;

    constructor({data, api, config, readOnly, block}) {
        console.log('constructor');
        console.log('data', data);
        console.log('api', api);
        console.log('config', config);
        console.log('readOnly', readOnly);
        console.log('block', block);

        this.api = api;
        this.config = config;

        this._show();
    }

    // renderSettings() {
    //     console.log('renderSettings');
    //     let defaultSetting = {
    //         label: 'Revert all to saved value',
    //         icon: IconUndo,
    //         closeOnActivate: true,
    //         onActivate: async () => {
    //             // Save the value in local storage
    //             let block = this.api.blocks.getBlockByIndex(0);
    //             block.call('setStorageValue', this.config.originalValue);
    //
    //             // Render the original value
    //             this.api.blocks.render({
    //                 blocks: [{
    //                     type: "paragraph", data: {
    //                         text: this.config.originalValue
    //                     }
    //                 }]
    //             });
    //         }
    //     };
    //
    //     return [...this.config.renderSettings, defaultSetting];
    // }

    renderSettings() {
        return [
            {
                label: 'Revert to saved value',
                icon: IconUndo,
                closeOnActivate: true,
                onActivate: async () => {
                    console.log('Revert button clicked');
                    // Save the value in local storage
                    // let block = this.api.blocks.getBlockByIndex(0);
                    // block.call('setStorageValue', this.config.originalValue);
                    //
                    // // Render the original value
                    // this.api.blocks.render({
                    //     blocks: [{
                    //         type: "paragraph", data: {
                    //             text: this.config.originalValue
                    //         }
                    //     }]
                    // });
                }
            },
        ];
    }

    _show() {
        let component = this.api.ui.nodes.wrapper.closest('[id$="_component"]');

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

        // with icon 3 dots
        let settingBtn = document.createElement('span');
        settingBtn.className = 'ce-toolbar__settings-btn';
        settingBtn.style.cursor = 'pointer';
        settingBtn.style.zIndex = '103';

        // Create the settings popover
        let settings = document.createElement('div');
        settings.className = 'ce-settings';
        settings.style.zIndex = '100';
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

        // open the settings popover
        settingBtn.addEventListener('click', () => {
            popover.classList.toggle('ce-popover--opened');
        });
        toolbar.appendChild(settingBtn);
        toolbar.appendChild(settings);


        component.prepend(toolbar);
    }
}