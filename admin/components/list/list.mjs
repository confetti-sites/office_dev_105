import {Storage} from '/admin/assets/js/admin_service.mjs';

export default class LimList {
    constructor(id, columns, originalRows) {
        this.id = id;
        this.columns = columns;
        this.originalRows = originalRows;
        this.rows = originalRows;
    }

    /**
     * @param {object} row
     * @returns {Array}
     */
    getColumns(row) {
        // Do not update the original row by reference
        let withoutReference = this.columns.map(column => row.data[column.id]);
        delete withoutReference['.'];
        return withoutReference;
    }

    getRows(ascending) {
        // Merge new rows from local storage
        // With row.id as key
        let rowsWithNew = this.rows;
        Storage.getMapItems(this.id).forEach((item) => {
            // check if not already in rows
            if (rowsWithNew.find(row => row.id === item.id)) {
                return;
            }
            const data = {};
            data['.'] = item.data['.'];
            for (const column of this.columns) {
                data[column.id] = {id: column.id};
                // In the list we can have normal values and file pointer values (suffixed -)
                let suffix = '';
                if (!localStorage.hasOwnProperty('/component' + item.id + '/' + column.id)) {
                    suffix = '-';
                }
                data[column.id]['component'] = JSON.parse(localStorage.getItem('/component' + item.id + '/' + column.id + suffix));
                if (localStorage.hasOwnProperty(item.id + '/' + column.id + suffix)) {
                    data[column.id]['value'] = JSON.parse(localStorage.getItem(item.id + '/' + column.id + suffix));
                }
            }
            rowsWithNew.push({id: item.id, data: data});
        });

        // Update existing data from local storage
        let result = [];
        for (const rowRaw of rowsWithNew) {
            const data = {};
            for (const column of this.columns) {
                // Use localstorage if available
                const id = rowRaw.id + '/' + column.id;
                data[column.id] = rowRaw.data[column.id];
                // In the list we can have normal values and file pointer values (suffixed -)
                let suffix = '';
                if (!localStorage.hasOwnProperty(id)) {
                    suffix = '-';
                }
                if (localStorage.hasOwnProperty(id + suffix)) {
                    data[column.id]['value'] = JSON.parse(localStorage.getItem(id + suffix));
                    data[column.id]['component'] = JSON.parse(localStorage.getItem('/component' + id + suffix));
                }
            }
            // The `.` value is from the item itself, mainly used for sorting
            if (localStorage.hasOwnProperty(rowRaw.id)) {
                data['.'] = JSON.parse(localStorage.getItem(rowRaw.id));
            } else {
                data['.'] = rowRaw['.'];
            }
            result.push({id: rowRaw.id, data: data});
        }

        if (ascending) {
            result.sort((a, b) => a.data['.'] - b.data['.']);
        } else {
            result.sort((a, b) => b.data['.'] - a.data['.']);
        }

        return result;
    }

    /**
     * @see https://onclick.blog/blog/creating-resizable-table-with-drag-drop-reorder-functionality-using-pure-javascript-and-tailwind-css
     * @param {HTMLElement} tbody
     */
    makeDraggable(tbody, ascending) {
        const originalRows = this.originalRows;
        let rows = tbody.querySelectorAll('tr');
        // Initialize the drag source element to null
        let dragSrcEl = null;
        // Get all current indexes
        const getIndexes = function (tbody) {
            // Get all current indexes
            let indexes = [];
            tbody.querySelectorAll('tr').forEach((row) => {
                indexes.push(row.getAttribute('index'));
            });
            // order indexes
            indexes = indexes.map((index) => parseInt(index));
            if (ascending) {
                indexes.sort((a, b) => a - b);
            } else {
                indexes.sort((a, b) => b - a);
            }
            return indexes;
        };
        // Loop through each row (skipping the first row which contains the table headers)
        for (let i = 0; i < rows.length; i++) {
            let row = rows[i];
            // Make each row draggable, but only if the icon is on mouse down
            if (row.getElementsByClassName('_drag_grip').length > 0) {
                row.getElementsByClassName('_drag_grip')[0].addEventListener('mousedown', e => {
                    row.draggable = true
                });
            }
            // Unsubscribe the drag event listener when the mouse is up
            row.addEventListener('mouseup', e => {
                row.draggable = false
            });

            // Add an event listener for when the drag starts
            row.addEventListener('dragstart', function (e) {
                console.log('dragstart');
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
                console.log('dragend');
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

                // Loop over all rows and update the order in local storage
                const updatedRows = tbody.querySelectorAll('tr');
                const indexes = getIndexes(tbody);
                for (let i = 0; i < updatedRows.length; i++) {
                    const id = updatedRows[i].getAttribute('content_id');
                    localStorage.setItem(id, JSON.stringify(indexes[i]));
                }

                // Loop over old rows and remove items in local storage that are changed
                // originalRows is not iterable
                for (const [key, row] of Object.entries(originalRows)) {
                    const id = row.id;
                    const oldIdOnThisIndex = row['.'];
                    const inStorage = JSON.parse(localStorage.getItem(id));
                    // Only store the new index if it is different from the old index
                    if (oldIdOnThisIndex === inStorage) {
                        localStorage.removeItem(id);
                    }
                }

                row.draggable = false;
                window.dispatchEvent(new Event('local_content_changed'));
            });

            // Add an event listener for when the dragged row is over another row
            row.addEventListener('dragover', function (e) {
                console.log('dragover');
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
        }
    }
}