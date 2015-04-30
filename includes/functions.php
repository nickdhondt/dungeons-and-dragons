<?php

/* The well known functions.php */

require_once("db/connect.php");

function user_exists($username) {
    global $connection;

    $sql = $connection->query("SELECT user_id FROM user WHERE username='$username'");

    //$sql->bind_param("s", $username);

    //$sql->execute();

    if (!$sql) {
        return $connection->error;
    } else {
        if ($sql->num_rows === 1) {
            $user_id = $sql->fetch_assoc();
            return $user_id["user_id"];
        } else {
            return false;
        }
    }
}

function user_id_exists($user_id) {
    global $connection;

    $sql = $connection->query("SELECT COUNT(user_id) AS users FROM user WHERE user_id='$user_id'");

    if (!$sql) {
        return $connection->error;
    } else {
        $users = $sql->fetch_assoc();
        if ($users >= 1) {
            return true;
        } else {
            return false;
        }
    }
}

function user_data($user_id, $fields) {
    global $connection;

    $sql_fields = prepare_fields_select($fields);

    $sql = $connection->query("SELECT $sql_fields FROM user WHERE user_id='$user_id'");

    if (!$sql) {
        return $connection->error;
    } else {
        return $sql->fetch_assoc();
    }
}

function permission_name($permission_type) {
    global $connection;

    $sql = $connection->query("SELECT name FROM permission WHERE type='$permission_type'");

    $permission_name = $sql->fetch_assoc();

    return $permission_name["name"];
}

function prepare_fields_select($fields) {
    if (!empty ($fields)) {
        $sql_fields = implode(", ", $fields);
    } else {
        $sql_fields = "*";
    }

    return $sql_fields;
}

function user_logged_in () {
    if (!empty($_SESSION["user_id"])) {
        return $_SESSION["user_id"];
    } else {
        return false;
    }
}

function get_user_list() {
    global $connection;

    $sql = $connection->query("SELECT username, user_id FROM user");

    if (!$sql) {
        return $connection->connect_error;
    } else {
        $rows = array();

        while($row = $sql->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}

function get_number_of_users(){
    global $connection;

    $sql = $connection->query("SELECT COUNT(user_id) as 'id' FROM user");

    if(!$sql) {
        return $connection->connect_error;
    } else {
        $number = $sql->fetch_assoc();
    }
    return $number["id"];
}

function get_turn(){
    global $connection;

    $sql = $connection->query("SELECT turn FROM turn WHERE turn_id = 0");

    if(!$sql){
        return $connection->error;
    } else {
        $turn = $sql->fetch_assoc()["turn"];
    }

    return $turn;
}

function update_turn_in_db($turn){
    global $connection;

    $stmt = $connection->prepare("UPDATE turn SET turn=? WHERE turn_id = 0");
    $stmt->bind_param('i', $turn);
    $stmt->execute();

    if(!empty($stmt->error)){
        return $stmt->error;
    } else {
        return true;
    }
}

function update_conditions_from_user($user_id){
    //We will go through all the conditions of the hero, and substract them all with 1.
    global $connection;
    $errors =  array();

    $sql_get = $connection->query("SELECT ucd_id as 'id', condition_value as 'value' FROM user_condition_data WHERE user_id = '".$user_id."'");
    $conditions = array();
    $rows = array();
    while($row = $sql_get->fetch_array(MYSQLI_ASSOC)){
        $conditions["id"] = $row["id"];
        $conditions["value"] = $row["value"] - 1;
        $rows[] = $conditions;
    }

    foreach($rows as $condition){
        $value = $condition["value"];
        if($value <= 0){
            //If the value <= 0, it means that the condition is expired.
            $sql = $connection->prepare("DELETE FROM user_condition_data WHERE ucd_id = ?");
            $sql->bind_param('i', $condition["id"]);
            $sql->execute();
        } else{
            $sql = $connection->query("UPDATE user_condition_data SET condition_value='".$value."' WHERE ucd_id = '".$condition["id"]."'");
        }

        if(!$sql){
            $errors[] = $connection->error;
        }
    }

    //Check if everything went succesfull
    if(!empty($errors)){
        return $errors;
    } else {
        return true;
    }
}

function get_user_turn_list(){
    global $connection;

    $sql = $connection->query("SELECT ubd.user_id as 'id', ubd.basic_value as 'turn'
            FROM user_basic_data ubd
            INNER JOIN basic b ON ubd.basic_id = b.basic_id
            WHERE ubd.basic_id = '8'");

    if(!$sql){
        return false;
    } else {
        $rows = array();
        while($row = $sql->fetch_array(MYSQLI_ASSOC)){
            $rows[] = $row;
        }
    }
    return $rows;
}

function register_user($username, $password) {
    global $connection;

    $options = [
        'cost' => 10,
    ];

    $hash = password_hash($password, PASSWORD_BCRYPT, $options);

    $sql = $connection->query("INSERT INTO user (username, password, permission_type) VALUES ('$username', '$hash', 0)");

    $user_id = $connection->insert_id;
    $now = microtime(true);

    $timestamps = $connection->query("INSERT INTO timestamps (user_id, basic_timestamp, skill_timestamp, inventory_timestamp, condition_timestamp) VALUES ($user_id, $now, $now, $now, $now)");

    if (!$sql || !$timestamps) {
        return $connection->connect_error;
    } else {
        return true;
    }
}

function delete_user($user_id) {
    global $connection;

    $sql = $connection->query("DELETE FROM user WHERE user_id='$user_id'");

    if (!$sql) {
        return $connection->connect_error;
    } else {
        $num_rows = $connection->affected_rows;

        if ($num_rows >= 1) {
            return true;
        } else {
            return false;
        }
    }
}

function get_races() {
    global $connection;

    $sql = $connection->query("SELECT name, race_id FROM races");

    if(!$sql) {
        return $connection->error;
    } else {
        $races = array();

        while ($row = $sql->fetch_assoc()) {
            $races[] = $row;
        }
        return $races;
    }
}

function get_classes() {
    global $connection;

    $sql = $connection->query("SELECT name, class_id FROM classes");

    if(!$sql) {
        return $connection->error;
    } else {
        $classes = array();

        while ($row = $sql->fetch_assoc()) {
            $classes[] = $row;
        }
        return $classes;
    }
}

function update_user($user_id, $fields) {
    global $connection;

    $values = prepare_fields($fields);

    if ($connection->query("UPDATE user SET $values WHERE user_id=$user_id")) {
        return true;
    } else {
        return $connection->error;
    }
}

function prepare_fields ($fields) {
    $single_values = array();

    foreach ($fields as $field => $value) {
        if (gettype($value) === "integer" || gettype($field) === "double") {
            $single_values[] = $field . "=" . $value;
        } else {
            $single_values[] = $field . "='" . $value . "'";
        }
    }

    return implode (", ", $single_values);
}

function get_basic_data($user_id, $current_timestamp){
    //This function gets the basic data. This includes all the basic info of all the players
    global $connection;
    $basic_data = array();

    //Check the basic Timestamp to determine whether or not the basic info is needed.
    $sql = $connection->query("SELECT basic_timestamp as 'basic', condition_timestamp as 'condition', inventory_timestamp as 'inventory' FROM timestamps WHERE user_id = '".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $timestamps = $sql->fetch_assoc();
    }

    if(($timestamps["basic"] >= $current_timestamp) ||(($timestamps["condition"]) >= $current_timestamp) ||($timestamps["inventory"] >= $current_timestamp)){
        //Get the list of users
        $user_list = get_user_list();
        foreach($user_list as $user){
            $data = array();
            if($timestamps["basic"] >= $current_timestamp){
                $data["basic_data"] = get_user_basic_data($user["user_id"])["data"];
                if($data["basic_data"]["error"] != false)
                    return $data["basic_data"]["error"];
            }

            if($timestamps["condition"] >= $current_timestamp) {
                $data["condition_data"] = get_user_condition_data($user["user_id"])["data"];
                if($data["condition_data"]["error"] != false)
                    return $data["condition_data"]["error"];
            }

            if($timestamps["inventory"] >= $current_timestamp){
                $data["inventory_data"] = get_user_inventory_data($user["user_id"])["data"];
                if($data["inventory_data"]["error"] != false){
                    return $data["inventory_data"]["error"];
                }
            }

            //Fill the main array
            $rows = array();
                //Get the user ID
            $rows["user_id"] = $user["user_id"];
                //Get the associated username
            if(get_user_name($user["user_id"]) != false) $rows["username"] = get_user_name($user["user_id"]);
            else return false;
                //Determine if you are this user
            if($user_id === $user["user_id"]) $rows["is_you"] = true;
            else $rows["is_you"] = false;
                //Add data to the main array
            $rows["data"] = $data;

            //Fill the gathered data in the array
            $basic_data[] = $rows;
        }
    } else {
        //If no new data is found, return false;
        return false;
    }
    return $basic_data;
}

function get_user_basic_data($user_id){
    global $connection;
    $basic_data = array();

    $sql = $connection->query("SELECT ubd.basic_id as 'id', b.name as 'name', ubd.basic_value as 'value' FROM user_basic_data ubd INNER JOIN basic b ON b.basic_id = ubd.basic_id WHERE user_id = '" . $user_id . "'");

    if (!$sql) {
        $basic_data["error"] = $connection->error;
    } else {
        $basic_data["error"] = false;
        $rows = array();    //Declare empty array to avoid problems

        while ($row = $sql->fetch_array(MYSQLI_ASSOC)) {
            $rows[] = $row;
        }
        $basic_data["data"] = $rows;
    }

    return $basic_data;
}

function get_monster_data(){
    global $connection;
    $monster_data = array();

    $sql = $connection->query("SELECT monster_id as 'monster_id', name as 'name', multiplier as 'multiplier' FROM monsters");

    if(!$sql){
        $monster_data["errors"] = true;
        return $connection->error;
    } else {
        $monster_data["errors"] = false;
        $rows = array();
        while($row = $sql->fetch_array(MYSQLI_ASSOC)){
            $rows[] = $row;
        }
        $monster_data["data"] = $rows;
    }
    return $monster_data;
}

function get_user_skill_data($user_id){
    //This function gets the skill data of the user
    global $connection;
    $skill_data = array();

    $sql = $connection->query("SELECT usd.skill_value as 'value', s.name as 'name', s.type as 'type', s.subtype as 'subtype', s.levels as 'max_levels', s.level_advantages as 'advantages'
            FROM user_skill_data usd
            INNER JOIN skills s ON usd.skill_id = s.skill_id
            WHERE usd.user_id ='".$user_id."'");

    if(!$sql){
        $skill_data["errors"] = true;
        return $connection->error;
    } else {
        $skill_data["errors"] = false;
        $rows = array();
        while($row = $sql->fetch_array(MYSQLI_ASSOC)){
            $rows[] = $row;
        }
        $skill_data["data"] = $rows;
    }
    return $skill_data;
}

function get_user_condition_data($user_id){
    global $connection;
    $condition_data = array();

    $sql = $connection->query("SELECT ucd.condition_id, ucd.condition_value as 'turns', a.advantage_value as 'damage', b.name as 'damage_on', a.basic_id as 'damage_on_id', c.name as 'condition' FROM user_condition_data ucd
      INNER JOIN advantages a ON ucd.condition_id = a.condition_id
      INNER JOIN basic b ON a.basic_id = b.basic_id
      INNER JOIN `condition` c ON ucd.condition_id = c.condition_id
      WHERE ucd.user_id = '" . $user_id . "'");

    if (!$sql) {
        $condition_data["error"] = $connection->error;
    } else {
        $condition_data["error"] = false;
        $rows = array();    //Declare empty array to avoid problems

        while ($row = $sql->fetch_array(MYSQLI_ASSOC)) {
            if ($row["turns"] != 0) $rows[] = $row;
            //Delete any 'finished' conditions if there were accidentally some in the database.
        }
        $condition_data["data"] = $rows;
    }
    return $condition_data;
}

function get_user_inventory_data($user_id){
    global $connection;
    $inventory_data = array();

    $sql = $connection->query("SELECT uid.item_id, i.name as 'name', uid.item_value as 'count', t.name as 'type', i.condition as 'condition_id'
        FROM user_inventory_data uid
        INNER JOIN inventory i ON uid.item_id = i.item_id
        INNER JOIN types t ON i.type = t.type_id
        WHERE uid.user_id = '" . $user_id . "'");

    if (!$sql) {
        return $connection->error;
    } else {
        $rows = array();
        $inventory = array();
        while ($row = $sql->fetch_array(MYSQLI_ASSOC)){
            //Get the different conditions for the condition ID.
            $conditions = get_conditions_by_id($row["condition_id"]);
                if($conditions["error"] != false)
                    $inventory_data["error"] = $conditions["error"];

            //Fill the array
            $rows["item_id"] = $row["item_id"];
            $rows["name"] = $row["name"];
            $rows["count"] = $row["count"];
            $rows["type"] = $row["type"];
            $rows["conditions"] = $conditions["data"];

            $inventory[] = $rows;
        }
        $inventory_data["data"] = $inventory;
    }
    return $inventory_data;
}

function get_user_name($user_id){
    global $connection;

    $sql = $connection->query("SELECT username FROM user WHERE user_id = '".$user_id."'");
    $user_data = $sql->fetch_array(MYSQLI_ASSOC);
    $username = $user_data["username"];

    if(!empty($username)) return $username;
    else return "username not resolved";
}

function get_conditions_by_id($condition_id){
    global $connection;
    $conditions = array();

    $sql = $connection->query("SELECT c.condition_id, c.duration as 'turns', a.advantage_value as 'damage', b.name as 'damage_on', b.basic_id as 'damage_on_id', c.name as 'condition'
      FROM `condition` c
      INNER JOIN advantages a ON c.condition_id = a.condition_id
      INNER JOIN basic b ON a.basic_id = b.basic_id
      WHERE c.condition_id = '" . $condition_id . "'");

    if (!$sql) {
        $conditions["error"] = $connection->error;
    } else {
        $rows = array();
        $conditions["error"] = false;
        while ($row = $sql->fetch_array(MYSQLI_ASSOC)){
            $rows[] = $row;
        }
        $conditions["data"] = $rows;
    }

    return $conditions;
}

function get_levelling_data($user_id, $current_timestamp){
    //This function gets the levelling data. This includes all the levelling info of the hero, and the available monsters.
    global $connection;
    $levelling_data = array();

    //Check the basic and skill timestamp, needed for this data.
    $sql = $connection->query("SELECT basic_timestamp as 'basic', skill_timestamp as 'skill' FROM timestamps WHERE user_id='".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $timestamps = $sql->fetch_assoc();
    }

    if(($timestamps["basic"] >= $current_timestamp) || ($timestamps["skill"] >= $current_timestamp)){
        //Check the basic timestamp
        if($timestamps["basic"] >= $current_timestamp){
            $raw_basic_data = get_user_basic_data($user_id);

            if($raw_basic_data["error"] != "false"){
                $basic_data = $raw_basic_data["data"];
            } else {
                return false;
            }
        }

        //Check the skill timestamp
        if($timestamps["skill"] >= $current_timestamp){
            $monsters = get_monster_data();
            $skill_data = get_user_skill_data($user_id);

            if($monsters["errors"] != false){
                return false;
            }
            if($skill_data["errors"] != false){
                return false;
            }
        }

        //Fill the main array
            if(!empty($basic_data)){
                //Get the users_id
                    $levelling_data["user_id"] = $user_id;
                //Get the users current EXP
                foreach($basic_data as $basic){
                    if($basic["id"] === "11"){
                        $levelling_data["user_exp"] = $basic["value"];
                    }
                    if($basic["id"] === "12"){
                        $levelling_data["user_exp_multiplier"] = $basic["value"];
                    }
                }
            } else {
                return false;
            }
            //Get the monsters
            $levelling_data["monster_data"] = $monsters["data"];
            //Get the skill data
            $levelling_data["skill_data"] = $skill_data["data"];
    } else {
        //If no new data is found, return false;
        return false;
    }
    return $levelling_data;
}

function get_skill_data($user_id, $current_timestamp){

}

function get_general_data($user_id, $current_timestamp){
    //This function gets the general data. This includes all the general info such as current user and the turn logic.
    global $connection;
    $general_data = array();

    //Check the timestamps that are active for general data.
    $sql = $connection->query("SELECT basic_timestamp as 'basic' FROM timestamps WHERE user_id='".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $timestamps = $sql->fetch_assoc();
    }

    if($timestamps["basic"] >= $current_timestamp){
        //Get current user data
        $fields = array("user_id", "username");
        $current_user = user_data($user_id, $fields);
        $general_data["current_user"] = $current_user;

        //Get turns until user has his turn.
        $turns_left = 0;
        $current_turn = get_turn();
        $user_turn_list = get_user_turn_list();
        if($user_turn_list != false){
            foreach($user_turn_list as $user_turn_data){
                if($user_turn_data["id"] === $user_id){
                    $user_turn = $user_turn_data["turn"];
                }
            }
            if(!empty($user_turn)){
                if($user_turn < $current_turn){
                    $max_turn = get_number_of_users();
                    $dist_to_max = $max_turn - $current_turn;
                    $turns_left = $user_turn + $dist_to_max + 1;    //+1 because the 0th step must count too.
                } elseif($user_turn > $current_turn){
                    $turns_left = $user_turn - $current_turn;
                } else {
                    $turns_left = 0;
                }
                $general_data["turns_left"] = $turns_left;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        //if no new data is found, return false;
        return false;
    }
    return $general_data;
}