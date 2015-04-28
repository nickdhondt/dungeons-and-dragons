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
    $sql = $connection->query("SELECT basic_timestamp as 'basic', condition_timestamp as 'condition' FROM timestamps WHERE user_id = '".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $timestamps = $sql->fetch_assoc();
    }

    if(($timestamps["basic"] >= $current_timestamp) ||(($timestamps["condition"]) >= $current_timestamp)){
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

    $sql = $connection->query("SELECT b.name as 'name', ubd.basic_value as 'value' FROM user_basic_data ubd INNER JOIN basic b ON b.basic_id = ubd.basic_id WHERE user_id = '" . $user_id . "'");

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

function get_user_condition_data($user_id){
    global $connection;
    $condition_data = array();

    $sql = $connection->query("SELECT ucd.condition_id, ucd.condition_value as 'turns', a.advantage_value as 'damage', b.name as 'damage on', c.name as 'condition' FROM user_condition_data ucd
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
            $rows[] = $row;
        }
        $condition_data["data"] = $rows;
    }
    return $condition_data;
}

function get_user_name($user_id){
    global $connection;

    $sql = $connection->query("SELECT username FROM user WHERE user_id = '".$user_id."'");
    $user_data = $sql->fetch_array(MYSQLI_ASSOC);
    $username = $user_data["username"];

    if(!empty($username)) return $username;
    else return "username not resolved";
}

function get_levelling_data($user_id, $current_timestamp){

}

function get_skill_data($user_id, $current_timestamp){

}

function get_general_data($user_id, $current_timestamp){

}