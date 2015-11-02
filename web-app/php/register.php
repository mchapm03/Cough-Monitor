<?php

/* Given an email and  password, adds a new user to the credentials table (if email
   doesn't already exist) Also adds a new table to the database for this patient.

   Params:
     email - user's email (must not already exist in records)
     pass  - user's password
*/

// include db connect class
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/db_table_constants.php';

// connecting to db
$db = new DB_CONNECT();

//grabs the ID associated with $email and creates a new patient cough table "P<ID>"
function create_new_patient_table($id) {
    //grab the new record from the credentials table
    $sql = "SELECT * from " . TBL_PATIENTS . " WHERE id = '" . $id . "'";
    $result = mysql_query($sql) or die(mysql_error());
    
    //strip the auto-assigned ID from the new record
    $row = mysql_fetch_array($result);
    $ID = $row["ID"];

    //create a table for the new patient using patiend ID as the table name
    $new_table = "P" . $id;
    $sql = "CREATE TABLE " . $new_table . " SELECT * FROM " . TBL_P_TEMPLATE .
           " WHERE 1=0";
    mysql_query($sql) or die(mysql_error());
}

//authenticate only if email and password sent
if(isset($_POST["pt_name"]) and isset($_POST["pt_id"])) {
    
    $name = mysql_real_escape_string($_POST['pt_name']);
    $id  = mysql_real_escape_string($_POST['pt_id']);
    $dob  = mysql_real_escape_string($_POST['pt_dob']);
    $wt = mysql_real_escape_string($_POST['pt_wt']);
    $ht  = mysql_real_escape_string($_POST['pt_ht']);
    $diagnosis = mysql_real_escape_string($_POST['diagnosis_date']);
    $tx_start  = mysql_real_escape_string($_POST['tx_start']);
    $notes  = mysql_real_escape_string($_POST['notes']);




    $result = mysql_query("SELECT * from " . TBL_PATIENTS . " WHERE id = '"
                          . $id . "'") or die(mysql_error());
    $response = array();
    //if this id doesn't already exist, attempt to insert it
    if(mysql_num_rows($result) == 0) {
        $sql = "INSERT INTO " . TBL_PATIENTS . " (name, id, dob, wt, ht, diagnosis_date, tx_start, notes) VALUES ('" . $name . "', " . $id . ", '" . $dob  . "', " . $wt . ", " . $ht . ", '" . $diagnosis  . "', '" . $tx_start  . "', '" . $notes . "')";
        //if new record successfully created, make a new patient cough table
        if(mysql_query($sql)) {
            create_new_patient_table($id);
            $response['success'] = true;
        }
        else {
            $response['success'] = false;
            $response['message'] = "User registration failed";
        }

    }
    //email already exists
    else {
        $response['success'] = false;
        $response['message'] = "Patient id already exists in records";
    }
} 

//missing params
else {
    $response['success'] = false;
    $response['message'] = "Required field(s) missing";
}

echo json_encode($response);
?>
