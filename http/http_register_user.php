<?php

require_once("../includes/functions.php");
session_start();

$request_legal = "false";

$user_id = user_logged_in();

if ($user_id != false) {
    $fields = array("permission_type");
    $user_data = user_data($user_id, $fields);

    if ($user_data["permission_type"] === "1") {
        if (!empty($_POST["data"])) {
            $post_data = json_decode($_POST["data"], true);

            if (!empty($post_data["username"]) && !empty($post_data["password"])) {
                if (strlen($post_data["username"]) > 16 || strlen($post_data["username"]) < 3) {
                    $errors[] = "De gebruikernaam moet tussen 3 en 16 karakters lang zijn.";
                    $request_legal = "false";
                }

                if (strlen($post_data["password"]) > 16 || strlen($post_data["password"]) < 3) {
                    $errors[] = "Het wachtwoord moet tussen 3 en 16 karakters lang zijn.";
                    $request_legal = "false";
                }

                if (empty($errors)) {
                    $status = register_user($post_data["username"], $post_data["password"]);

                    if ($status === true) {
                        $request_legal = "true";
                    } else {
                        $request_legal = "false";
                        $errors[] = "Gebruiker niet geregistreerd. Probleem:\"" . $status . "\"";
                    }
                }
            } else {
                $errors[] = "Zowel gebruikersnaam als wachtwoord moeten ingevuld worden.";
                $request_legal = "false";
            }
        } else {
            $request_legal = "false";
            $errors[] = "verkeerde aanvraag.";
        }
    } else {
        $request_legal = "false";
        $errors[] = "Gebruiker geen administrator.";
    }
} else {
    $request_legal = "false";
    $errors[] = "Gebruiker niet ingelogd.";
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