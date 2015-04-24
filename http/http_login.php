<?php

// Include functions.php
require_once("../includes/functions.php");

$errors = array();
$user_passed = "false";
$user_id = 0;
$user_data  = array();

// Data will be receive as a JSON string in the $_POST superglobal.
// The JSON string will always be in the "data" value.
if (!empty($_POST["data"])) {

    // Convert the JSON string to an array
    // Note: second parameter makes sure the string will be converted to an array and not a php opbject
    $post_data = json_decode($_POST["data"], true);

    // Checking if the username and password have been received
    if (empty($post_data["username"]) || empty($post_data["password"])) {
        // Set errors
        // Set user passed flag to false
        // Explanation: On the login page the value of $user_passed will be checked
        //              If this is false, the errors in the errors array (remember this is send using JSON) will be shown to the user
        //              If this is true, the javascript code will proceed to log the user in.
        $errors[] = "Gebruikersnaam en wachtwoord moeten ingevuld zijn.";
        $user_passed = "false";
    } else {
        // Get the user id (or false if the user does not exist)
        $user_id = user_exists($post_data["username"]);
        // Check if the user exists
        if ($user_id === false) {
            // Set user passed flag and errors.
            $errors[] = "Gebruiker niet gevonden.";
            $user_passed = "false";
        } else {
            // Get the password
            $fields = array("password");
            $user_data = user_data($user_id, $fields);

            // Check if the password is correct
            if (password_verify($post_data["password"], $user_data["password"])) {
                // Set user passed flag to true
                $user_passed = "true";
                // Set a session to verify if the user is logged in
                // If we receive a http request (on either page eg. http_user_data.php, http_***.php), we will check if the user is logged in (using the session)
                // If a user with admin rights requests data (or wants to perform an db update), we will send the data (or update the records). Other users won't be allowed to do this.
                $_SESSION["user_id"] = $user_id;
            } else {
                // Set errors and user flag
                $errors[] = "Wachtwoord is onjuist.";
                $user_passed = "false";
            }
        }
    }
} else {
    // Set errors and user flag
    $errors[] = "Verkeerde loginaanvraag.";
    $user_passed = "false";
}

// If the are errors and the user passed flag is false the appropriate data is returned.
// Guidelines: error are put in an "errors" key value pair and data in a "data" key value pair.
if (!empty($errors) && $user_passed === "false") {
    $return_message = array(
        "login" => $user_passed,
        "errors" => $errors // Note: $errors is an array put in an array => multidimensional array
    );
    // JSON encode our array
    $return_data = json_encode($return_message);
} else {
    // Guideline: make sure the data is understandable client side.
    // Example: errors are put in a numeric array => no problem, errors are errors, we just display them.
    //          (see below) The user id is put in an associative array because if we receive the data on the other end (client side), it is useful to know what parameter means what
    $return_message = array(
        "login" => $user_passed,
        "data" => array("user_id" => $user_id)
    );
    // JSON encode the array
    $return_data = json_encode($return_message);
}

// "Return" the json string to the client
echo $return_data;