<?php

/* Returns Patient stats. Returns the total number of patients registered in the database
   and the total number of audio files that have been uploaded so far. 

   Returns:
     JSON success/failure w/ no_patients and no_recs
*/
 header("Access-Control-Allow-Origin: *");

// include db connect class
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/db_table_constants.php';

// connecting to db
$db = new DB_CONNECT();
    //get total number of users

        $patients = mysql_query("SELECT COUNT(*) from " . TBL_PATIENTS . " WHERE id > 0;") or die(mysql_error());
        $res_array = mysql_fetch_array($patients);
    //get total number of recordings

        $recordings = mysql_query("SELECT SUM(no_recordings) from " . TBL_PATIENTS .";") or die(mysql_error());
        $rec_array = mysql_fetch_array($recordings);
    //if user found, verify password and build response accordingly
    $response = array();
    $response['success'] = true;
    $response['no_pts'] = $res_array["COUNT(*)"];
    if($rec_array["SUM(no_recordings)"] == null){
        $response['no_recs'] = 0;
    }else{
        $response['no_recs'] = $rec_array["SUM(no_recordings)"];
    }

//return JSON
echo json_encode($response);
?>

