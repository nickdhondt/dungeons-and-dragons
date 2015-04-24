// Pfff comments
window.onload = function() { init(); };
var userId, permissionId;

function init() {
    document.getElementById("btn_login").addEventListener("click", function() { tryLogin(); });
    document.getElementById("logout").addEventListener("click", function() { logout(); });
    document.getElementById("one").addEventListener("click", function() { showTab("one"); });
    document.getElementById("two").addEventListener("click", function() { showTab("two"); });
    document.getElementById("three").addEventListener("click", function() { showTab("three"); });
    requestUserData();
}

function showTab(tab) {
    var tabOne = document.getElementById("tab_one");
    var tabTwo = document.getElementById("tab_two");
    var tabThree = document.getElementById("tab_three");

    var one = document.getElementById("one");
    var two = document.getElementById("two");
    var three = document.getElementById("three");

    if (tab === "one") {
        tabOne.style.display = "block";
        tabTwo.style.display = "none";
        tabThree.style.display = "none";
        one.setAttribute("class", "active");
        two.setAttribute("class", "");
        three.setAttribute("class", "");
    } else if (tab === "two") {
        tabOne.style.display = "none";
        tabTwo.style.display = "block";
        tabThree.style.display = "none";
        one.setAttribute("class", "");
        two.setAttribute("class", "active");
        three.setAttribute("class", "");
    } else if (tab === "three") {
        tabOne.style.display = "none";
        tabTwo.style.display = "none";
        tabThree.style.display = "block";
        one.setAttribute("class", "");
        two.setAttribute("class", "");
        three.setAttribute("class", "active");
    }
}

function logout() {
    sendXHR("", "http/http_logout.php", "get", "processLogout");
}

function processLogout(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.logged_out === "true") {
            window.location.reload();
        }
        else {
            var notif = new Notification("Er is een fout opgetreden.", false);
        }
    }
}

function tryLogin() {
    var userCredentials = {};

    userCredentials.username = document.getElementById("txt_username").value;
    userCredentials.password = document.getElementById("txt_password").value;

    var loginData = JSON.stringify(userCredentials);

    sendXHR(loginData, "http/http_login.php", "post", "processLoginResponse");
}

function sendXHR(data, url, type, executeFunction) {
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = xhr.responseText;

            switch (executeFunction) {
                case "processLoginResponse":
                    processLoginResponse(response);
                    break;
                case "processUserData":
                    processUserData(response);
                    break;
                case "processLogout":
                    processLogout(response);
                    break;
            }
        }
    };

    xhr.open(type, url);

    if (type === "post") {
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("data=" + data);
    } else {
        xhr.send();
    }
}

function processLoginResponse(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.login === "true") {
            disableLogin();
            userId = responseParse.data.user_id;

            requestUserData();
        }
        else {
            displayErrors(responseParse.errors);
        }
    }
}

function displayErrors(errorsObject) {
    var errorsAmount = errorsObject.length;
    console.log(errorsAmount);
    for (var i = 0; i < errorsAmount; i++) {
        var notification = new Notification(errorsObject[i], false);
    }
}

function requestUserData(requestUserId) {
    var requestDataFromUser = {};

    if (typeof (requestUserId) === "undefined") {
        if (typeof (userId) === "undefined") {
            requestDataFromUser.user_id = "false";
        } else {
            requestDataFromUser.user_id = userId;
        }
        //console.log("ok");
    } else {
        requestDataFromUser.user_id = requestUserId;
        //console.log("ok2");
    }

    var jsonData = JSON.stringify(requestDataFromUser);

    sendXHR(jsonData, "http/http_user_data.php", "post", "processUserData");
}

function processUserData(jsonData) {
    //console.log(jsonData);
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_accepted === "true") {
            var user_info = document.getElementById("user_info");
            disableLogin();

            user_info.innerHTML = "Welkom " + responseParse.data.username + " <span>(" + responseParse.data.permission_name + ")</span>";
            userId = responseParse.data.user_id;
            permissionId = responseParse.data.permission_type;
        } else {
            displayErrors(responseParse.errors);
        }
    }
}

function parseJSON(jsonData) {
    var responseParse = null;
    try {
        responseParse = JSON.parse(jsonData);
    } catch (ex) {
        var notificationJSONError = new Notification("Er is een serverfout opgetreden, kan data niet verwerken: " + ex, false);
        var notificationServerError = new Notification("Server meldt: " + jsonData, false);
    }

    return responseParse;
}

function disableLogin() {
    var loginHolder = document.getElementById("login_holder");
    loginHolder.style.opacity = "0";
    loginHolder.style.pointerEvents = "none";
}

var Notification = function(message, keepAlive) {
    this.message = message;
    this.id = ("" + (Math.floor((Math.random() * 10000) + 1000))).substring(0,4);
    this.keepAlive = keepAlive;

    var notifContainer = document.getElementById("notifications");

    var notifNode = document.createElement("div");
    var notifText = document.createTextNode(message);
    notifNode.appendChild(notifText);
    notifNode.setAttribute("id", this.id);
    var closeNotifNode = document.createElement("span");
    var closeNotifText = document.createTextNode("✖");
    closeNotifNode.appendChild(closeNotifText);
    closeNotifNode.setAttribute("class", "close_notif");
    notifNode.appendChild(closeNotifNode);
    notifNode.style.opacity = "0";
    setTimeout(function() { notifNode.style.opacity = "1"; }, 5);

    notifContainer.appendChild(notifNode);

    if(keepAlive !== true) {
        this.disable();
    }
};

Notification.prototype.disable = function() {
    var currentId = this.id;
    var currentNotification = document.getElementById(this.id);

    setTimeout(function() {
        currentNotification.style.opacity = "0";
        setTimeout(function() { currentNotification.remove() }, 490);
    }, 5000);
};