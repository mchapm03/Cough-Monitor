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

//authenticate only if email and password sent
if(isset($_POST["email"]) and isset($_POST["pass"])) {
    $email = mysql_real_escape_string($_POST['email']);
    $pass  = md5(mysql_real_escape_string($_POST['pass']));
    $name = mysql_real_escape_string($_POST['name']);

    $result = mysql_query("SELECT * from " . TBL_CRED . " WHERE email = '"
                          . $email . "'") or die(mysql_error());

    $response = array();

    //if this email doesn't already exist, attempt to insert it
    if(mysql_num_rows($result) == 0) {
        
        $sql = "INSERT INTO " . TBL_CRED . " (email, pass, name) VALUES('" . $email . "', '" . $pass . "', '" . $name . "')";

        if(mysql_query($sql)) {
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
        $response['message'] = "Email already exists in records";
    }
}

//missing params
else {
    $response['success'] = false;
    $response['message'] = "Required field(s) missing";
}

echo json_encode($response);
?>
