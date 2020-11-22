console.log('Andy Stock Tracker positions.js script has been injected correctly');

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
    const xhr = new XMLHttpRequest();
    xhr.open("POST", BASEURL + "extension/stats", true);

    //Send the proper header information along with the request
    addAuthorisationHeader(xhr);

    xhr.onreadystatechange = function() { // Call a function when the state changes.
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            // Request finished. Do processing here.
            console.log('Stats sent home')
        }
    }

    xhr.send(positions);
}

(function() {
    let origOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        this.addEventListener('load', function() {
            if (this.responseURL === 'https://live.trading212.com/rest/v1/customer/accounts/stats') {
                console.log('Capturing stats request');
                savePositions(this.response)
            }
        });
        origOpen.apply(this, arguments);
    };
})();
