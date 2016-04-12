<?php

/*
 * Following code will get the patient data for one patient.
 
 Params:
   pt-id: Patient id number 

   Returns:
     A JSON object with all information associated with patient.
 */

// array for JSON response
$response = array();

// include db connect class
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/db_table_constants.php';

// connecting to db
$db = new DB_CONNECT();

//send back failure if no ID parameter passed
if (isset($_GET["pt-id"])) {
    $id = mysql_real_escape_string($_GET['pt-id']);
    //try to find user
    $result = mysql_query("SELECT * from " . TBL_PATIENTS . " WHERE id = '"
                          . $id . "'") or die(mysql_error());
    if($row = mysql_fetch_array($result)){
        $row["success"]=true;
        echo json_encode($row);
    }
    // patient does not exist in records
    else{
        $row = array();
        $row["success"] = false;
        $row["message"] = "Invalid ID.";
        die(json_encode($row));
    }
}else{
  $response = array();
  $response['success'] = false;
  $response['message'] = 'Must specify a Patient ID #.';
  echo json_encode($response);
}
?>

