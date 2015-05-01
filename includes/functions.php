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

function delete_condition($ucd_id){
    global $connection;

    $sql = $connection->prepare("DELETE FROM user_condition_data WHERE ucd_id = ?");
    $sql->bind_param('i', $ucd_id);
    $sql->execute();
}

function update_conditions_from_user($user_id){
    //We will go through all the conditions of the hero, and substract them all with 1.
    global $connection;
    $errors =  array();

    $sql_get = $connection->query("SELECT ucd_id as 'id', condition_value as 'value', condition_id as 'cid' FROM user_condition_data WHERE user_id = '".$user_id."'");
    $conditions = array();
    $rows = array();
    while($row = $sql_get->fetch_array(MYSQLI_ASSOC)){
        $conditions["id"] = $row["id"];
        $conditions["value"] = $row["value"] - 1;
        $conditions["cid"] = $row["cid"];
        $rows[] = $conditions;
    }

    foreach($rows as $condition){
        $value = $condition["value"];
        if($value <= 0){
            //If the value <= 0, it means that the condition is expired.
            delete_condition($condition["id"]);

            //When the condition is deleted, restore the condition
            $success = validate_condition($user_id, $condition["cid"], true);

            //Controle
            if(!$success){
                $errors[] = $success;
            }
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

    $sql = $connection->query("SELECT ubd.user_id as 'id', ubd.basic_value as 'turn', username as 'name'
            FROM user_basic_data ubd
            INNER JOIN basic b ON ubd.basic_id = b.basic_id
            INNER JOIN user ON ubd.user_id = user.user_id
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

function alter_timestamps($user_id, $basic, $skill, $inventory, $condition){
    //This function alters the timestamps in the database
    global $connection;

    //THE SELECT QUERY
    $sql = $connection->query("SELECT basic_timestamp as 'basic', skill_timestamp as 'skill', inventory_timestamp as 'inventory',
        condition_timestamp as 'condition' FROM timestamps WHERE user_id='".$user_id."'");

    $rows = array();
    while($row = $sql->fetch_array(MYSQLI_ASSOC))
    {
        $rows[] = $row;
    }

    //Set the default values:
    $nbasic = $rows[0]["basic"];
    $nskill = $rows[0]["skill"];
    $ninventory = $rows[0]["inventory"];
    $ncondition = $rows[0]["condition"];

    if(!empty($basic)) $nbasic = $basic;
    if(!empty($skill)) $nskill = $skill;
    if(!empty($inventory)) $ninventory = $inventory;
    if(!empty($condition)) $ncondition = $condition;

    //THE UPDATE QUERY
    $timestamps = $connection->query("UPDATE `dungeons_and_dragons`.`timestamps` SET `basic_timestamp` = '".$nbasic."', `skill_timestamp` = '".$nskill."', `inventory_timestamp` = '".$ninventory."', `condition_timestamp` = '".$ncondition."' WHERE `timestamps`.`user_id` = 1");

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

            $success = clean_user_crumbles($user_id);

            if($success != true){
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }
    }
}

function clean_user_crumbles($user_id){
    global $connection;

    $sql1 = $connection->query("DELETE FROM timestamps WHERE user_id='$user_id'");
    $sql2 = $connection->query("DELETE FROM user_basic_data WHERE user_id='$user_id'");
    $sql3 = $connection->query("DELETE FROM user_condition_data WHERE user_id='$user_id'");
    $sql4 = $connection->query("DELETE FROM user_inventory_data WHERE user_id='$user_id'");
    $sql5 = $connection->query("DELETE FROM user_skill_data WHERE user_id='$user_id'");

    if ((!$sql1) || (!$sql2) || (!$sql3) || (!$sql4) || (!$sql5)) {
        return $connection->connect_error;
    } else {
        return true;
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
        $skill_data["errors"] = $connection->error;
        return $skill_data;
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

function get_tabtwo_data($user_id, $current_timestamp){
    //This function makes the array for the second tab.
    $exp_data = array();
    $exp_data["levelling"] = get_levelling_data($user_id, $current_timestamp);
    $exp_data["skills"] = get_skill_data($user_id, $current_timestamp);

    $new_data = false;
    foreach($exp_data as $pod){
        if($pod != false) $new_data = true;
    }

    if($new_data == true) return $exp_data;
    else return false;
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

    //Check the basic timestamp
    if($timestamps["basic"] >= $current_timestamp){
        //Get the basic_user's data
        $raw_basic_data = get_user_basic_data($user_id);

        if($raw_basic_data["error"] != "false"){
            $basic_data = $raw_basic_data["data"];
        } else {
            return false;
        }

        //Get the monster data
        $monsters = get_monster_data();

        if($monsters["errors"] != false){
            return false;
        }
    }

    //Fill the main array
    if(!empty($monsters)){
        //Get the monsters
        $levelling_data["monster_data"] = $monsters["data"];
    } else {
        //If no new data is found, return false;
        return false;
    }

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
        //If no new data is found, return false;
        return false;
    }

    return $levelling_data;
}

function get_skill_data($user_id, $current_timestamp){
    //This function gets the skill data. This includes all the skills and their advantages.
    global $connection;
    $skill_data = array();

    //Check the skill timestamp to determine whether or not the skill info is up-to-date
    $sql = $connection->query("SELECT basic_timestamp as 'basic', skill_timestamp as 'skill' FROM timestamps WHERE user_id='".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $timestamps = $sql->fetch_assoc();
    }

    if(($timestamps["basic"] >= $current_timestamp) || ($timestamps["skill"] >= $current_timestamp)){
        //Get the basic data
        $sql_ap = $connection->query("SELECT ubd.basic_value as 'value', b.name as 'name' FROM user_basic_data ubd
            INNER JOIN basic b ON b.basic_id = ubd.basic_id
            WHERE (ubd.basic_id = 5) AND (ubd.user_id = '".$user_id."')");

        $row = $sql_ap->fetch_assoc();
        $actionpoints = $row["value"];

        //Get the skill data
        $sdd = get_user_skill_data($user_id);
        if($sdd["errors"] != false){
            return $sdd; //This contains the error
        } else {
            //The skill_data array contains: "value, name, type, subtype, max_levels, advantages";

            foreach($sdd["data"] as $skill_data_data){
                //Fetch the level_data
                $level_data = array();
                $advantages = explode(';', $skill_data_data["advantages"]);
                for($i=0;$i<=count($advantages);$i++){
                    $advantage_data = array();
                    $advantage = $advantages[$i];
                    $advantage_data["nr"] = $i + 1;
                    if($skill_data_data["value"] >= $i){
                        $advantage_data["is_achieved"] = true;
                    } else {
                        $advantage_data["is_achieved"] = false;
                    }
                    $advantage_data["advantage"] = $advantage;
                    $level_data[] = $advantage_data;
                }

                //Make skill_data["data"] array
                $subdata = array();
                $subdata["name"] = $skill_data_data["name"];
                $subdata["type"] = $skill_data_data["type"];
                $subdata["subtype"] = $skill_data_data["subtype"];
                $subdata["number_of_levels"] = $skill_data_data["max_levels"];
                $subdata["levels"] = $level_data;
                $data[] = $subdata;
            }
        }
    } else {
        //If no new data is found, return false;
        return false;
    }

    //Fill the main array
    if(!empty($skill_data["errors"])){
        return $skill_data["errors"];
    } else {
        $skill_data["user_id"] = $user_id;
        $skill_data["actionpoints"] = $actionpoints;
        $skill_data["data"] = $data;
    }
    return $skill_data;
}

function list_basic_skillitems() {
    global $connection;

    $sql = $connection->query("SELECT basic_id, name FROM basic WHERE basic_id=0 OR basic_id=1 OR basic_id=2 OR basic_id=3 OR basic_id=4 OR basic_id=5 OR basic_id=6 OR basic_id=7 OR basic_id=9");

    if (!$sql) {
        return $connection->error;
    } else {
        $basics = array();

        while($row = $sql->fetch_assoc()) {
            $basics[] = $row;
        }

        return $basics;
    }
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

function list_basic_conditions() {
    global $connection;

    $sql = $connection->query("SELECT condition_id, name FROM `condition`");

    if (!$sql) {
        return $connection->error;
    } else {
        $conditions = array();

        while($row = $sql->fetch_assoc()) {
            $conditions[] = $row;
        }

        return $conditions;
    }
}

function list_inventory_items() {
    global $connection;

    $sql = $connection->query("SELECT item_id, name FROM inventory");

    if (!$sql) {
        return $connection->error;
    } else {
        $conditions = array();

        while ($row = $sql->fetch_assoc()) {
            $conditions[] = $row;
        }

        return $conditions;
    }
}

function find_basic_id_for_basic_name($basic_name) {
    //This function will return the id for a given basic name.
    global $connection;
    $basic_id = 0;

    $sql = $connection->query("SELECT basic_id FROM basic WHERE name ='".$basic_name."'");
    $basic_id = $sql->fetch_array(MYSQLI_BOTH)[0];

    if(count($basic_id) === 1){
        return $basic_id;
    } else {
        return "10";    //This will ensure that, IF THERE WAS A FAULT, the data would be displayed to the user, which can be used for debugging.
    }
}

function get_maximum_basic_values($user_id){
    //This function will get the race and class from the user and calculate his maximum basic values
    global $connection;
    $basic_values = array();
    $basic_values["error"] = false;
    $basic_values["user_id"] = $user_id;

    //Get the user's race and class
    $fields = array("user_id, race, class");
    $user_data = user_data($user_id, $fields);

    //Get the maximum data for the race
    $sqlrace = $connection->query("SELECT attack, defence, walking, mana, health FROM races WHERE race_id='".$user_data["race"]."'");

    if(!$sqlrace){
        $basic_values["error"] = true;
        return $connection->error;
    } else {
        $racedata = array();
        while($row = $sqlrace->fetch_array(MYSQLI_ASSOC)){
            $r[] = $row;
        }
    }

    //Get the maximum data for the class
    $sqlclass = $connection->query("SELECT attack, defence, walking, mana, health FROM classes WHERE class_id='".$user_data["class"]."'");

    if(!$sqlclass){
        $basic_values["error"] = true;
        return $connection->error;
    } else {
        $classdata = array();
        while($rows = $sqlclass->fetch_array(MYSQLI_ASSOC)){
            $c[] = $rows;
        }
    }

    //Calculate basic values
    $attack = intval($c[0]["attack"]) + intval($r[0]["attack"]);
    $defence = intval($c[0]["defence"]) + intval($r[0]["defence"]);
    $walking =  intval($c[0]["walking"]) + intval($r[0]["walking"]);
    $mana = intval($c[0]["mana"]) + intval($r[0]["mana"]);
    $health =  intval($c[0]["health"]) + intval($r[0]["health"]);

    //Write calculations to array
    $basic_values[] = array("id"=>find_basic_id_for_basic_name("attack"), "name"=>"attack", "max"=>$attack);
    $basic_values[] = array("id"=>find_basic_id_for_basic_name("defence"), "name"=>"defence", "max"=>$defence);
    $basic_values[] = array("id"=>find_basic_id_for_basic_name("walking"), "name"=>"walking", "max"=>$walking);
    $basic_values[] = array("id"=>find_basic_id_for_basic_name("mana"), "name"=>"mana", "max"=>$mana);
    $basic_values[] = array("id"=>find_basic_id_for_basic_name("health"), "name"=>"health", "max"=>10);

    return $basic_values;
}

function initialize_user_basic_data($user_id){
    //This function will initialize the basic user data
    global $connection;
    $response = array();

    //Get all the basic data.
    $sqldata = $connection->query("SELECT basic_id FROM basic");

    if (!$sqldata) {
        $response["errors"] = $connection->error;
        return $response;
    } else {
        $response["errors"] = false;
    }

    $basics = array();
    while($row = $sqldata->fetch_array(MYSQLI_ASSOC)){
        $basics[] = $row["basic_id"];
    }

    //Loop through all the basics and write the default value for the user
    for($i=0; $i<=count($basics); $i++){
        $max_basics = get_maximum_basic_values($user_id);
        $value = 0;
        foreach($max_basics as $max_basic){
            if(isset($max_basic["id"])) {
                //The ID Must be set
                if($max_basic["id"] == $i) {
                    //The first five basics get their max value
                    $value = $max_basic["max"];
                }
            }
            if($i === 8){
                //The turn must be the ...th user.
                $value = get_number_of_users();
            }
            if($i === 10){
                //To start, there are no user messages
                $value = "";
            }
            if($i === 12){
                //The multiplier is traditionally 1.
                $value = 1;
            }
        }

        $stmt = $connection->prepare("INSERT INTO user_basic_data (user_id, basic_id, basic_value) VALUES(?,?,?)");
        $stmt->bind_param('iii', $user_id, $i, $value);
        $stmt->execute();

        if(!$stmt){
            $response["errors"] = $connection->error;
        } else {
            $response["errors"] = false;
        }
    }

    //Update the timestamps for the basics.
    $now = microtime(true);
    $success = alter_timestamps($user_id, $now, "", "", "");

    if($success != true){
        $response["errors"] = $success;
    } else {
        $response["errors"] = false;
    }

    return $response;
}

function get_shop_data($user_id, $current_timestamp){
    //This function gets the shop data. This includes the check for resources and all the inventory items.
    global $connection;
    $shop_data = array();

    //Check the needed timestamps for the $shop_data
        //What is needed? Money -> basic_array, Price -> inventory, skill_reqs -> skill
    $sql = $connection->query("SELECT basic_timestamp as 'basic', skill_timestamp as 'skill',
            inventory_timestamp as 'inventory' FROM timestamps WHERE user_id = '".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $timestamps = $sql->fetch_assoc();
    }

    if(($timestamps["basic"] >= $current_timestamp) && ($timestamps["skill"] >= $current_timestamp) & ($timestamps["inventory"]) >= $current_timestamp){
        //All of the above timestamps are needed for the calculations.
        //Get all the shop items
        $shop_items = get_shop_items();
        if(!empty($shop_items)){
            foreach($shop_items as $shop_item){
                //Gather the $price_data
                $price_data = array();
                $price_values = explode(';', $shop_item["price_value"]);
                $price_items = explode(';', $shop_item["price_item"]);
                for($i=0;$i<count($price_items); $i++){
                    $pd = array();
                    $pd["value"] = $price_values[$i];
                    $pd["item"] = $price_items[$i];
                    $price_data[] = $pd;
                }

                //Gather the $skill_data
                $skill_data = array();
                $skill_values = explode(';', $shop_item["skill_value"]);
                $skill_item = explode(';', $shop_item["skill_requirement"]);
                for($j=0;$j<count($skill_item);$j++){
                    $sd = array();
                    $sd["value"] = $skill_item[$j];
                    $sd["name"] = $skill_item[$j];
                    $skill_data[] = $sd;
                }

                //Check if the upgrade is present in the user's inventory
                $upgrade_present = false;
                if($shop_item["upgrade"] == 0) $upgrade_present = true; //If the items hasn't got an upgrade, display it.
                else {
                    //Display the item ONLY if the user has got the previous version.
                    $sql = $connection->query("SELECT item_id as 'id' FROM user_inventory_data WHERE item_id='".$shop_item["upgrade"]."'");
                    $row = $sql->fetch_assoc();
                    if(count($row) < 1){
                        //The user hasn't got a signle item with the previous upgrade ID.
                        $upgrade_present = false;
                    } else {
                        //If there are multiple ID's found, the user can upgrade for sure.
                        $upgrade_present = true;
                    }
                }

                //Fill the main array
                $item_data = array();
                $item_data["item_id"] = $shop_item["item_id"];
                $item_data["can_buy"] = check_item_requirements($user_id, $item_data["item_id"], $upgrade_present);
                if($item_data["can_buy"]["errors"] != false) return $item_data["can_buy"]["errors"];
                $item_data["item_data"] = get_item_data($item_data["item_id"]);
                $item_data["price_data"] = $price_data;
                $item_data["skill_data"] = $skill_data;
                $item_data["upgrade"] = $shop_item["upgrade"];

                $shop_data[] = $item_data;
            }
        } else {
            //If no data is found, return false;
            return false;
        }
    } else {
        //if no new data if found, return false;
        return false;
    }
    return $shop_data;
}

function check_item_requirements($user_id, $item_id, $upgrade_present){
    //This function will check whether the conditions for buying an item are fulfilled.
    //["errors"] must be false if everything is owkay
    return true;
}

function get_shop_items(){
    //This function will get all the shop items.
    global $connection;
    $shop_items = array();

    $sql = $connection->query("SELECT * FROM shop");

    if(!$sql){
        return false;
    } else {
        while($row = $sql->fetch_array(MYSQLI_ASSOC)){
            $shop_items[] = $row;
        }
    }
    return $shop_items;
}

function get_item_data($item_id){
    //This function will get the name, type and conditions of an item.
    global $connection;
    $item_data = array();

    $sql = $connection->query("SELECT name, type, `condition` as 'id' FROM inventory WHERE item_id='".$item_id."'");

    if(!$sql){
        $item_data["errors"] = $connection->error;
        return $item_data;
    } else {
        $row = $sql->fetch_array(MYSQLI_ASSOC);
        $item_data["name"] = $row["name"];
        $item_data["type"] = $row["type"];
        $condition_id = $row["id"];
        $conditions = get_conditions_by_id($condition_id);

        if($conditions["error"] != false){
            $item_data["errors"] = $conditions["error"];
        } else {
            $item_data["condition"] = $conditions["data"];
        }
    }
    return $item_data;
}

function write_to_user_basic($user_id, $basic_id, $value){
    //This function will write the changes to the database.
    global $connection;

    //Check the current value
    $sql = $connection->query("SELECT basic_value, ubd_id as 'id' FROM user_basic_data WHERE (user_id='".$user_id."') AND (basic_id='".$basic_id."')");
    $row = $sql->fetch_assoc();
    $current_value = $row["basic_value"];
    $ubd = $row["id"];
    $new_value = $current_value + $value;

    //Controle op current value
    if($new_value <= 0){
        $new_value = 0;
    }

    //EXP mag niet boven de 1.000.000 gaan
    if(($basic_id == 11) && ($new_value >= 1000000)){
        $new_value = 1000000;        //EXP mag niet boven de 1.000.000 gaan.
    }

    //Write the given value
    $stmt = $connection->prepare("UPDATE `dungeons_and_dragons`.`user_basic_data` SET `basic_value` = ? WHERE `user_basic_data`.`ubd_id` = ?");
    $stmt->bind_param('ii', $new_value, $ubd);
    $stmt->execute();

    //After writing, update the timestamps
    $now = microtime(true);
    $timestamps = alter_timestamps($user_id, $now, "", "", "");

    if(isset($timestamps["errors"])){
        if($timestamps["errors"] != false){
            return "false";
        }
    }

    if(!$stmt){
        return "false";
    } else {
        return "true";
    }
}

function add_condition($user_id, $condition_id){
    //This function will add a requested condition to the users condition.
    global $connection;
    $condition_data = array();
    $condition_data["error"] = false;

    //Select condition
    $sql = $connection->query("SELECT duration FROM `condition` WHERE condition_id='".$condition_id."'");

    if(!$sql){
        $condition_data["error"] = $connection->error;
    } else{
        $rows = array();
        while($row = $sql->fetch_array(MYSQLI_ASSOC)){
            $rows[] = $row;
        }

        //Check if the array has the expected lentgh
        if(count($rows) <= 0){
            $condition_data["error"] = "Er werd meer dan 1 ID gevonden!!! SATAN IS AAN HET WERK. HIDE YOUR WIFE, HIDE YOUR KIDS!";
        } else {
            $condition_value = $rows[0]["duration"];
        }
    }

    //Divide the conditions in two different sorts
    if($condition_value == 0){
        $success = validate_condition($user_id, $condition_id);
    } else {
        $stmt = $connection->prepare("INSERT INTO user_condition_data(user_id, condition_id, condition_value) VALUES (?,?,?)");
        $stmt->bind_param('iii',$user_id, $condition_id, $condition_value);
        $stmt->execute();

        if(!$stmt){
            $condition_data["error"] = $connection->error;
        } else {
            //The condition has been added to the database
            $success = validate_condition($user_id, $condition_id);
        }

        if(!$success){
            $condition_data["error"] = $success;
        }
    }

    //Alter Timestamps
    $now = microtime(true);
    $success = alter_timestamps($user_id, $now, "", "", "");

    if(!$success){
        $condition_data["error"] = $success;
    }

    return $condition_data;
}

function validate_condition($user_id, $condition_id, $devalidate = false){
    //This will add/substract the values when assigning a condition.
    global $connection;
    $validate = array();

    $sql = $connection->query("SELECT advantage_value as 'value', basic_id as 'id', advantage_id as 'aid' FROM advantages WHERE condition_id='".$condition_id."'");

    if(!$sql){
        $validate["error"] = $connection->error;
    } else {
        $rows = array();
        while($row = $sql->fetch_array(MYSQLI_ASSOC)){
            $rows[] = $row;
        }
    }

    foreach($rows as $advantage){
        //Get the value info.
        $id = $advantage["id"];
        $change = $advantage["value"];

        //Get the current values
        $sql = $connection->query("SELECT basic_value FROM user_basic_data WHERE (basic_id='".$id."') AND (user_id='".$user_id."')");
        $rows = $sql->fetch_assoc();
        $current_value = $rows["basic_value"];

        //Calculate the new value
        if($devalidate === false){
            $new_value = $current_value + $change;
        } else {
            $new_value = $current_value - $change;
        }

        //Get the maximum value possible
        $maximum_basic_values = get_maximum_basic_values($user_id);
        foreach($maximum_basic_values as $maximum_basic_value){
            if(isset($max_basic["id"])) {
                //The ID Must be set
                if($maximum_basic_value["id"] === $id){
                    $max_value = $maximum_basic_value["max"];
                    $min_value = 0;
                }
            }
        }

        if((isset($max_value)) && (isset($min_value))){
            //The code is safe
            if($new_value > $max_value){
                $new_value = $max_value;
            }
            if($new_value < $min_value){
                $new_value = $min_value;
            }

            //Write the new value to the database
            $stmt = $connection->prepare("UPDATE `dungeons_and_dragons`.`user_basic_data` SET `basic_value` = '?' WHERE (`user_basic_data`.`basic_id`='".$id."') AND (`user_basic_data`.`user_id`='".$user_id."')");
            $stmt->prepare('i', $new_value);
            $stmt->execute();

            //update the timestamp
            $now = microtime(true);
            $success = alter_timestamps($user_id, $now, "", "", "");

            if(!$success){
                $validate["error"] = $success;
            }
        }
    }

    if(empty($validate["error"])) $validate = true;
    return $validate;
}