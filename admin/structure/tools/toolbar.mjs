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
    constructor({data, api, config, readOnly, block}){
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

    renderSettings() {
        console.log('renderSettings');
        let defaultSetting = {
            label: 'Revert all to saved value',
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

        return [...this.config.renderSettings, defaultSetting];
    }

    render() {
        return {
            label: 'Revert to saved value',
            // icon: IconUndo,
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
        };
    }

    _show() {
        let component = this.api.ui.nodes.wrapper.closest('[id$="_component"]');

        // Create the toolbar
        let div = document.createElement('div');
        div.style.position = 'absolute';
        div.style.right = '0';
        div.style.top = '0';
        div.style.marginTop = '2rem';
        div.style.className = 'ce-toolbar__actions';
        // with icon 3 dots
        let span = document.createElement('span');
        span.className = 'ce-toolbar__settings-btn';
        div.appendChild(span);
        component.prepend(div);

    }
}