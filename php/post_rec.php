<?php

/* Given a patient ID number and an array of files, uploads audio files to the 
    server. Files are uploaded into the audio folder in a folder that is named
    by the patient id. If new files are added, the no_recordings count in the
    TBL_PATIENTS table is updated. 

   Params:
     id= patient id
     date = date recording taken
   Files:
      audio files for coughs
*/


// TODO: figure out how to run matlab script:
      // matlab -nodisplay -r "runAllWavInDATADIR"
header("Access-Control-Allow-Origin: *");

// include db connect class
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/db_table_constants.php';

// connecting to db
$db = new DB_CONNECT();

// get the patient id, sample code, hospital code, and date from the POST param
$ID = $_POST["id"];
$DATE = $_POST["date"];
$SAM_CODE = $_POST["sample-code"];
$HOS_CODE = $_POST["hos-code"];

//format the date:
$DATE_FORMATTED = new DateTime($DATE);

//Get the treatment start date: 
 $get_tx_start = mysql_query("SELECT tx_start from " . TBL_PATIENTS . " WHERE id = " . $ID ) or die(mysql_error());
 //$get_tx_start = mysql_query("SELECT tx_start from " . TBL_PATIENTS . " WHERE id = " . $ID ) or die(mysql_error());

echo json_encode($get_tx_start);
$tx_start = $get_tx_start['tx_start'];
$days_since_tx_start = date_diff($DATE_FORMATTED, (new DateTime($tx_start)));

// each file is named like: T_0332_HC521_20160321_03, where
// T_ : for all because it is a cough recording
// 0332 : is the sample code, input by user in text field
// HC : hospital code, HC or DM for this study
// 521 : patient id number
// 20160321 : datestring
// 03 : recording number
$FILE_STRING = "T_" . $SAM_CODE . "_" . $HOS_CODE . $ID . "_" . date_format($DATE_FORMATTED, "Ymd") . "_" . ($days_since_tx_start->format("%D"));

// Keep track of the total number of coughs for the whole day
$Total_coughs = 0;
// see if patient exists in database
$result = mysql_query("SELECT * from " . TBL_PATIENTS . " WHERE id = '"
                          . $ID . "'") or die(mysql_error());

if(mysql_num_rows($result) == 0) {
    $response = array();
    $response['success'] = false;
    $response['message'] = 'Patient ID does not exist in records.';
    echo json_encode($response);
}else{
    // Get the patients table
    $patient_table = "P" . $ID;

    // patient audio files located at /p539/patient_data/audio/[ID]
    $rec_output_dir  = $_SERVER['DOCUMENT_ROOT'];
    // for the server:
    $rec_output_dir .= "/tufts_cough/patient_data/audio/" . $ID . "/";


    // check that the patient has a folder for it's audio files
    if(!file_exists($rec_output_dir)) {
        mkdir($rec_output_dir, 0755);
        $file_count = 1;
    }else{
        $file_count = iterator_count(new FilesystemIterator($rec_output_dir, FilesystemIterator::SKIP_DOTS))+1;
    }

    //$FILE_STRING .= "_" . strval($file_count);

    $error = false;
    $response = array();
    $files = array();


    $uploaddir = $rec_output_dir;

    // delete stuff in  getcwd() . '/matlab/DATADIR'
    $oldfiles = glob(getcwd() . '/matlab/DATADIR/*'); // get all file names
    foreach($oldfiles as $file){ // iterate files
      if(is_file($file))
        unlink($file); // delete file
    }

    // upload each file and add the name to the patient's table for easy access later
    foreach($_FILES as $file){

            if(move_uploaded_file($file['tmp_name'], $uploaddir .$FILE_STRING)){
                $files[] = $uploaddir .$FILE_STRING;
                // Also put file in folder for matlab algorithm to see it
                copy($uploaddir .$FILE_STRING, getcwd() . '/matlab/DATADIR/' . $file['name']);
                // simulate cough rate
                // TODO: get cough rate from MATLAB algorithm
                $runMatlab = exec("cd matlab & runAllWavInDATADIR.exe");

                $cough_amt = rand(30, 70);
                $Total_coughs += $cough_amt;
                $sql = "INSERT INTO " . $patient_table . " (date, FILE_NAME, COUGH_COUNT) " . "VALUES ('" . $DATE . "', '" . $file['name'] . "', '" . $cough_amt . "');";
                mysql_query($sql) or die(mysql_error());

            }else{
                $error = true;
            }
        
    }

    //if files successfully uploaded to getcwd() . '/matlab/DATADIR', run matlab script
    //get stuff out of output folder

    // If no error, success = true, else success = false
    $response['success'] = !$error;
    // If error, return error message, else return an array of file names
    $response['message'] = ($error) ? array('error' => 'There was an error uploading your files' . $uploaddir) : array('files' => $files);

    // Record a summary to the $P<ID>_SUMMARY table
    $pt_summary_table = $patient_table . "_SUMMARY";
    // TODO: get a more exact rate from hardware
    $cough_rate = ((float)$Total_coughs) / 24.0;
    $summary_sql = "INSERT INTO " . $pt_summary_table . " (date, cough_rate, total_coughs) VALUES ('" . $DATE . "', '" . $cough_rate . "', '" . $Total_coughs . "');";
    mysql_query($summary_sql) or die(mysql_error());

    //update the no_recordings column for the patient in the TBL_PATIENTS table
    $no_recs_sql = mysql_query("SELECT no_recordings FROM " . TBL_PATIENTS . " WHERE id=" . $ID . ";") or die(mysql_error());
    $no_recs_before = (int)mysql_fetch_array($no_recs_sql)['no_recordings'];
    $total_files = count($_FILES);
    $new_count = ($no_recs_before + count($_FILES));
    $sql = "UPDATE " . TBL_PATIENTS . " SET no_recordings=" .$new_count." WHERE id=" . $ID . ";";
    mysql_query($sql) or die(mysql_error());
    
    echo json_encode($response);
}
?>