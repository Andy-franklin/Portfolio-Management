// Saves options to chrome.storage
function save_options() {
    let apiToken = document.getElementById('apiToken').value;
    chrome.storage.sync.set({
        apiToken: apiToken
    }, function () {
        // Update status to let user know options were saved.
        let status = document.getElementById('status');
        status.textContent = 'Api Token Saved.';
        setTimeout(function () {
            status.textContent = '';
        }, 750);
    });
}

function restore_options() {
    chrome.storage.sync.get({
        apiToken: ''
    }, function (items) {
        document.getElementById('apiToken').value = items.apiToken;
    });
}

document.addEventListener('DOMContentLoaded', restore_options);
document.getElementById('save').addEventListener('click', save_options);
