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

$response = array();
if(isset($_POST['id'])){
	$rec_output_dir  = $_SERVER['DOCUMENT_ROOT'];
	$rec_output_dir .= "/p539/patient_data/audio/" . $_POST['id'] . "/";
	if(file_exists($rec_output_dir)) {
		$response['success'] = true;
		$response['message'] = array_diff(scandir($rec_output_dir), array('..', '.'));
	}else{
		$response['success'] = false;
		$response['message'] = "No recordings have been uploaded for this patient.";
	}
}else{
	$response['success'] = false;
}
echo json_encode($response);

?>