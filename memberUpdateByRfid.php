<?php

require 'connect.php';

// define('HOST', 'localhost');
// define('USER', 'parkirot_parkirot_456');
// define('PASS', '1Parkir123');
// define('DB', 'parkirot_parkir');


// define('HOST', 'localhost');
// define('USER', 'root');
// define('PASS', '');
// define('DB', 'parkir');

// $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');
// date_default_timezone_set("Asia/Jakarta");

$id = $_POST['id'];
$norfid = $_POST['norfid'];
$iduser = $_POST['iduser'];


$viewdata = array(
    'status'=>200,
    'result'=>false,
    'error'=>null
 );



$query = mysqli_query($con, 
		"UPDATE member SET 
		norfid = '$norfid',
		iduser = '$iduser'
		WHERE id = $id
		");

$viewdata['result'] = $query;



mysqli_close($con);

header('Content-type: application/json');
echo json_encode($viewdata)
?>