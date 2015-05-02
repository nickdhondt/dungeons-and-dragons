<?php

require_once("../includes/functions.php");
session_start();

$errors = array();
$request_legal = "false";

$user_id = user_logged_in();

if ($user_id != false) {
    $post_data = json_decode($_POST["data"], true);

    if (empty($post_data["user_id"]) || empty($post_data["user_id"])) {
        $errors[] = "Er is een fout opgetreden: \"geen user id ontvangen en/of item. De kassierster kan niet afrekenen.\"";
        $request_legal = "false";
    }

    $fields = array("permission_type");
    $user_data = user_data($user_id, $fields);

    if ($user_data["permission_type"] !== "1" && $post_data["user_id"] != $user_id) {
        $request_legal = "false";
        $errors[] = "De gebruiker is geen administrator.";
    } else {
        $request_legal = "true";

        $shop_data = get_shop_data($post_data["user_id"], 0);
        $will_buy = array();

        foreach ($shop_data as $shop_item) {
            if ($shop_item["item_id"] == $post_data["buy_item"]) {
                $will_buy = $shop_item;
            }
        }

        //print_r($will_buy);

        foreach ($will_buy["price_data"] as $price) {
            if($price["item"] == 33) {
                echo "gold";
                write_to_user_basic($post_data["user_id"], 7, -$price["value"]);
            } else  {
                add_to_user_inventory($post_data["user_id"], $post_data["buy_item"], $substract = false, $value = $price["value"]);
            }
        }

        add_to_user_inventory($post_data["user_id"], $post_data["buy_item"], $substract = false, $value = "");
    }
} else {
    $request_legal = "false";
    $errors[] = "De gebruiker is niet ingelogd";
}

if ($request_legal === "true" && empty($errors)) {
    $return_message = array(
        "request_legal" => $request_legal
    );
} else {
    $return_message = array(
        "request_legal" => $request_legal,
        "errors" => $errors
    );
}

// JSON encode our array
$return_data = json_encode($return_message);

// "Return" the json string to the client
echo $return_data;