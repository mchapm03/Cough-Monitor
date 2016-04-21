<?php

/*
 * Following code will get the patient data for one patient.
 
 Params:
   pt-id: Patient id number 

   Returns:
     A JSON object with all information associated with patient.
 */
 header("Access-Control-Allow-Origin: *");

// array for JSON response
$response = array();

// include db connect class
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/db_table_constants.php';

// connecting to db
$db = new DB_CONNECT();

//send back failure if no ID parameter passed
if (isset($_POST["pt-id"])) {
    $id = mysql_real_escape_string($_POST['pt-id']);
    //try to find user
    $result = mysql_query("SELECT * from P" . $id ) or die(mysql_error());
    if($result){
      $response = array();
      $response["success"]=true;
      while($row = mysql_fetch_assoc($result)){
        $response[$row['date']] = [$row['COUGH_COUNT'], $row['QUALITY']];
      }
      echo json_encode($response);

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

