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

    if (!$sql) {
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

function get_basic_data_user($user_id, $current_timestamp){
    //This function gets the basic data for a user.
    //Basic data is described in the database-table "Basic"
    global $connection;

    //Check the basic Timestamp to determine whether or not the basic info is needed.
    $sql = $connection->query("SELECT basic_timestamp FROM timestamps WHERE user_id = '".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $basic_timestamp = $sql->fetch_assoc();
    }

    if($basic_timestamp["basic_timestamp"] >= $current_timestamp) {
        //This code is gathers the new data.
        $sql = $connection->query("SELECT b.name as 'name', ubd.basic_value as 'value' FROM user_basic_data ubd INNER JOIN basic b ON b.basic_id = ubd.basic_id WHERE user_id = '".$user_id."'");

        if(!$sql){
            return $connection->error;
        } else {
            $rows = array();    //Declare empty array to avoid problems
            while($row = $sql->fetch_array(MYSQLI_ASSOC))
            {
                $rows[] = $row;
            }
            return $rows;   //This array contains the name and the value of the basic information.
        }

    } else {
        //If no new data is found, this function returns false
        return false;
    }
}

function get_basic_data_users($current_timestamp){
    //This function will get the basic data for all the listed users.
    //The architecture is: Array(1=>(user_id,username,basic_data), 2=>...);
    $users = get_user_list();

    $basic_data_users = array();
    foreach($users as $user){
        $username = $user["username"];
        $user_id = $user["user_id"];

        $basic_data_user = get_basic_data_user($user_id, $current_timestamp);

        if ($basic_data_user != false) {

            $basic_data_users_entry["user_id"] = $user_id;
            $basic_data_users_entry["username"] = $username;
            $basic_data_users_entry["basic_data"] = $basic_data_user;

            $basic_data_users[] = $basic_data_users_entry;

        }
    }

    return $basic_data_users;
}

function get_condition_data_user($user_id, $current_timestamp){
    //This function gets the condition data for a user.
        //Condition data is described in the database-table "condition".
    global $connection;

    //Check the condition timestamp to determine wheter or not the condition info is needed.
    $sql = $connection->query("SELECT condition_timestamp FROM timestamps WHERE user_id = '".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $condition_timestamp = $sql->fetch_assoc();
    }

    if($condition_timestamp["condition_timestamp"] >= $current_timestamp){
        $sql = $connection->query("SELECT ucd.condition_id, a.basic_id, ucd.condition_value as 'turns', a.advantage_value as 'damage', b.name as 'damage on', c.name as 'condition' FROM user_condition_data ucd
        INNER JOIN advantages a ON ucd.condition_id = a.condition_id
        INNER JOIN basic b ON a.basic_id = b.basic_id
        INNER JOIN `condition` c ON ucd.condition_id = c.condition_id
        WHERE ucd.user_id = '".$user_id."'");

        if(!$sql){
            return $connection->error;
        } else {
            $rows = array();
            while($row = $sql->fetch_array(MYSQLI_ASSOC)){
                if($row["turns"] != 0) $rows[] = $row;
                    //Delete any 'finished' conditions if there were accidentally some in the database.
                    //These are already executed when written to the database, so they are discarted without any action.
            }

            //Set the ID on the rows:
            $condition_data = array();
            $condition_data["user_id"] = $user_id;
            $condition_data["condition_data"] = $rows;

            return $condition_data;   //This array contains the condition_id, the turns left for the condition, the damage, the object were there is damage on and the name of the condition.
        }
    } else {
        //if no new data is found, this function returns false
        return false;
    }
    //This function returns an array containing:
        //"condition_id, basic_id, turns left, damage, damage_on, condition"-values.
}

function get_inventory_data_user($user_id, $current_timestamp){
    //This function gets the inventory data for a user.
        //Inventory data is described in the database-table "inventory".
    global $connection;

    //Check the basic Timestamp to determine whether or not the inventory info is up-to-date.
    $sql=$connection->query("SELECT inventory_timestamp FROM timestamps WHERE user_id = '".$user_id."'");

    if(!$sql){
        return $connection->error;
    } else {
        $inventory_timestamp = $sql->fetch_assoc();
    }

    //Get the inventory Data
    if($inventory_timestamp["inventory_timestamp"] >= $current_timestamp){
        $sql = $connection->query("SELECT uid.item_id, uid.item_value as 'item_count', i.name as 'name', t.name as 'type', i.condition as 'condition'
        FROM user_inventory_data uid INNER JOIN inventory i ON uid.item_id = i.item_id
        INNER JOIN types t ON i.type = t.type_id WHERE uid.user_id = '".$user_id."'");

        if(!$sql){
            return $connection->error;
        } else {
            $rows = array();
            while($row = $sql->fetch_array(MYSQLI_ASSOC)){
                //Get the condition data
                $condition_id = $row["condition"];
                $conditions = get_condition_data_from_id($condition_id);

                //Check if the condition data is fetched errorless.
                if($conditions["error"] == true){
                    return $conditions["error_data"];
                }

                //Add all data to the $rows
                $rows["item_id"] = $row["item_id"];
                $rows["item_count"] = $row["item_count"];
                $rows["name"] = $row["name"];
                $rows["type"] = $row["type"];
                $rows["condition_id"] = $row["condition"];
                $rows["condition_data"] = $conditions["condition_data"];

            }

            $inventory_data = array();
            $inventory_data["user_id"] = $user_id;
            $inventory_data["inventory_data"] = $rows;

            return $inventory_data; //This array contains the item id, the number of items, the name of the item, the type of the item and the condition_id of the item.
        }
    } else {
        //If no new data is found, this function returns false
        return false;
    }
}

function get_condition_data_from_id($condition_id){
    //This function will get the conditions that are given in the $condition_id_array.
        //Conditions are described in the table 'conditions' and their advantages are described in 'advantages'
    global $connection;

    $sql = $connection->query("SELECT a.advantage_id, a.advantage_value as 'damage', b.name as 'damage_on' FROM advantages a
        INNER JOIN basic b ON a.basic_id = b.basic_id WHERE a.condition_id = '".$condition_id."'");

    $conditions = array();
    if(!$sql){
        $conditions["error"] = true;
        $conditions["error_data"] = $connection->error;
    } else {
        $rows = array();
        $conditions["error"] = false;
        while($row = $sql->fetch_array(MYSQLI_ASSOC)){
            $rows[] = $row;
        }
        $conditions["condition_data"] = $rows;
    }

    return $conditions;
        //If failed, this contains "error"=>true.
        //If succeeded, this contains "error"=>false, "condition_data"=>actual data
}


