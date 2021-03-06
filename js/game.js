// Pfff comments
window.onload = function() { init(); };
var userId, permissionId;
var ownUserId;
var admin;
var lastPing = 0, connectionErrors = 0;
var connectionLost = false;
var adminPanelOpen = false;
var uExp, uExpM, newExp, addExp;

function init() {
    document.getElementById("btn_login").addEventListener("click", function() { tryLogin(); });
    document.getElementById("logout").addEventListener("click", function() { logout(); });
    document.getElementById("one").addEventListener("click", function() { showTab("one"); });
    document.getElementById("two").addEventListener("click", function() { showTab("two"); });
    document.getElementById("three").addEventListener("click", function() { showTab("three"); });
    document.getElementById("admin_one").addEventListener("click", function(e) { showAdminTab("one"); e.stopPropagation(); });
    document.getElementById("admin_two").addEventListener("click", function(e) { showAdminTab("two"); e.stopPropagation(); });
    document.getElementById("admin_three").addEventListener("click", function(e) { showAdminTab("three"); e.stopPropagation(); });
    document.getElementById("admin_four").addEventListener("click", function(e) { showAdminTab("four"); e.stopPropagation(); });
    document.getElementById("btn_register").addEventListener("click", function(e) { requestUserRegister(); e.stopPropagation(); });
    document.getElementById("txt_new_username").addEventListener("click", function(e) { e.stopPropagation(); });
    document.getElementById("txt_new_password").addEventListener("click", function(e) { e.stopPropagation(); });
    document.getElementById("show_admin").addEventListener("click", function() { toggleAdminPanel(); });
    window.matchMedia("(orientation: portrait)").addListener(handleOrientationChange);
    document.getElementById("btn_race_class").addEventListener("click", function() { requestChoiceRaceClass(); });
    document.getElementById("next_turn").addEventListener("click", function(e) { requestNextTurn(); e.stopPropagation(); });
    document.getElementById("random_items").addEventListener("click", function(e) { requestRandomItems(); e.stopPropagation(); });

    requestUserData();
}

function requestRandomItems() {
    var randomItemData = {
        "user_id": userId
    };

    sendXHR(JSON.stringify(randomItemData), "http/http_add_random_items.php", "post", "processRandomItemsAdd");
}

function processRandomItemsAdd(jsonData) {
    console.log(parseJSON(jsonData));
}

function catchUseItemEvent() {
    var useItemButtons = document.getElementsByClassName("use_item_button");

    for (var i = 0; i < useItemButtons.length; i++) {
        useItemButtons[i].addEventListener("click", function(e) { requestUseItem(e); })
    }
}

function requestUseItem(e) {
    var useItem = e.target.id.substr(3, 3);

    var requestData = {
        "use_item": useItem,
        "user_id": userId
    };

    sendXHR(JSON.stringify(requestData), "http/http_use_item.php", "post", "processUseItem");
}

function requestNextTurn() {
    var nextTurn = {
        next_turn: "true"
    };

    sendXHR(JSON.stringify(nextTurn), "http/http_next_turn.php", "post", "processNextTurn");
}

function handleOrientationChange() {
    if (adminPanelOpen === false) {
        if (window.matchMedia("screen and (max-device-width: 640px)").matches) {
            document.getElementById("show_admin").style.right = "-90%";
        } else {
            document.getElementById("show_admin").style.right = "-40em";
        }
    }
}

function toggleAdminPanel() {
    if (adminPanelOpen === false) {
        document.getElementById("show_admin").style.right = "0";
        adminPanelOpen = true;
    } else {
        if (window.matchMedia("screen and (max-device-width: 640px)").matches) {
            document.getElementById("show_admin").style.right = "-90%";
            adminPanelOpen = false;
        } else {
            document.getElementById("show_admin").style.right = "-40em";
            adminPanelOpen = false;
        }
    }
}

function requestUserRegister() {
    var userCredentials = {};

    userCredentials.username = document.getElementById("txt_new_username").value;
    userCredentials.password = document.getElementById("txt_new_password").value;

    var registerData = JSON.stringify(userCredentials);

    sendXHR(registerData, "http/http_register_user.php", "post", "processRegisterResponse");
}

function processRegisterResponse(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_legal === "true") {
            var notif = new Notification("De gebruiker werd succesvol toegevoegd.", false);
            requestUserData();
        }
        else {
            displayErrors(responseParse.errors);
        }
    }
}

function microtime(getAsFloat) {
    var now = new Date()
            .getTime() / 1000;
    var s = parseInt(now, 10);

    return (getAsFloat) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
}

function openStream() {
    var eventSource = new EventSource("stream/stream_push_events.php?user_id=" + userId);

    setInterval(function() {
        var changeUserview = document.getElementsByClassName("change_userview");

        for (var i = 0; i < changeUserview.length; i++) {
            changeUserview[i].addEventListener("click", function(e) { eventSource.close(); });
            changeUserview[i].addEventListener("click", function(e) { eventSource.close(); });
        }
    }, 500);


    eventSource.addEventListener("ping", function(e) {
        var streamErrorNotifs = document.getElementsByClassName("streamErrorNotif");
        var streamErrorLength = streamErrorNotifs.length;

        for(var i = 0; i < streamErrorLength; i++) {
            disableClick(streamErrorNotifs[i].id);
        }

        connectionLost = false;
        connectionErrors = 0;
        lastPing = microtime(true);
        document.getElementById("ping_info").innerHTML = JSON.parse(e.data).time;
    }, false);

    eventSource.addEventListener("game_event", function(e) {
        var parsedGameEvent = parseJSON(e.data);

        console.log(parsedGameEvent);

        for (var i = 0; i < parsedGameEvent.basic.length; i++) {
            if (parsedGameEvent.basic[i].is_you === true) {
                if (typeof parsedGameEvent.basic[i].data.basic_data !== "undefined") {
                    var basicDataList = document.getElementById("basic_data_list");
                    basicDataList.innerHTML = "";

                    for (var j = 0; j < parsedGameEvent.basic[i].data.basic_data.length; j++) {
                        if (parsedGameEvent.basic[i].data.basic_data[j].id !== "10") {
                            var basicValueNode = document.createElement("li");
                            var basicValueTextNode;

                            if (parsedGameEvent.basic[i].data.basic_data[j].id === "8") {
                                basicValueTextNode = document.createTextNode("Je bent speler: " + parsedGameEvent.basic[i].data.basic_data[j].value);
                            } else {
                                if (j < 5) {
                                    basicValueTextNode = document.createTextNode(parsedGameEvent.basic[i].data.basic_data[j].value + " " + parsedGameEvent.basic[i].data.basic_data[j].name  + " (max: " + parsedGameEvent.basic[i].data.basic_data[j].max + ")");
                                } else {
                                    basicValueTextNode = document.createTextNode(parsedGameEvent.basic[i].data.basic_data[j].value + " " + parsedGameEvent.basic[i].data.basic_data[j].name);
                                }
                            }
                            basicValueNode.appendChild(basicValueTextNode);
                            basicDataList.appendChild(basicValueNode);
                        }
                    }
                }

                if (typeof parsedGameEvent.basic[i].data.condition_data !== "undefined") {
                    var condList = document.getElementById("conditions_list");
                    condList.innerHTML = "";

                    var conditionsFormatted = prepareConditions(parsedGameEvent.basic[i].data.condition_data);

                    makeListConditions(conditionsFormatted, "conditions_list");
                }

                if (typeof parsedGameEvent.basic[i].data.inventory_data !== "undefined") {
                    var inventoryList = document.getElementById("inventory_list");
                    inventoryList.innerHTML = "";

                    for (var l = 0; l < parsedGameEvent.basic[i].data.inventory_data.length; l++) {
                        var inventoryItemNode, inventoryItemTextNode, useItemButtonNode, useItemButtonTextNode, infoNode;
                        if (typeof parsedGameEvent.basic[i].data.inventory_data[l].conditions !== "string") {
                            inventoryItemNode = document.createElement("li");
                            inventoryItemTextNode = document.createTextNode(parsedGameEvent.basic[i].data.inventory_data[l].name + " (aantal: " + parsedGameEvent.basic[i].data.inventory_data[l].count + ")");
                            useItemButtonNode = document.createElement("div");
                            useItemButtonNode.setAttribute("class", "use_item_button");
                            useItemButtonNode.setAttribute("id", "inv" + parsedGameEvent.basic[i].data.inventory_data[l].item_id);
                            useItemButtonTextNode = document.createTextNode("Gebruiken");
                            useItemButtonNode.appendChild(useItemButtonTextNode);
                            infoNode = document.createElement("ul");
                            infoNode.setAttribute("class", "hover_show");
                            infoNode.setAttribute("id", "inventory_hover_show" + parsedGameEvent.basic[i].data.inventory_data[l].item_id);

                            var inventoryConditionsFormatted = prepareConditions(parsedGameEvent.basic[i].data.inventory_data[l].conditions);

                            inventoryItemNode.appendChild(inventoryItemTextNode);
                            inventoryItemNode.appendChild(useItemButtonNode);
                            inventoryItemNode.appendChild(infoNode);
                            inventoryList.appendChild(inventoryItemNode);

                            document.getElementById("inventory_hover_show" + parsedGameEvent.basic[i].data.inventory_data[l].item_id).innerHTML = "";

                            makeListConditions(inventoryConditionsFormatted, "inventory_hover_show" + parsedGameEvent.basic[i].data.inventory_data[l].item_id);
                        } else {
                            inventoryItemNode = document.createElement("li");
                            inventoryItemTextNode = document.createTextNode("Bericht van de goden: " + parsedGameEvent.basic[i].data.inventory_data[l].conditions + " (nr: " + parsedGameEvent.basic[i].data.inventory_data[l].count + ")");
                            useItemButtonNode = document.createElement("div");
                            useItemButtonNode.setAttribute("class", "use_item_button");
                            useItemButtonNode.setAttribute("id", "inv" + parsedGameEvent.basic[i].data.inventory_data[l].item_id);
                            useItemButtonTextNode = document.createTextNode("Gebruiken");
                            useItemButtonNode.appendChild(useItemButtonTextNode);
                            infoNode = document.createElement("ul");
                            infoNode.setAttribute("class", "hover_show");
                            infoNode.setAttribute("id", "inventory_hover_show" + parsedGameEvent.basic[i].data.inventory_data[l].item_id);

                            inventoryItemNode.appendChild(inventoryItemTextNode);
                            inventoryItemNode.appendChild(useItemButtonNode);
                            inventoryItemNode.appendChild(infoNode);
                            inventoryList.appendChild(inventoryItemNode);
                        }
                    }
                }
                catchUseItemEvent();
            }
        }

        if (typeof parsedGameEvent.levelling.levelling !== "undefined") {
            var pageMonsterList = document.getElementById("monsters_list");
            pageMonsterList.innerHTML = "";
            var monsterList = parsedGameEvent.levelling.levelling.monster_data;
            var monstersAmount = Math.ceil(monsterList.length / 2);
            var colOne = document.createElement("div");
            colOne.setAttribute("id", "monster_column");
            var colTwo = document.createElement("div");
            colTwo.setAttribute("id", "monster_column_two");

            for (var k = 0; k < monsterList.length; k++) {

                if ((k + 1) <= monstersAmount) {
                    var monsterButtonNode = document.createElement("div");
                    var monsterButtonTextNode = document.createTextNode(monsterList[k].name);
                    monsterButtonNode.setAttribute("id", "exp" + monsterList[k].multiplier);

                    monsterButtonNode.appendChild(monsterButtonTextNode);
                    colOne.appendChild(monsterButtonNode);
                } else {
                    monsterButtonNode = document.createElement("div");
                    monsterButtonTextNode = document.createTextNode(monsterList[k].name);
                    monsterButtonNode.setAttribute("id", "exp" + monsterList[k].multiplier);

                    monsterButtonNode.appendChild(monsterButtonTextNode);
                    colTwo.appendChild(monsterButtonNode);
                }
            }

            pageMonsterList.appendChild(colOne);
            pageMonsterList.appendChild(colTwo);

            var expDisplayNode = document.createElement("div");
            expDisplayNode.setAttribute("id", "user_multiplier");
            pageMonsterList.appendChild(expDisplayNode);

            var multiplierNode = document.createElement("input");
            multiplierNode.setAttribute("type", "range");
            multiplierNode.setAttribute("min", 0);
            multiplierNode.setAttribute("max", 90);
            multiplierNode.setAttribute("step", 10);
            multiplierNode.setAttribute("id", "sld_exp");

            pageMonsterList.appendChild(multiplierNode);

            uExp = parsedGameEvent.levelling.levelling.user_exp;
            uExpM = parsedGameEvent.levelling.levelling.user_exp_multiplier;

            var expNode = document.createElement("div");
            var expTextNode = document.createTextNode("jou exp + ( je multiplier ✖ slider ✖ monster ) = exp");
            expNode.appendChild(expTextNode);
            expNode.setAttribute("id", "calc_exp");
            pageMonsterList.appendChild(expNode);

            var confirmButtonNode = document.createElement("input");
            confirmButtonNode.setAttribute("id", "btn_exp_user");
            confirmButtonNode.setAttribute("type", "button");
            confirmButtonNode.setAttribute("value", "Toevoegen");

            pageMonsterList.appendChild(confirmButtonNode);

            catchMonsterEvents();
        }

        renderShop(parsedGameEvent.shop);
    }, false);

    eventSource.onerror = function(e) {
        console.log("connection error [time: " + microtime(true) + "]");
        connectionErrors++;
    };

    setInterval(function () {
        if (((lastPing < (microtime(true) - 10)) || (connectionErrors >= 2)) && connectionLost === false) {
            var notif = new Notification("Jantje ging naar de winkel, maar zijn serververbinding werd verbroken. Dus overwoog hij de pagina te herladen.", true, "streamErrorNotif");
            connectionLost = true;
        }
    }, 1000);
}

function renderShop(shopArrays) {
    if (shopArrays !== false) {
        var shopItemsPerColumn = Math.ceil(shopArrays.length / 4);

        var shopListing = document.getElementById("shop_listing");

        var shopColOneNode = document.getElementById("shop_one");
        var shopColTwoNode = document.getElementById("shop_two");
        var shopColThreeNode = document.getElementById("shop_three");
        var shopColFourNode = document.getElementById("shop_four");
        shopColOneNode.innerHTML = "";
        shopColTwoNode.innerHTML = "";
        shopColThreeNode.innerHTML = "";
        shopColFourNode.innerHTML = "";

        shopListing.appendChild(shopColOneNode);
        shopListing.appendChild(shopColTwoNode);
        shopListing.appendChild(shopColThreeNode);
        shopListing.appendChild(shopColFourNode);

        for (var i = 0; i < shopArrays.length; i++) {
            if (i < shopItemsPerColumn) {
                addToShopColumn(shopArrays[i], "shop_one");
            } else if (i >= shopItemsPerColumn && i < (shopItemsPerColumn * 2)) {
                addToShopColumn(shopArrays[i], "shop_two");
            } else if (i >= (shopItemsPerColumn * 2) && i < (shopItemsPerColumn * 3)) {
                addToShopColumn(shopArrays[i], "shop_three");
            } else if (i >= (shopItemsPerColumn * 3) && i < (shopItemsPerColumn * 4)) {
                addToShopColumn(shopArrays[i], "shop_four");
            }
        }

        catchShopEvents();
    }
}

function catchShopEvents() {
    var buyButtons = document.getElementsByClassName("buy_item_button");

    for (var i = 0; i < buyButtons.length; i++) {
        buyButtons[i].addEventListener("click", function(e) {
            var sendData = {
                "user_id": userId,
                "buy_item": e.target.id.substr(3, 3)
            };

            sendXHR(JSON.stringify(sendData), "http/http_buy_item.php", "post", "processBuyItem");
        });
    }
}

function formatNumber(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function addToShopColumn(shopItem, column) {
    var columnAddTo = document.getElementById(column);

    var shopItemNode = document.createElement("li");
    var shopItemTextNode = document.createTextNode(shopItem.item_data.name);

    shopItemNode.setAttribute("id", "item" + shopItem.item_id);
    shopItemNode.appendChild(shopItemTextNode);

    columnAddTo.appendChild(shopItemNode);
    if (shopItem.can_buy !== true) {
        var messageNode = document.createElement("div");
        var messageTextNode = document.createTextNode(shopItem.can_buy);
        messageNode.appendChild(messageTextNode);
        shopItemNode.appendChild(messageNode)
    } else {
        var buyButtonNode = document.createElement("div");
        var buyButtonTextNode = document.createTextNode("Aankopen");
        buyButtonNode.setAttribute("class", "buy_item_button");
        buyButtonNode.setAttribute("id", "buy" + shopItem.item_id);
        buyButtonNode.appendChild(buyButtonTextNode);
        shopItemNode.appendChild(buyButtonNode);

        for (var i = 0; i < shopItem.price_data.length; i++ ) {
            var priceNode = document.createElement("div");
            var priceTextNode = document.createTextNode(shopItem.price_data[i].itemname + ": " + formatNumber(shopItem.price_data[i].value));
            priceNode.appendChild(priceTextNode);
            shopItemNode.appendChild(priceNode);
        }

        for (var j = 0; j < shopItem.skill_data.length; j++ ) {
            if (shopItem.skill_data[j].name !== "0") {
                var skillNode = document.createElement("div");
                var skillTextNode = document.createTextNode(shopItem.skill_data[j].skillname + " level: " + shopItem.skill_data[j].value);
                skillNode.appendChild(skillTextNode);
                shopItemNode.appendChild(skillNode);
            }
        }

        var inventoryConditionsFormatted = prepareConditions(shopItem.item_data.condition);

        makeListConditions(inventoryConditionsFormatted, "item" + shopItem.item_id);
    }
}

function catchMonsterEvents() {
    var displayMul = document.getElementById("user_multiplier");
    var sldValue = document.getElementById("sld_exp");
    displayMul.innerHTML = "Multiplier: x" + sldValue.value;

    document.getElementById("sld_exp").addEventListener("input", function (e) {
        displayMul.innerHTML = "Multiplier: x" + e.target.value;

        var activeMonsterButton = document.getElementsByClassName("active_monster");
        var monsterMultiplier = false;

        for (var k = 0; k < activeMonsterButton.length; k++) {
            monsterMultiplier = activeMonsterButton[k].id.substr(3, 2);
        }

        addExp = parseInt(parseInt(uExpM) * parseInt(e.target.value) * parseInt(monsterMultiplier));
        newExp = parseInt(uExp) + addExp;

        if (monsterMultiplier !== false) {
            document.getElementById("calc_exp").innerHTML = uExp + " + ( " + uExpM + " ✖ " + e.target.value + " ✖ " + monsterMultiplier + " ) = " + newExp;
        }
    });

    var colOneButtons = document.getElementById("monster_column").childNodes;
    var colTwoButtons = document.getElementById("monster_column_two").childNodes;

    for (var i = 0; i < colOneButtons.length; i++) {
        colOneButtons[i].addEventListener("click", function(e) {
            for (var i = 0; i < colOneButtons.length; i++) {
                colOneButtons[i].setAttribute("class", "");
                colTwoButtons[i].setAttribute("class", "");
            }

            document.getElementById(e.target.id).setAttribute("class", "active_monster");

            var activeMonsterButton = document.getElementsByClassName("active_monster");
            var monsterMultiplier = false;

            for (var k = 0; k < activeMonsterButton.length; k++) {
                monsterMultiplier = activeMonsterButton[k].id.substr(3, 2);
            }

            addExp = parseInt(parseInt(uExpM) * parseInt(sldValue.value) * parseInt(monsterMultiplier));
            newExp = parseInt(uExp) + addExp;

            if (monsterMultiplier !== false) {
                document.getElementById("calc_exp").innerHTML = uExp + " + ( " + uExpM + " ✖ " + sldValue.value + " ✖ " + monsterMultiplier + " ) = " + newExp;
            }
        });
    }

    for (var j = 0; j < colTwoButtons.length; j++) {
        colTwoButtons[j].addEventListener("click", function(e) {
            for (var i = 0; i < colTwoButtons.length; i++) {
                colTwoButtons[i].setAttribute("class", "");
                colOneButtons[i].setAttribute("class", "");
            }

            document.getElementById(e.target.id).setAttribute("class", "active_monster");

            var activeMonsterButton = document.getElementsByClassName("active_monster");
            var monsterMultiplier = false;

            for (var k = 0; k < activeMonsterButton.length; k++) {
                monsterMultiplier = activeMonsterButton[k].id.substr(3, 2);
            }

            addExp = parseInt(parseInt(uExpM) * parseInt(sldValue.value) * parseInt(monsterMultiplier));
            newExp = parseInt(uExp) + addExp;
            
            if (monsterMultiplier !== false) {
                document.getElementById("calc_exp").innerHTML = uExp + " + ( " + uExpM + " ✖ " + sldValue.value + " ✖ " + monsterMultiplier + " ) = " + newExp;
            }
        });
    }

    document.getElementById("btn_exp_user").addEventListener("click", function () {
        if (typeof newExp !== "undefined") {
            var monsterExpUser = {
                "add_exp_value": addExp,
                "user_id": userId,
                "basic_id": 11
            };
            sendXHR(JSON.stringify(monsterExpUser), "http/http_add_exp.php", "post", "processAddExp");
        } else {
            new Notification("Je moet een monster selecteren. (Ps. Sven is gay)", false);
        }
    });
}

function makeListConditions(conditionsCollection, appendTo) {
    var appendToElement = document.getElementById(appendTo);

    for (var n = 0; n < conditionsCollection.length; n++) {

        if (conditionsCollection[n].condition_id != 0) {
            var conditionNode = document.createElement("li");
            var conditionTextNode = document.createTextNode(conditionsCollection[n].name + " (aantal beurten: " + conditionsCollection[n].turns + ")");
            conditionNode.appendChild(conditionTextNode);
            var affectsNode = document.createElement("ul");

            for (var o = 0; o < conditionsCollection[n].affects.length; o++) {
                var effectNode = document.createElement("li");
                var effectTextNode = document.createTextNode(conditionsCollection[n].affects[o].damage + " effect op: " + conditionsCollection[n].affects[o].damage_on);

                effectNode.appendChild(effectTextNode);
                affectsNode.appendChild(effectNode);
            }

            conditionNode.appendChild(affectsNode);
            appendToElement.appendChild(conditionNode);
        }
    }
}

function prepareConditions(conditionsArray) {
    conditionsArray.sort();

    var conditionsFormatted = [];

    for (var k = 0; k < conditionsArray.length; k++) {
        var found = false;

        for (var m = 0; m < conditionsFormatted.length; m++) {
            if (conditionsFormatted[m].condition_id == conditionsArray[k].condition_id) {
                found = true;

                conditionsFormatted[m].affects[conditionsFormatted[m].affects.length] = {
                    damage_on: conditionsArray[k].damage_on,
                    damage: conditionsArray[k].damage
                };
            }
        }

        if (found === false) {
            conditionsFormatted[conditionsFormatted.length] = {
                name: conditionsArray[k].condition,
                condition_id: conditionsArray[k].condition_id,
                turns: conditionsArray[k].turns,
                affects: [
                    {
                        damage_on: conditionsArray[k].damage_on,
                        damage: conditionsArray[k].damage
                    }
                ]
            };
        }
    }

    return conditionsFormatted;
}

function showAdminTab(tab) {
    var tabOne = document.getElementById("admin_tab_one");
    var tabTwo = document.getElementById("admin_tab_two");
    var tabThree = document.getElementById("admin_tab_three");
    var tabFour = document.getElementById("admin_tab_four");


    var one = document.getElementById("admin_one");
    var two = document.getElementById("admin_two");
    var three = document.getElementById("admin_three");
    var four = document.getElementById("admin_four");

    if (tab === "one") {
        tabOne.style.display = "block";
        tabTwo.style.display = "none";
        tabThree.style.display = "none";
        tabFour.style.display = "none";
        one.setAttribute("class", "active");
        two.setAttribute("class", "");
        three.setAttribute("class", "");
        four.setAttribute("class", "");
    } else if (tab === "two") {
        tabOne.style.display = "none";
        tabTwo.style.display = "block";
        tabThree.style.display = "none";
        tabFour.style.display = "none";
        one.setAttribute("class", "");
        two.setAttribute("class", "active");
        three.setAttribute("class", "");
        four.setAttribute("class", "");
    } else if (tab === "three") {
        tabOne.style.display = "none";
        tabTwo.style.display = "none";
        tabThree.style.display = "block";
        tabFour.style.display = "none";
        one.setAttribute("class", "");
        two.setAttribute("class", "");
        three.setAttribute("class", "active");
        four.setAttribute("class", "");
    } else if (tab === "four") {
        tabOne.style.display = "none";
        tabTwo.style.display = "none";
        tabThree.style.display = "none";
        tabFour.style.display = "block";
        one.setAttribute("class", "");
        two.setAttribute("class", "");
        three.setAttribute("class", "");
        four.setAttribute("class", "active");
    }
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
                case "processUserList":
                    processUserList(response);
                    break;
                case "processRegisterResponse":
                    processRegisterResponse(response);
                    break;
                case "processDeleteUser":
                    processDeleteUser(response);
                    break;
                case "processRacesAndClasses":
                    processRacesAndClasses(response);
                    break;
                case "processRegisterClassRace":
                    processRegisterClassRace(response);
                    break;
                case "processNextTurn":
                    processNextTurn(response);
                    break;
                case "processBasics":
                    processBasics(response);
                    break;
                case "processConditions":
                    processConditions(response);
                    break;
                case "processInventoryItems":
                    processInventoryItems(response);
                    break;
                case "processBuyItem":
                    processBuyItem(response);
                    break;
                case "processRandomItemsAdd":
                    processRandomItemsAdd(response);
                    break;
            }
        }
    };

    xhr.onerror = function() { new Notification("De actie werd niet verzonden. Er zijn mogelijk connectie problemen.", false); };

    xhr.open(type, url);

    if (type === "post") {
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("data=" + data);
    } else {
        xhr.send();
    }
}

function processBuyItem(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_accepted === "true") {
            new Notification("Je hebt het item succesvol aangekocht. Dit is een nieuwe milestone in je avontuur, geef nu al je geld aan Nick!", false);
        }
        else {
            displayErrors(responseParse.errors);
        }
    }
}

function processNextTurn(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_accepted === "true") {
            new Notification("Beurt doorgegeven aan: " + responseParse.next_user, false);
        }
        else {
            displayErrors(responseParse.errors);
        }
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
    for (var i = 0; i < errorsAmount; i++) {
        var notification = new Notification(errorsObject[i], false);
    }
}

function requestUserData(requestUserId) {
    var requestDataFromUser = {};

    if (typeof (requestUserId) === "undefined") {
        requestDataFromUser.user_id = "false";
    } else {
        requestDataFromUser.user_id = requestUserId;
    }

    var jsonData = JSON.stringify(requestDataFromUser);

    sendXHR(jsonData, "http/http_user_data.php", "post", "processUserData");
}

function enableGameArea() {
    document.getElementById("game_area").style.opacity = "1";
}

function processUserData(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_accepted === "true") {
            disableLogin();


            if (admin === "true") {
                ownUserId = responseParse.data.admin_data.user_id;
            } else {
                ownUserId = responseParse.data.user_id;
            }

            userId = responseParse.data.user_id;

            if(responseParse.data.class !== "0" && responseParse.data.race !== "0") {
                var user_info = document.getElementById("user_info");

                user_info.innerHTML = "Welkom " + responseParse.data.username + " <span>(" + responseParse.data.permission_name + ")</span>";
                userId = responseParse.data.user_id;
                permissionId = responseParse.data.permission_type;
                admin = responseParse.data.admin;

                if (admin === "true") {
                    showAdminPanel();
                    var adminPanelName = document.getElementById("admin_user");

                    adminPanelName.innerHTML = responseParse.data.admin_data.username;
                    requestUserList();
                }

                enableGameArea();
                openStream();
            } else {
                enableRaceClassPrompt();
            }
        } else {
            displayErrors(responseParse.errors);
        }
    }
}

function enableRaceClassPrompt() {
    var chooseRaceAndClass = document.getElementById("choose_race_class");

    chooseRaceAndClass.style.opacity = "1";
    chooseRaceAndClass.style.pointerEvents = "all";

    requestRacesAndClasses();
}

function requestRacesAndClasses() {
    var requestClassAndRaceData = {};

    requestClassAndRaceData.user_id = ownUserId;

    var jsonData = JSON.stringify(requestClassAndRaceData);

    sendXHR("", "http/http_classes_and_races_list.php", "get", "processRacesAndClasses");
}

function processRacesAndClasses(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_legal === "true") {
            var racesAmount = responseParse.data.races.length;
            var classesAmount = responseParse.data.classes.length;

            var racesList = document.getElementById("race");
            var classesList = document.getElementById("class");

            for (var i = 0; i < racesAmount; i++) {
                var raceNode = document.createElement("li");
                var raceNodeText = document.createTextNode(responseParse.data.races[i].name);
                raceNode.appendChild(raceNodeText);
                raceNode.setAttribute("id", "race" + responseParse.data.races[i].race_id);

                raceNode.addEventListener("click", function(e) { processRaceClick(e); });

                racesList.appendChild(raceNode);
            }

            for (var j = 0; j < racesAmount; j++) {
                var classNode = document.createElement("li");
                var classNodeText = document.createTextNode(responseParse.data.classes[j].name);
                classNode.appendChild(classNodeText);
                classNode.setAttribute("id", "class" + responseParse.data.classes[j].class_id);

                classNode.addEventListener("click", function(e) { processClassClick(e); });

                classesList.appendChild(classNode);
            }
        } else {
            displayErrors(responseParse.errors);
        }
    }
}

function processRaceClick(e) {
    var raceClicked = document.getElementById(e.target.id);

    var racesButtons = document.getElementById("race").childNodes;
    var raceButtonsAmount = racesButtons.length;

    for (var i = 0; i < raceButtonsAmount; i++) {
        racesButtons[i].setAttribute("class", "");
    }

    raceClicked.setAttribute("class", "race_class_active");
}

function processClassClick(e) {
    var classClicked = document.getElementById(e.target.id);

    var classButtons = document.getElementById("class").childNodes;
    var classButtonsAmount = classButtons.length;

    for (var i = 0; i < classButtonsAmount; i++) {
        classButtons[i].setAttribute("class", "");
    }

    classClicked.setAttribute("class", "race_class_active");
}

function requestChoiceRaceClass() {
    var chosenClass, chosenRace;

    var classButtons = document.getElementById("class").childNodes;
    var racesButtons = document.getElementById("race").childNodes;

    var classButtonsAmount = classButtons.length;
    var raceButtonsAmount = racesButtons.length;

    for (var i = 0; i < classButtonsAmount; i++) {
        if (classButtons[i].className === "race_class_active") {
            chosenClass = classButtons[i].id.substr(5,1);
        }
    }

    for (var j = 0; j < raceButtonsAmount; j++) {
        if (racesButtons[j].className === "race_class_active") {
            chosenRace = racesButtons[j].id.substr(4,1);
        }
    }

    if (typeof (chosenClass) === "undefined" || typeof (chosenRace) === "undefined") {
        var notif = new Notification("Je moet een ras en klasse selecteren", false);
    } else {
        var chosenClassAndRace = {};

        chosenClassAndRace.user_id = userId;
        chosenClassAndRace.race = chosenRace;
        chosenClassAndRace.class = chosenClass;

        sendXHR(JSON.stringify(chosenClassAndRace), "http/http_register_class_and_race.php", "post", "processRegisterClassRace");
    }
}

function processRegisterClassRace(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_legal !== "true") {
            displayErrors(responseParse.errors);
        } else {
            requestUserData();
            disableRaceClassPrompt();
            enableGameArea();
        }
    }
}

function disableRaceClassPrompt() {
    var chooseRaceAndClass = document.getElementById("choose_race_class");

    chooseRaceAndClass.style.opacity = "0";
    chooseRaceAndClass.style.pointerEvents = "none";
}

function requestUserList() {
    sendXHR("", "http/http_user_list.php", "get", "processUserList");
}

function processUserList(jsonData) {
    var responseParse = parseJSON(jsonData);

    var usersCount = responseParse.data.length;

    var userListNode = document.createElement("ul");

    for (var i = 0; i < usersCount; i++) {
        var userNode = document.createElement("li");
        var usernameNode = document.createElement("div");
        var usernameText = document.createTextNode(responseParse.data[i].username);
        var deleteUserNode = document.createElement("div");
        var deleteUserText = document.createTextNode("✖");
        deleteUserNode.appendChild(deleteUserText);
        usernameNode.appendChild(usernameText);
        userNode.appendChild(usernameNode);
        userNode.appendChild(deleteUserNode);
        usernameNode.setAttribute("class", "change_userview");
        usernameNode.setAttribute("id", responseParse.data[i].user_id);
        deleteUserNode.setAttribute("class", "delete_user");
        deleteUserNode.setAttribute("id", responseParse.data[i].user_id);
        userListNode.appendChild(userNode);
    }

    var userListTab = document.getElementById("admin_tab_one");
    userListTab.innerHTML = "";

    userListTab.appendChild(userListNode);

    //openStream();
    requestBasics();
    requestConditions();
    requestInventory();

    var changeUserview = document.getElementsByClassName("change_userview");
    var deleteUser = document.getElementsByClassName("delete_user");

    for (var j = 0; j < changeUserview.length; j++) {
        changeUserview[j].addEventListener("click", function(e) {
            e.stopPropagation();
            requestUserData(this.id);
        });

        deleteUser[j].addEventListener("click", function(e) {
            e.stopPropagation();
            if (this.id == ownUserId) new Notification("Je kan jezelf niet verwijderen.", false);
            else if (confirm("De gebruiker verwijderen?") === true) requestDeleteUser(this.id);
        });
    }
}

function requestInventory() {
    sendXHR("", "http/http_list_inventory.php", "get", "processInventoryItems");
}

function processInventoryItems(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_legal !== "true") {
            displayErrors(responseParse.errors);
        } else {
            var conditions = document.getElementById("inventory");
            conditions.innerHTML = "<h3>Inventory</h3>";
            var selectNode = document.createElement("select");
            selectNode.setAttribute("id", "lst_inventory");

            for (var i = 0; i < responseParse.data.length; i++) {
                var optionNode = document.createElement("option");
                var optionTextNode = document.createTextNode(responseParse.data[i].name);
                optionNode.setAttribute("value", responseParse.data[i].item_id);

                optionNode.appendChild(optionTextNode);
                selectNode.appendChild(optionNode);
            }

            conditions.appendChild(selectNode);

            var addButtonNode = document.createElement("div");
            var addButtonTextNode = document.createTextNode("+1");
            addButtonNode.setAttribute("id", "add_inventory");
            addButtonNode.setAttribute("class", "inventory_button");
            addButtonNode.appendChild(addButtonTextNode);

            conditions.appendChild(addButtonNode);

            var subButtonNode = document.createElement("div");
            var subButtonTextNode = document.createTextNode("-1");
            subButtonNode.setAttribute("id", "sub_inventory");
            subButtonNode.setAttribute("class", "inventory_button");
            subButtonNode.appendChild(subButtonTextNode);

            conditions.appendChild(subButtonNode);

            document.getElementById("lst_inventory").addEventListener("click", function (e) {
                e.stopPropagation();
            });

            catchInventoryEvent();
        }
    }
}

function catchInventoryEvent() {
    var conditionButton = document.getElementsByClassName("inventory_button");

    for (var i = 0; i < conditionButton.length; i++) {
        conditionButton[i].addEventListener("click", function(e) {
            e.stopPropagation();

            var action;

            if (e.target.id === "add_inventory") action = "add";
            else action = "substract";

            requestAddInventory(action);
        });
    }
}

function requestAddInventory(action) {
    var conditionsList = document.getElementById("lst_inventory");
    var condition = conditionsList.options[conditionsList.selectedIndex].value;

    var requestCondition = {
        "action": action,
        "user_id": userId,
        "inventory": condition
    };
    sendXHR(JSON.stringify(requestCondition), "http/http_add_inventory.php", "post", "processAddInventory");
}

function requestConditions() {
    sendXHR("", "http/http_list_conditions.php", "get", "processConditions");
}

function processConditions(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_legal !== "true") {
            displayErrors(responseParse.errors);
        } else {
            var conditions = document.getElementById("conditions");
            conditions.innerHTML = "<h3>Conditions</h3>";
            var selectNode = document.createElement("select");
            selectNode.setAttribute("id", "lst_conditions");

            for(var i = 0; i < responseParse.data.length; i++) {
                var optionNode = document.createElement("option");
                var optionTextNode = document.createTextNode(responseParse.data[i].name);
                optionNode.setAttribute("value", responseParse.data[i].condition_id);

                optionNode.appendChild(optionTextNode);
                selectNode.appendChild(optionNode);
            }

            conditions.appendChild(selectNode);

            var addButtonNode = document.createElement("div");
            var addButtonTextNode = document.createTextNode("+1");
            addButtonNode.setAttribute("id", "add_condition");
            addButtonNode.setAttribute("class", "condition_button");
            addButtonNode.appendChild(addButtonTextNode);

            conditions.appendChild(addButtonNode);

            var subButtonNode = document.createElement("div");
            var subButtonTextNode = document.createTextNode("-1");
            subButtonNode.setAttribute("id", "sub_condition");
            subButtonNode.setAttribute("class", "condition_button");
            subButtonNode.appendChild(subButtonTextNode);

            conditions.appendChild(subButtonNode);

            document.getElementById("lst_conditions").addEventListener("click", function (e){ e.stopPropagation(); });

            catchConditionEvents();
        }
    }
}

function catchConditionEvents() {
    var conditionButton = document.getElementsByClassName("condition_button");

    for (var i = 0; i < conditionButton.length; i++) {
        conditionButton[i].addEventListener("click", function(e) {
            e.stopPropagation();

            var action;

            if (e.target.id === "add_condition") action = "add";
            else action = "substract";

            requestAddCondition(action);
        });
    }
}

function requestAddCondition(action) {
    var conditionsList = document.getElementById("lst_conditions");
    var condition = conditionsList.options[conditionsList.selectedIndex].value;

    var requestCondition = {
        "action": action,
        "user_id": userId,
        "condition": condition
    };
    sendXHR(JSON.stringify(requestCondition), "http/http_add_condition.php", "post", "processAddCondition");
}

function requestBasics() {
    sendXHR("", "http/http_list_basic.php", "get", "processBasics");
}

function processBasics(jsonData){
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_legal !== "true") {
            displayErrors(responseParse.errors);
        } else {
            document.getElementById("admin_tab_three").innerHTML = "<ul id=\"basic_controls\"></ul>";
            var basicControls = document.getElementById("basic_controls");

            for (var i = 0; i < responseParse.data.length; i++) {
                var basicControlsNode = document.createElement("li");
                var basicControlsButtonNode = document.createElement("div");
                basicControlsButtonNode.setAttribute("class", "no_pointer");
                var basisControlsTextNode = document.createTextNode(responseParse.data[i].name);
                basicControlsButtonNode.appendChild(basisControlsTextNode);
                basicControlsNode.appendChild(basicControlsButtonNode);

                var plusOneNode = document.createElement("div");
                var plusOneTextNode = document.createTextNode("+1");
                plusOneNode.appendChild(plusOneTextNode);
                plusOneNode.setAttribute("class", "basic_control_button");
                plusOneNode.setAttribute("id", "plus" + responseParse.data[i].basic_id);
                basicControlsNode.appendChild(plusOneNode);

                var minuOneNode = document.createElement("div");
                var minuOneTextNode = document.createTextNode("-1");
                minuOneNode.appendChild(minuOneTextNode);
                minuOneNode.setAttribute("class", "basic_control_button");
                minuOneNode.setAttribute("id", "minu" + responseParse.data[i].basic_id);
                basicControlsNode.appendChild(minuOneNode);

                basicControls.appendChild(basicControlsNode);
            }

            var basicAdminTab = document.getElementById("admin_tab_three");
            var basicMessageNode = document.createElement("div");

            var messageTextFieldNode = document.createElement("input");
            messageTextFieldNode.setAttribute("type", "text");
            messageTextFieldNode.setAttribute("placeholder", "Bericht aan speler");
            messageTextFieldNode.setAttribute("id", "txt_message");

            basicAdminTab.appendChild(basicMessageNode);
            basicAdminTab.appendChild(messageTextFieldNode);

            var expNode = document.createElement("div");
            var expTextNode = document.createTextNode("Exp:");
            expNode.setAttribute("id", "exp_slider_value");

            expNode.appendChild(expTextNode);
            var expSlider = document.createElement("input");
            expSlider.setAttribute("type", "range");
            expSlider.setAttribute("id", "exp_slider");
            expSlider.setAttribute("max", "5000");
            expSlider.setAttribute("min", "-5000");
            expSlider.setAttribute("step", "20");

            basicAdminTab.appendChild(expNode);
            basicAdminTab.appendChild(expSlider);

            var sendExpNode = document.createElement("input");
            sendExpNode.setAttribute("type", "button");
            sendExpNode.setAttribute("value", "Verstuur Exp");
            sendExpNode.setAttribute("id", "btn_exp");

            basicAdminTab.appendChild(sendExpNode);

            catchBasicControlEvent();
        }
    }
}

function catchBasicControlEvent() {
    var basicControlButtons = document.getElementsByClassName("basic_control_button");
    var basicExpSlider = document.getElementById("exp_slider");

    var expSliderValue = document.getElementById("exp_slider_value");
    expSliderValue.innerHTML = "Exp: " + basicExpSlider.value;

    basicExpSlider.addEventListener("input", function(e) {
        e.stopPropagation();
        expSliderValue.innerHTML = "Exp: " + e.target.value;
    });

    basicExpSlider.addEventListener("click", function(e) {  e.stopPropagation(); });

    for(var i = 0; i < basicControlButtons.length; i++) {
        basicControlButtons[i].addEventListener("click", function(e) { requestBasic(e); e.stopPropagation(); });
    }

    document.getElementById("txt_message").addEventListener("click", function (e) { e.stopPropagation(); });
    document.getElementById("txt_message").addEventListener("keydown", function (e) {
        if (e.keyCode === 13) {
            var userMessage = {
                "message": e.target.value,
                "user_id": userId
            };

            document.getElementById("txt_message").value = "";

            sendXHR(JSON.stringify(userMessage), "http/http_user_message.php", "post", "processUserMessage");
        }
    });

    document.getElementById("btn_exp").addEventListener("click", function(e) {e.stopPropagation(); requestExpAddition(); });
}

function requestExpAddition() {
    var exp = document.getElementById("exp_slider").value;

    var expInfo = {
        "exp": exp,
        "user_id": userId
    };

    sendXHR(JSON.stringify(expInfo), "http/http_exp_add.php", "post", "processExpAdd");
}

function requestBasic(e) {
    var eventId = e.target.id;
    var action = eventId.substr(0, 4);
    var basicId = eventId.substr(4, 1);
    var performAction;

    if (action === "plus") {
        performAction = "add";
    } else {
        performAction = "substract";
    }

    var requestBasicData = {
        "basic_id": basicId,
        "action": performAction,
        "user_id": userId
    };

    sendXHR(JSON.stringify(requestBasicData), "http/http_add_basic.php", "post", "processBasicResponse");
}

function requestDeleteUser(deleteUserId) {
    var userDeleteData = {};

    userDeleteData.user_id = deleteUserId;

    var deleteData = JSON.stringify(userDeleteData);

    sendXHR(deleteData, "http/http_delete_user.php", "post", "processDeleteUser");
}

function processDeleteUser(jsonData) {
    var responseParse = parseJSON(jsonData);

    if (responseParse !== null) {
        if (responseParse.request_legal === "true") {
            var notif = new Notification("De gebruiker werd succesvol verwijderd.", false);
            requestUserData();
        }
        else {
            displayErrors(responseParse.errors);
        }
    }
}

function showAdminPanel() {
    var adminPanel = document.getElementById("show_admin");

    adminPanel.style.display = "block";
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

var Notification = function(message, keepAlive, notifClass) {
    this.message = message;
    this.notifId = ("" + (Math.floor((Math.random() * 10000) + 1000))).substring(0,4);
    this.keepAlive = keepAlive;

    var notifContainer = document.getElementById("notifications");

    var notifNode = document.createElement("div");
    var notifText = document.createTextNode(message);
    notifNode.appendChild(notifText);
    notifNode.setAttribute("id", this.notifId);
    if (notifClass !== "undefined") notifNode.setAttribute("class", notifClass);
    notifNode.style.opacity = "0";
    setTimeout(function() { notifNode.style.opacity = "1"; }, 5);

    notifContainer.appendChild(notifNode);

    if(keepAlive !== true) {
        this.disable();
    }

    document.getElementById(this.notifId).addEventListener("click", function(e) { disableClick(e.target.id); } );
};

Notification.prototype.disable = function() {
    var currentId = this.notifId;
    var currentNotification = document.getElementById(this.notifId);

    setTimeout(function() {
        currentNotification.style.opacity = "0";
        setTimeout(function() { currentNotification.remove() }, 490);
    }, 7000);
};

function disableClick(id) {
    var currentNotification = document.getElementById(id);

    currentNotification.style.opacity = "0";
    setTimeout(function() { currentNotification.remove() }, 490);
}