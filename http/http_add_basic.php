<?php
//This page expects:
    //"basic_id", "action", "user_id"
//This page will execute the following:
    //Change the required values in the database

require_once "../includes/functions.php";
session_start();

$errors = array();
$post_data = array();
$data_acquired = "false";
//$_POST["data"] = array("basic_id"=>"0", "action"=>"add", "user_id"=>"1");

if(empty($_POST["data"])){
    //Set the flag to false
    $data_acquired = "false";
    $errors[] = "Foute of geen data ontvangen";
} else {
    //Set flag to true
    $data_acquired = "true";
    //Decode the post
    $post_data = json_decode($_POST["data"], true);
    //$post_data = $_POST["data"];

    //Check if the post data contains all the needed values
    if((isset($post_data["basic_id"])) && (!empty($post_data["action"])) && (isset($post_data["user_id"]))){
        //Save the values
        $basic_id = $post_data["basic_id"];
        $action = $post_data["action"];
        $user_id = $post_data["user_id"];

        //Check if the user has got sufficient rights to do this action.
        //Get the permission type.
        $user_logged_id = user_logged_in();
        if($user_logged_id != false){
             $fields = array("user_id", "permission_type");
             $user_data = user_data($user_logged_id, $fields);

             //Check for admin
            if($user_data["permission_type"] === "1"){
                //Write to the database
                switch($action){
                    case "add":
                        $request_completed = write_to_user_basic($user_id, $basic_id, 1);
                        $request_msg = "Er is iets fout gegaan. Whoopsie";
                        break;
                    case "substract":
                        $request_completed = write_to_user_basic($user_id, $basic_id, -1);
                        $request_msg = "Er is iets fout gegaan. Whoopsie";
                        break;
                    default:
                        $request_completed = "false";
                        $request_msg = "The action was undefined. (add or substract expected) Check your message header.";
                        break;
                }

                if($request_completed == 69){
                    $request_completed = "false";
                    $request_msg = "Uw basic waarde mag niet onder 0 gaan.";
                }

                if($request_completed == "true"){
                    $data_acquired = "true";
                } else {
                    $data_acquired = "true";
                    $errors[] = "Error, de server meldt het volgende";
                    $errors[] = $request_msg;
                }
            } else {
                $data_acquired = "false";
                $errors[] = "U heeft geen rechten om deze bewerking uit te voeren. Contacteer een administrator.";
            }
        } else {
            $data_acquired = "false";
            $errors[] = "Het systeem kon niet achterhalen wie of wat u bent.";
            $errors[] = "Gelieve uw identiteitscrisis achter u te laten en linea recta naar de mannen van de IT te zoeken.";
        }
    } else {
        $data_acquired = "false";
        $errors[] = "Niet alle data ontvangen. Een of meerdere verwachte elementen ontbreken";
    }
}

// Prepare the return data
if ($data_acquired === "true" && empty($errors)) {
    $return_message = array(
        "request_accepted" => $data_acquired,
        "data" => "true"
    );
} else {
    $return_message = array(
        "request_accepted" => $data_acquired,
        "errors" => $errors
    );
}

// JSON encode our array
$return_data = json_encode($return_message);

// "Return" the json string to the client
echo $return_data;