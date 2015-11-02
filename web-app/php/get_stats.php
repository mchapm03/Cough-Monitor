<?php

/* Authenticates a user. Returns a json response with success code
   and a message if authentication failed.
   
   Params:
     email - user's email
     pass  - user's password

   Returns:
     JSON success/failure w/ message
*/

// include db connect class
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/db_table_constants.php';

// connecting to db
$db = new DB_CONNECT();
    //get total number of users

        $patients = mysql_query("SELECT COUNT(*) from " . TBL_PATIENTS ) or die(mysql_error());
        $res_array = mysql_fetch_array($patients);
    //get total number of recordings

        $recordings = mysql_query("SELECT SUM(no_recordings) from " . TBL_PATIENTS ) or die(mysql_error());
        $rec_array = mysql_fetch_array($recordings);
    //if user found, verify password and build response accordingly
    $response = array();
    $response['success'] = true;
    $response['no_pts'] = $res_array["COUNT(*)"];
    $response['no_recs'] = $rec_array["SUM(no_recordings)"];


//return JSON
echo json_encode($response);
?>

