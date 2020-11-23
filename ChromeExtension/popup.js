const BASEURL = 'https://212.test/api/';
let apiToken = '';
let user = {};

let triggerCapture = document.getElementById('triggerCapture');
triggerCapture.onclick = function(element) {
    chrome.tabs.query(
        { active: true, windowId: chrome.windows.WINDOW_ID_CURRENT },
        function(tabs) {
            const { id: tabId } = tabs[0].url;
            //Trigger the click on the menu
            //To have the page make the request to https://live.trading212.com/rest/v1/customer/accounts/stats
            let code = `document.getElementsByClassName('dropdown-account-menu')[0].click()`;
            chrome.tabs.executeScript(tabId, { code }, function () {});
            //Close the menu quick
            chrome.tabs.executeScript(tabId, { code }, function () {});
        }
    );
};

document.getElementById('go-to-options').onclick = function() {
    if (chrome.runtime.openOptionsPage) {
        chrome.runtime.openOptionsPage();
    } else {
        window.open(chrome.runtime.getURL('options.html'));
    }
};

const getStorageData = key =>
    new Promise((resolve, reject) =>
        chrome.storage.sync.get(key, result =>
            chrome.runtime.lastError
                ? reject(Error(chrome.runtime.lastError.message))
                : resolve(result)
        )
    )

function overwriteApiToken() {
    chrome.storage.sync.set({
        apiToken: undefined
    }, function () {});
}

function setupDisplay(hasToken) {
    console.log('settingUpDisplay' + hasToken)
    if (hasToken) {
        //Show options
        document.getElementById('main-options').classList.remove('hidden');
        document.getElementById('initial-setup-options').classList.add('hidden');
        document.getElementById('logout').classList.remove('hidden');

        document.getElementById('status').innerHTML = 'logged in as ' + user.name;
    } else {
        //Show initial settings button
        document.getElementById('initial-setup-options').classList.remove('hidden');
        document.getElementById('main-options').classList.add('hidden');
        document.getElementById('logout').classList.add('hidden');

        document.getElementById('status').innerHTML = 'not logged in.';
    }
}

function addAuthorisationHeader(xhr, apiToken)
{
    xhr.setRequestHeader('Accept', 'application/json')
    xhr.setRequestHeader('Authorization', 'Bearer ' + apiToken)
}

function requestUser(apiToken) {
    console.log('requestingUsername');
    const xhr = new XMLHttpRequest();
    xhr.open("GET", BASEURL + 'user', true);
    addAuthorisationHeader(xhr, apiToken)
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            user = JSON.parse(this.response);
            handleValidUser();
        } else if (this.status === 401) {
            handleNonValidUser();
        }
    }
    xhr.send();
}

function handleValidUser() {
    setupDisplay(true);
    //Add the positions tracking code
    // injectCode(apiToken);
}

function handleNonValidUser() {
    overwriteApiToken();
    setupDisplay(false);
}

document.addEventListener('DOMContentLoaded', async function () {
    apiToken = (await getStorageData('apiToken')).apiToken;
    console.log(apiToken + ' an API token has been found.')

    if (apiToken !== '' && apiToken !== undefined) {
        requestUser(apiToken);
    } else {
        setupDisplay(false)
    }
});

let logout = document.getElementById('logout');
logout.onclick = function(element) {
    handleNonValidUser();
}
