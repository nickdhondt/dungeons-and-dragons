<?php

require_once("../includes/functions.php");
session_start();

$errors = array();
$request_legal = "false";

$user_id = user_logged_in();

if ($user_id != false) {
    $post_data = json_decode($_POST["data"], true);

    if (empty($post_data["user_id"])) {
        $errors[] = "Er is een fout opgetreden: \"geen user id ontvangen\"";
        $request_legal = "false";
    }

    $fields = array("permission_type");
    $user_data = user_data($user_id, $fields);

    if ($user_data["permission_type"] !== "1" && $post_data["user_id"] != $user_id) {
        $request_legal = "false";
        $errors[] = "De gebruiker is geen administrator";
    } else {
        $ids = extract_item_ids();
        $rand_items = array();

        for ($i = 0 ; $i < 10; $i++) {
            $item = $ids[rand(0, (count($ids) - 1))];

            $rand = rand(0, $item["type"]);

            if ($rand === 0) {
                $rand_items[] = $item;
            }
        }

        for ($j = 0; $j < count($rand_items); $j++) {
            add_to_user_inventory($post_data["user_id"], $rand_items[$j]["item_id"]);
        }

        $request_legal = "true";
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