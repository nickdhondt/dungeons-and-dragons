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
        $basic_timestamp = $sql->fetch_assoc()[0];
    }

    if($basic_timestamp >= $current_timestamp){
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
        $basic_data_users_entry["user_id"] = $user_id;
        $basic_data_users_entry["username"] = $username;
        $basic_data_users_entry["basic_data"] = $basic_data_user;

        $basic_data_users[] = $basic_data_users_entry;
    }

    return $basic_data_users;
}
