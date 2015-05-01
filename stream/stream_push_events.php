<?php
/* Template to build on */
/* This document will stream real time events to clients */

// This header defines this file is a steam
header("Content-Type: text/event-stream\n\n");
header("Cache-Control: no-cache");

// Set the script start as a float
$script_beginning = microtime(true);

// Every 5 seconds we will send a ping event to check connectivity
$last_ping = 0;

// Set the maximum execution time to 300 seconds (= 5 minutes) instead of 30 seconds
ini_set('max_execution_time', 300);

require_once "../includes/functions.php";

// The timestamp is set to 0 at the beginning of the script, this will cause the script to stream all data one time at the beginning
// Explanation follows
$timestamp = 0;

// Get the request user id
$user_id = $_GET["user_id"];

// We need to flush data while running the script (because otherwise the script will send all it's data after about 5 minutes instead of real time
ob_implicit_flush(true);
ob_end_flush();

$first_loop = true;

// Based on the script beginning time, we execute as far as 280 seconds (4 minutes 40 seconds)
while ($script_beginning >= (microtime(true) - 280)) {

    // This stream is based on this concept:
    // A user connect and $timestamp is 0: the function below will check the db for records where the time (= time they were inserted/updated) is greater that $timestamp
    // They will (should) always be greater than 0 ((micro)time = seconds since 1970). We send the data to the client and set the timestamp to now (microtime())
    // The new loop will not get the data from the database again because the record update/insert times are not greater than $timestamp (which has been update to now (microtime()))
    // When a record is updated the time in the database will be greater, so it will be send to the user
    // (see opposing-buttons on github for actual code)
    //$new_events = function_in_other_file($timestamp);

    // Ping very 5 seconds
    if ($last_ping < (microtime(true) - 5)) {
        // Define ping event for javascript eventSource()
        echo "event: ping\n";
        // Force a reconnection after 2 seconds (if connection is lost client side)
        echo "retry: 2000\n";
        // Actual data (please always JSON)
        $ping_event_time = json_encode(array("time" => microtime(true)));
        echo "data: " . $ping_event_time . "\n\n";

        $last_ping = microtime(true);
    }
    //Set the new events to the following arrays.
    $uid = $_GET["user_id"];
    $new_events = array();
    $new_events["basic"] = get_basic_data($uid, $timestamp);
    $new_events["levelling"] = get_levelling_data($uid, $timestamp);
    $new_events["skill"] = get_skill_data($uid, $timestamp);
    $new_events["general"] = get_general_data($uid, $timestamp);

    //Check if the new events don't return false:
    $new_data = false;
    foreach($new_events as $new_event){
        if($new_event != false) $new_data = true;
    }

    //Stream the $new_events
    if ($new_data != false) {
        // JSON encode
        $json_game_data = json_encode($new_events);

        // Define ping event for javascript eventSource()
        echo "event: game_event\n";
        // Force a reconnection after 2 seconds (if connection is lost client side)
        echo "retry: 2000\n";
        // Actual data (please always JSON)
        echo "data: " . $json_game_data . "\n\n";

        // Flush the cache to the user
        flush();
        // Update current time to prevent db form returning data when not necessary
        $timestamp = microtime(true);
    }

    // Flush the cache to the user
    flush();

    // pause the loop for .5 seconds
    if ($first_loop === true) {
        $first_loop = false;
    } else {
        usleep(500000);
    }
}