import {storage} from '/admin/assets/js/admin_service.mjs';

export default class LimList {
    constructor(id, columns, originalRows) {
        this.id = id;
        this.columns = columns;
        this.rows = originalRows;
    }

    getRows() {
        // Load new rows from local storage
        storage.getNewItems(this.id).forEach((item) => {
            const data = {};
            for (const column of this.columns) {
                if (localStorage.hasOwnProperty(item.id + '/' + column.id)) {
                    data[column.id] = localStorage.getItem(item.id + '/' + column.id);
                } else {
                    data[column.id] = column.default_value ?? '';
                }
            }
            this.rows.push({id: item.id, data: data});
        });

        // Update existing rows from local storage
        const result = [];
        for (const rowRaw of this.rows) {
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
}