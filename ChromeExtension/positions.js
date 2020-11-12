console.log('Andy Stock Tracker positions.js script has been injected correctly');

let positions = [];
let positionsTime = null;

function savePositions(details) {
    positionsTime = new Date().getTime();
    positions = details;
    postHome(document.getElementsByClassName('username')[0].innerHTML);
}

function postHome(username) {
    console.log(username);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "https://stocks.test/api/v1/extension/" + username + "/holdings", true);

    //Send the proper header information along with the request
    xhr.setRequestHeader("Content-Type", "application/json");

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
