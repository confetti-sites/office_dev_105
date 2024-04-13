export class storage {
    /**
     * @param {string} serviceApiUrl
     * @param {string} prefix
     * @returns {boolean}
     */
    static saveFromLocalStorage(serviceApiUrl, prefix) {
        // Get all items from local storage (exact match and prefix + '/')
        let items = Object.keys(localStorage)
            .filter(key => key === prefix || key.startsWith(prefix + '/'))
            .map(key => {
                return {
                    "id": key,
                    "value": localStorage.getItem(key)
                };
            });

        // Save all items to the server
        this.save(serviceApiUrl, items).then(r => {
            // Remove saved items from local storage
            items.forEach(item => {
                localStorage.removeItem(item.id);
            });

            // Get parent content id to redirect to
            // \w|~ remove word characters (with ulid)
            // /-/ remove target ids
            const parentContentId = prefix.replace(/\/(\w|~|\/-\/)+$/, '');
            if (parentContentId === '' || parentContentId === '/model') {
                window.location.reload();
            } else {
                window.location.href = `/admin${parentContentId}`;
            }
            return true;
        });
    }

    /**
     * @param serviceApiUrl
     * @param data
     * @returns {Promise<any>}
     */
    static save(serviceApiUrl, data) {
        return fetch(`${serviceApiUrl}/confetti-cms/content/contents`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + document.cookie.split('access_token=')[1].split(';')[0],
            },
            body: JSON.stringify({"data": data})
        })
            .then(response => {
                if (response.status >= 300) {
                    console.log("Error: " + response.responseText);
                    return;
                }
                return response.json();
            })
            .catch(error => {
                console.error('Error:', error);
            });

    }

    /**
     * @param {string} prefix
     * @returns {{id: string, value: string}[]}
     */
    static getLocalStorageItems(prefix) {
        return Object.keys(localStorage)
            .filter(key => {
                // We want to include /model/overview/blog~1Z4BJ9J5D9
                // when key is /model/overview/blog~
                if (prefix.endsWith('~') || prefix.endsWith('/-')) {
                    return key.startsWith(prefix);
                }
                return key === prefix || key.startsWith(prefix + '/');
            })
            .map(key => {
                return {
                    "id": key,
                    "value": localStorage.getItem(key)
                };
            });
    }

    /**
     * Get all new created items from local storage
     * Search for items that end with full ulid `/model/banner~1ZK6J9J5D9`
     * but the search query only has `/model/banner~`
     * Note that we don't want to include `/model/banner~1ZK6J9J5D9/title`
     */
    static getNewItems(key) {
        // trim the right side of the localstorage id with 10 characters and compare
        return Object.keys(localStorage)
            .filter(id => id.slice(0, -10)  === key)
            .map(id => {
                return {
                    "id": id,
                    "data": {
                        ".": localStorage.getItem(id),
                    }
                };
            });
    }

    /**
     * @param {string} prefix
     * @param {string} componentLabel
     * @returns {string}
     */
    static getSubmitText(prefix, componentLabel) {
        const total = storage.getLocalStorageItems('/model').length;
        const toSave = storage.getLocalStorageItems(prefix).length;
        if (toSave === 0) {
            let otherChanges = '';
            if (total > 0) {
                otherChanges = total === 1 ? ' (but there is one other change to publish)' : ` (but there are ${total - toSave} other changes to publish)`;
            }
            return `${componentLabel} has no changes ${otherChanges}`;
        }
        if (total === toSave) {
            if (toSave === 1) {
                return `${componentLabel} has one change. Click here to publish.`;
            }
            return `${componentLabel} has ${toSave} changes. Click here to publish.`;
        }
        if (toSave === 1) {
            return `${componentLabel} has one change. Click here to publish. (without ${total - toSave} other changes)`;
        }
        return `${componentLabel} has ${toSave} changes. Click here to publish. (without ${total - toSave} other changes)`;
    }
}