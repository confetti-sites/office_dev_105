export class content {
    /**
     * @param {string} serviceApiUrl
     * @param {string} prefix
     * @returns {boolean}
     */
    static saveLocalStorage(serviceApiUrl, prefix) {
        // console.log("Saving to local storage");
        // console.log("Service API URL: " + serviceApiUrl);
        console.log("Prefix: " + prefix);
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
        this.save(serviceApiUrl, items);

        // Get parent content id
        // \w|~ remove word characters (with ulid)
        // /-/ remove target ids
        const parentContentId = prefix.replace(/\/(\w|~|\/-\/)+$/, '');
        // Redirect to parent page
        window.location.href = `/admin/${parentContentId}`;
        return true;
    }

    static save(serviceApiUrl, data) {
        fetch(`${serviceApiUrl}/confetti-cms/content/contents`, {
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
}