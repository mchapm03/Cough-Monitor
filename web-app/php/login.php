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

//authenticate only if email and password sent
if(isset($_POST["email"]) and isset($_POST["pass"])) {
    $email = mysql_real_escape_string($_POST['email']);
    $pass  = md5(mysql_real_escape_string($_POST['pass']));

    //try to find user
    $result = mysql_query("SELECT * from " . TBL_CRED . " WHERE email = '"
                          . $email . "'") or die(mysql_error());
    //try to find user
   // $result = mysql_query("SELECT * from " . TBL_CRED . " WHERE EMAIL = '"
     //                     . $email . "'") or die(mysql_error());

    //if user found, verify password and build response accordingly
    $response = array();
    if($row = mysql_fetch_array($result)) {

        if(strcmp($row["pass"], $pass) == 0)
            $response['success'] = true;
        else {
            $response['success'] = false;
            $response['message'] = "Incorrect Password. Please try again.";
        }
    }
    //send back failure if email not found
    else {
        $response['success'] = false;
        $response['message'] = "No account with given email. Please try again or <a href='new-user.html'>make a new account.</a>";
    }
}
//missing params
else {
    $response['success'] = false;
    $response['message'] = "Required field(s) missing";
}

//return JSON
echo json_encode($response);
?>

