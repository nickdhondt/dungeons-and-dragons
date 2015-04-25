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

    $sql = $connection->query("INSERT INTO user (username, password, permission_type) VALUES ('$username', '$password', 0)");

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