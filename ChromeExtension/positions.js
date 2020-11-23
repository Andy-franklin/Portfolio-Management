const DEBUG_POSITIONS = true

if (DEBUG_POSITIONS) {
    console.log('Positions; Andy Stock Tracker positions.js script has been injected correctly');
}

let positions = [];
let positionsTime = null;

const BASEURL = document.getElementsByName("andyStockBaseUrl")[0].value;
const APITOKEN = document.getElementsByName("stockTrackPositionsApiToken")[0].value;

function addAuthorisationHeader(xhr)
{
    xhr.setRequestHeader('Accept', 'application/json')
    xhr.setRequestHeader('Authorization', 'Bearer ' + APITOKEN)
}

function savePositions(details) {
    positionsTime = new Date().getTime();
    positions = details;
    postHome();
}

function postHome() {
    positionsTime = new Date();
    const xhr = new XMLHttpRequest();
    xhr.open("POST", BASEURL + "extension/stats", true);

    //Send the proper header information along with the request
    addAuthorisationHeader(xhr);

    xhr.onreadystatechange = function() { // Call a function when the state changes.
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            // Request finished. Do processing here.
            if (DEBUG_POSITIONS) {
                console.log('Positions; Stats sent home')
            }
        }
    }

    xhr.send(JSON.stringify(positions));
}

/**
 * @deprecated This was getting the stats from the /stats endpoint but has been moved to a websocket
 * Leaving this in as it could be they change how the positions stats are sent
 */
(function() {
    let origOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        this.addEventListener('load', function() {
            if (this.responseURL === 'https://live.trading212.com/rest/v1/customer/accounts/stats') {
                if (DEBUG_POSITIONS) {
                    console.log('Positions; Capturing stats request');
                }
                savePositions(this.response)
            }
        });
        origOpen.apply(this, arguments);
    };
})();

function testJSON(text) {
    if (typeof text !== "string") {
        return false;
    }
    try {
        JSON.parse(text);
        return true;
    } catch (error) {
        return false;
    }
}

(function() {
    wsHook.after = function(messageEvent, url, wsObject) {
        let jsonString = '';
        for (let i = 0; i < messageEvent.data.length; i++) {
            if (messageEvent.data.charAt(i) === '[') {
                jsonString = messageEvent.data.substring(i);
                break;
            }
        }
        if (testJSON(jsonString)) {
            let json = JSON.parse(jsonString);
            if (json[0] === 'acc') {
                positions = json;
                if (positionsTime === null || ((new Date) - positionsTime) > 10 * 60 * 1000) {
                    if (DEBUG_POSITIONS) {
                        console.log('Positions; Sending data ' + (new Date) + ':' + positionsTime)
                    }
                    postHome()
                } else {
                    if (DEBUG_POSITIONS) {
                        console.log('Positions; Not sending data. Too soon (' + ((new Date) - positionsTime) + ')');
                    }
                }
            }
        }
        return messageEvent;
    }
})();