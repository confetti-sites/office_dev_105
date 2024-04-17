import {storage} from '/admin/assets/js/admin_service.mjs';

export default class LimList {
    constructor(id, columns, originalRows) {
        this.id = id;
        this.columns = columns;
        this.rows = originalRows;
    }

    getRows() {
        // Load new rows from local storage
        let rowsWithNew = this.rows;
        storage.getNewItems(this.id).forEach((item) => {
            const data = {};
            for (const column of this.columns) {
                if (localStorage.hasOwnProperty(item.id + '/' + column.id)) {
                    data[column.id] = localStorage.getItem(item.id + '/' + column.id);
                } else {
                    data[column.id] = column.default_value ?? '';
                }
            }
            rowsWithNew.push({id: item.id, data: data});
        });

        // Update existing rows from local storage
        const result = [];
        for (const rowRaw of rowsWithNew) {
            const data = {};
            for (const column of this.columns) {
                // Use localstorage if available
                const id = rowRaw.id + '/' + column.id;
                if (localStorage.hasOwnProperty(id)) {
                    data[column.id] = localStorage.getItem(id);
                } else {
                    data[column.id] = rowRaw.data[column.id];
                }
            }
            result.push({id: rowRaw.id, data});
        }
        return result;
    }

    /**
     * @see https://onclick.blog/blog/creating-resizable-table-with-drag-drop-reorder-functionality-using-pure-javascript-and-tailwind-css
     * @param {HTMLElement} tbody
     */
    makeDraggable(tbody) {
        let rows = tbody.querySelectorAll('tr');
        // Initialize the drag source element to null
        let dragSrcEl = null;
        // Loop through each row (skipping the first row which contains the table headers)
        for (let i = 0; i < rows.length; i++) {
            let row = rows[i];
            // Make each row draggable
            row.draggable = true;

            // Add an event listener for when the drag starts
            row.addEventListener('dragstart', function (e) {
                // Set the drag source element to the current row
                dragSrcEl = this;
                // Set the drag effect to "move"
                e.dataTransfer.effectAllowed = 'move';
                // Set the drag data to the outer HTML of the current row
                e.dataTransfer.setData('text/html', this.outerHTML);
                // Remove all default hover:bg colors because when the first row is
                // dragged and removed, the second row will be the first row and will have hover:bg-
                for (let i = 0; i < rows.length; i++) {
                    rows[i].classList.add('hover:bg-inherit');
                }
            });

            // Add an event listener for when the drag ends
            row.addEventListener('dragend', function (e) {
                // Restore the background color of the real target row
                dragSrcEl.classList.remove('bg-gray-100');
                // Restore all default hover:bg colors
                for (let i = 0; i < rows.length; i++) {
                    rows[i].classList.remove('hover:bg-inherit');
                }
                // If the drag source element is not the current row
                if (dragSrcEl !== this) {
                    // Get the index of the drag source element
                    let sourceIndex = dragSrcEl.rowIndex;
                    // Get the index of the target row
                    let targetIndex = this.rowIndex;
                    // If the source index is less than the target index
                    if (sourceIndex < targetIndex) {
                        // Insert the drag source element after the target row
                        tbody.insertBefore(dragSrcEl, this.nextSibling);
                    } else {
                        // Insert the drag source element before the target row
                        tbody.insertBefore(dragSrcEl, this);
                    }
                }
            });

            // Add an event listener for when the dragged row is over another row
            row.addEventListener('dragover', function (e) {
                // Prevent the default dragover behavior
                e.preventDefault();
                // Add border classes to the current row to indicate it is a drop target
                // If the drag source element is not the current row
                if (dragSrcEl !== this) {
                    // Add a background color to the real target row
                    dragSrcEl.classList.add('bg-gray-100');
                    // Get the index of the drag source element
                    let sourceIndex = dragSrcEl.rowIndex;
                    // Get the index of the target row
                    let targetIndex = this.rowIndex;
                    // If the source index is less than the target index
                    if (sourceIndex < targetIndex) {
                        // Insert the drag source element after the target row
                        tbody.insertBefore(dragSrcEl, this.nextSibling);
                    } else {
                        // Insert the drag source element before the target row
                        tbody.insertBefore(dragSrcEl, this);
                    }
                }
            });

            // Add an event listener for when the dragged row enters another row
            // row.addEventListener('dragenter', function (e) {
                // Prevent the default dragenter behavior
                // e.preventDefault();
            // });

            // // Add an event listener for when the dragged row leaves another row
            // row.addEventListener('dragleave', function (e) {
            // });
            //
            // Add an event listener for when the dragged row is dropped onto another row
            // row.addEventListener('drop', function (e) {
                // Prevent the default drop behavior
                // e.preventDefault();
            // });
        }
    }
}