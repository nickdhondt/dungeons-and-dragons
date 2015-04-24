window.onload = function() { init(); };
var user_id;
var username;

function init() {
    document.getElementById("btn_login").addEventListener("click", function() { tryLogin(); });
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
    console.log(responseParse);

    if (responseParse !== null) {
        if (responseParse.login === "true") {
            disableLogin();
            user_id = responseParse.data.user_id;
            username = responseParse.data.username;
            document.getElementById("user_info").innerHTML = username;
        }
        else {
            var notification = new Notification(responseParse.errors[0], false);
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