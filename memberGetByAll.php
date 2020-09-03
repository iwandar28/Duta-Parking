<?php

require 'connect.php';

// define('HOST', 'localhost');
// define('USER', 'parkirot_parkirot_456');
// define('PASS', '1Parkir123');
// define('DB', 'parkirot_parkir');


// // define('HOST', 'localhost');
// // define('USER', 'root');
// // define('PASS', '');
// // define('DB', 'parkir');

// $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');
date_default_timezone_set("Asia/Jakarta");



$viewdata = array(
    'status'=>200,
    'result'=>array(),
    'error'=>null
 );
$query = mysqli_query($con, 
		"SELECT * FROM member ORDER BY id DESC");
$datadetail = array();
$spasi = " ";
	while($output = mysqli_fetch_array($query)){
		$row = array();
		$row['id'] = $output['id'];
		$row['nama'] = $output['nama'];
		$row['unit'] = $output['unit'];
		$row['statuspegawai'] = $output['statuspegawai'];
		$row['jeniskendaraan'] = $output['jeniskendaraan'];
		$row['nopol'] = $output['nopol'];
		$row['pan'] = $output['pan'];
		$row['norfid'] = $output['norfid'];
		$row['produk'] = $output['produk'];
		$row['awal'] = $output['awal'];
		$row['akhir'] = $output['akhir'];
		$row['userupdate'] = $output['userupdate'];
		$row['iduser'] = $output['iduser'];
		$row['status'] = $output['status'];
		// $row['pan'] = substr($output['pan'],0,4)." ".substr($output['pan'],4,4)." ".substr($output['pan'],8,4)." ".substr($output['pan'],12,4);
        array_push($viewdata['result'], $row);  
    }
mysqli_close($con);

header('Content-type: application/json');
echo json_encode($viewdata)
?>