<?php
//Summarization:
/*  This page expects:
        "next_turn", "user_id"
    This page will execute the following:
        - Set the next turn to the incremented or resetted number.
        - Check the conditions of the next player.

*/

require_once "../includes/functions.php";
session_start();

$errors = array();
$post_data = array();
$data_acquired = "false";
$next_turn = 0;

if(empty($_POST["data"])){
    //Set flag to false
    $data_acquired = "false";
    $errors[] = "Foute of geen data ontvangen.";
} else {
    //Set flag to true
    $data_acquired = "true";
    //Decode the post
    $post_data = json_encode($_POST["data"], true);

    //Check if the user has got sufficient rights to do this action.
        //Get the permission type.
    $user_id = $post_data["user_id"];
    $fields = array("user_id", "permission_type");
    $user_data = user_data($user_id, $fields);

        //Check for admin
    if($user_data["permission"] === "1"){
        if(!empty($post_data["next_turn"])){
            $current_turn = $post_data["next_turn"];

            //At this point, all of the nessecary checks have been done.
                //Get the number of users.
            $max_turn = get_number_of_users();

                //Check if the next turn is greater.
            $next_turn = $current_turn + 1;
            if($next_turn > $max_turn) $next_turn = 0;

                //Write the next turn to the db.
            $succes = update_turn_in_db($next_turn);
            if($succes){
                //The turn has been updated in the database, but we must re-evaluate all the conditions for the user at next turn.
                //Determine which user can play in the next turn.
                    /* INSERT LOGIC HERE */
            } else {
                $errors[] = "Er heeft zich een fout voorgedaan tijdens het wegschrijven naar de database in http_next_turn.";
            }
        } else {
            $data_acquired = "false";
            $errors[] = "De server kreeg niet de verwachtte data. 'next_turn not found at the JSON parse in http_next_turn'";
        }
    } else {
        $errors[] = "De gebruiker heeft geen rechten om deze bewerking uit te voeren.";
    }
}

// Prepare the return data
if ($data_acquired === "true" && empty($errors)) {
    $return_message = array(
        "request_accepted" => $data_acquired,
        "data" => $next_turn
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