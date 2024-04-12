export class content {
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
            .filter(key => key === prefix || key.startsWith(prefix + '/'))
            .map(key => {
                return {
                    "id": key,
                    "value": localStorage.getItem(key)
                };
            });
    }

    /**
     * @param {string} prefix
     */
    static getLabel(prefix) {
        const total = content.getLocalStorageItems(prefix).length;
        if (total === 0) {
            return 'Nothing to save';
        }
        if (prefix === '/model') {
            return 'Publish all ' + total + ' changes';
        }
        return 'Publish ' + total + ' changes';
    }
}