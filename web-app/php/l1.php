<?php

$logged_in = false;
if (!isset($_SERVER['PHP_AUTH_USER']) and !isset($_POST["logout"])) {
    header('WWW-Authenticate: Basic realm=""');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Please enter username and password to get to portal.';
    exit;
} elseif (isset($_POST["logout"])){
    header('WWW-Authenticate: Basic realm=""');
	header('HTTP/1.0 401 Unauthorized');
    echo 'Please enter username and password to get to portal.';

}else {


	require_once dirname(__FILE__) . '/db_connect.php';
	require_once dirname(__FILE__) . '/db_table_constants.php';

	// connecting to db
	$db = new DB_CONNECT();


    $email = $_SERVER['PHP_AUTH_USER'];
    $pass  = md5($_SERVER['PHP_AUTH_PW']);

    //try to find user
    $result = mysql_query("SELECT * from " . TBL_CRED . " WHERE email = '"
                          . $email . "'") or die(mysql_error());

    //if user found, verify password and build response accordingly
    if($row = mysql_fetch_array($result)) {

        if(strcmp($row["pass"], $pass) == 0) $logged_in = true;
        else {
		    header('WWW-Authenticate: Basic realm=""');
		    header('HTTP/1.0 401 Unauthorized');
		    echo 'Please enter username and password to get to portal.';
        }
    }
    //send back failure if email not found
    else {
	    header('WWW-Authenticate: Basic realm=""');
	    header('HTTP/1.0 401 Unauthorized');
	    echo 'Please enter username and password to get to portal.';
    }
}
?>

