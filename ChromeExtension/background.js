'use strict';

const getStorageData = key =>
    new Promise((resolve, reject) =>
        chrome.storage.sync.get(key, result =>
            chrome.runtime.lastError
                ? reject(Error(chrome.runtime.lastError.message))
                : resolve(result)
        )
    )

const BASEURL = 'https://212.test/api/';

function injectCode(apiToken) {
    let resourceUrl = chrome.runtime.getURL('positions.js');
    let wsHookResourceUrl = chrome.runtime.getURL('vendor/wsHook.js');
    const monkeyPatchCode = `
        var existing = document.getElementById('stockTrackPositionsJs');

        var andyStockBaseUrl = document.createElement('input');
        andyStockBaseUrl.setAttribute('type', 'hidden');
        andyStockBaseUrl.setAttribute('value', '${BASEURL}');
        andyStockBaseUrl.setAttribute('name', 'andyStockBaseUrl');

        var stockTrackPositionsApiToken = document.createElement('input');
        stockTrackPositionsApiToken.setAttribute('type', 'hidden');
        stockTrackPositionsApiToken.setAttribute('value', '${apiToken}');
        stockTrackPositionsApiToken.setAttribute('name', 'stockTrackPositionsApiToken');

        (document.head || document.documentElement).appendChild(andyStockBaseUrl);
        (document.head || document.documentElement).appendChild(stockTrackPositionsApiToken);

        if (existing === null) {
            var wsHook = document.createElement('script');
            wsHook.src = '${wsHookResourceUrl}';
            wsHook.setAttribute('id', 'wsHook');
            (document.head || document.documentElement).appendChild(wsHook);
        
            var s = document.createElement('script');
            s.src = '${resourceUrl}';
            s.setAttribute('id', 'stockTrackPositionsJs');
            (document.head || document.documentElement).appendChild(s);
        }
    `;

    chrome.tabs.query(
        { active: true },
        function(tabs) {
            if (tabs[0].url === "https://live.trading212.com/beta") {
                console.log('Injecting; Tab correct (' + tabs[0].url + ')')
                const { id: tabId } = tabs[0].url;
                chrome.tabs.executeScript(tabId, {code: monkeyPatchCode, runAt: 'document_start'}, function () {
                    console.log('Injecting; Monkey Patch Injected')
                });
            }
        }
    );
}

async function onCommitted() {
    console.log('Injecting; fetching apiToken');
    let apiToken = (await getStorageData('apiToken')).apiToken;
    console.log('Injecting; ' + apiToken);
    injectCode(apiToken);
}

chrome.webNavigation.onCommitted.addListener(onCommitted, {
    url: [
        {urlPrefix: 'https://live.trading212.com/beta'},
    ]
});

