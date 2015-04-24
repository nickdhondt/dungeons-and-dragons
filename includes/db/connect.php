<?php

/* DB connect */

$servername = "localhost";
$username = "root";
$password = "";
$database = "dungeons_and_dragons";

$connection = new mysqli($servername, $username, $password, $database, 3306);

if (!$connection) {
    die ("verbinding met de database mislukt: " . $connection->connect_errno);
}

?>