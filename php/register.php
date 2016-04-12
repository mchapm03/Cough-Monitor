<?php

/* Given an email and  password, adds a new user to the credentials table (if email
   doesn't already exist) Also adds a new table to the database for this patient.

   Params:
     email - user's email (must not already exist in records)
     pass  - user's password
*/
header("Access-Control-Allow-Origin: *");

// include db connect class
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/db_table_constants.php';

// connecting to db
$db = new DB_CONNECT();

// Creates a new patient cough table "P<ID>" to hold data/references to all files uploaded for a patient
// Also creates a table called "P<ID>_SUMMARY"  
function create_new_patient_table($id) {
    //grab the new record from the patients table
    $sql = "SELECT * from " . TBL_PATIENTS . " WHERE id = '" . $id . "'";
    $result = mysql_query($sql) or die(mysql_error());
    
    //strip the auto-assigned ID from the new record
    $row = mysql_fetch_array($result);
    $ID = $row["ID"];

    //create a table for the new patient using patient ID as the table name
    $new_table = "P" . $id;
    $sql = "CREATE TABLE " . $new_table . " SELECT * FROM " . TBL_P_TEMPLATE .
           " WHERE 1=0";
    mysql_query($sql) or die(mysql_error());

    //create a summary table using the template, PO_SUMMARY
    $new_table .= "_SUMMARY";
    //$sql2 = "CREATE TABLE " . $new_table . " SELECT * FROM " . TBL_P_SUMMARY_TEMPLATE . " WHERE 1=0";
           $sql2 = "CREATE TABLE " . $new_table . " SELECT * FROM P0_SUMMARY WHERE 1=0";
    mysql_query($sql2) or die(mysql_error());
}

//authenticate only if name and id sent in POST request
if(isset($_POST["pt_id"])) {
    
    $id  = mysql_real_escape_string($_POST['pt_id']);
    //$dob  = mysql_real_escape_string($_POST['pt_dob']);
    $diagnosis = mysql_real_escape_string($_POST['diagnosis_date']);
    $tx_start  = mysql_real_escape_string($_POST['tx_start']);
    $notes  = mysql_real_escape_string($_POST['notes']);




    $result = mysql_query("SELECT * from " . TBL_PATIENTS . " WHERE id = '"
                          . $id . "'") or die(mysql_error());
    $response = array();
    //if this id doesn't already exist, attempt to insert it
    if(mysql_num_rows($result) == 0) {
        // Add patient information into the PATIENTS table
        $sql = "INSERT INTO " . TBL_PATIENTS . " (id, diagnosis_date, tx_start, notes) VALUES ('" . $id . "', '" . $diagnosis  . "', '" . $tx_start  . "', '" . $notes . "')";
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
