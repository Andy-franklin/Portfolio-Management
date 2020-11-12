/*
    Inject our code to capture the stats response data
 */
function injectCode() {
    let resourceUrl = chrome.runtime.getURL('positions.js');
    const monkeyPatchCode = `
        var existing = document.getElementById('stockTrackPositionsJs');
        if (existing === null) {
            var s = document.createElement('script');
            s.src = '${resourceUrl}';
            s.setAttribute('id', 'stockTrackPositionsJs');
            (document.head || document.documentElement).appendChild(s);
        }
    `;
    chrome.tabs.query(
        { active: true, windowId: chrome.windows.WINDOW_ID_CURRENT },
        function(tabs) {
            const { id: tabId } = tabs[0].url;
            chrome.tabs.executeScript(tabId, {code: monkeyPatchCode, runAt: 'document_end'}, function () {
                console.log('Monkey Patch Injected')
            });
        }
    );
}

injectCode();

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
