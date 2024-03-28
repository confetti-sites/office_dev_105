document.addEventListener('alpine:init', () => {
    Alpine.store('config', {
        apiUrl: undefined,
        init() {
            let xhr = new XMLHttpRequest();
            xhr.open('GET', '/config.blade.php', true);
            xhr.responseType = 'json';
            xhr.onload = function () {
                let status = xhr.status;
                if (status === 200) {
                    Alpine.store('config').setApiUrl(xhr.response.api_url);
                    document.dispatchEvent(new CustomEvent('config:init'));
                } else {
                    console.error(status, xhr.response);
                    Alpine.store('config').setApiUrl('error_from_config');
                }
            };
            xhr.send()
        },
        getApiUrl() {
            return this.apiUrl
        },
        setApiUrl(url) {
            console.log('setApiUrl ' + url);
            this.apiUrl = url
        }
    })


    Alpine.store('form', {
        previewImage: null,
        changes: [],
        allChanges() {
            return this.changes
        },
        upsert(id, value) {
            let exists = false;
            // Update if exists
            this.changes = this.changes.map(function (change) {
                if (change.id === id) {
                    exists = true;
                    return {
                        id: id,
                        value: value
                    }
                } else {
                    return change;
                }
            });
            // Add if not exists
            if (!exists) {
                this.changes.push({
                    id: id,
                    value: value
                });
            }
        },
        uploadImage(event) {
            this.previewImage = URL.createObjectURL(event.target.files[0]);
            console.log('event', event.target.files);
        },
    })
    Alpine.bind('field', () => ({
        '@change'(event) {
            console.log('change');
            Alpine.store('form').upsert(event.target.attributes.name.value, event.target.value)
        },
        '@saveThisField'(event) {
            console.log('saveThisField');
            Alpine.store('form').upsert(event.target.attributes.name.value, event.target.value)
            saveContents([{
                id: event.target.attributes.name.value,
                value: event.target.value
            }], () => {
            });
        },
    }))
    Alpine.bind('submit', () => ({
        '@click.throttle.1000ms'(event) {
            console.log('submit ->');
            const parentContentId = event.target.attributes['parent-content-id'].value
            const hasParent = event.target.attributes['has-parent'].value
            let data = Alpine.store('form').allChanges();
            saveContents(data, () => {
                // if parentContentId string has ~ go back (to the list)
                if (hasParent) {
                    location.href = '/admin' + parentContentId
                } else {
                    location.reload()
                }
            });
        },
    }))

    function saveContents(data, ready) {
        // Wait for config
        let configWaiter = setInterval(function () {
            let apiUrl = Alpine.store('config').getApiUrl();
            if (apiUrl === undefined) {
                return;
            }
            clearInterval(configWaiter);
            // Do the request
            let xhr = new XMLHttpRequest();
            xhr.withCredentials = true;
            xhr.addEventListener("readystatechange", function () {
                if (this.status >= 300) {
                    console.log("Error: " + this.responseText);
                    return;
                }
                ready()
            });
            xhr.open("PATCH", apiUrl + "/confetti-cms/content/contents");
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.setRequestHeader("Authorization", "Bearer " + document.cookie.split('access_token=')[1].split(';')[0]);
            let body = JSON.stringify({"data": data});
            xhr.send(body);
        }, 200);
    }
})
