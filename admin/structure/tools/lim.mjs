/** @see https://github.com/codex-team/icons */
import {IconEtcVertical} from 'https://esm.sh/@codexteam/icons';

export class Toolbar {
    /** @type {HTMLElement} */
    constructor(component) {
        this.component = component;
    }

    /**
     * @param {ToolbarItem[]} settingItems
     * @param settingItems
     */
    init(settingItems) {
        // Component needs to be relative
        this.component.style.position = 'relative';

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

        for (let itemData of settingItems) {
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
        this.component.prepend(toolbar);
    }
}