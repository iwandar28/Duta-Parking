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

$id = $_POST['id'];
$nama = $_POST['nama'];
$unit = $_POST['unit'];
$statuspegawai = $_POST['statuspegawai'];
$jeniskendaraan = $_POST['jeniskendaraan'];
$nopol = $_POST['nopol'];
$pan = $_POST['pan'];
$norfid = $_POST['norfid'];
$produk = $_POST['produk'];
$awal = $_POST['awal'];
$akhir = $_POST['akhir'];
$userupdate = $_POST['userupdate'];
$iduser = $_POST['iduser'];
$status = $_POST['status'];



$viewdata = array(
    'status'=>200,
    'result'=>false,
    'error'=>null
 );



$query = mysqli_query($con, 
		"UPDATE member SET 
		nama = '$nama',
		unit = '$unit',
		statuspegawai = '$statuspegawai',
		jeniskendaraan = '$jeniskendaraan',
		nopol = '$nopol',
		pan = '$pan',
		norfid = '$norfid',
		produk = '$produk',
		awal = '$awal',
		akhir = '$akhir',
		userupdate = '$userupdate',
		iduser = '$iduser',
		status = '$status'
		WHERE id = $id
		");

$viewdata['result'] = $query;



mysqli_close($con);

header('Content-type: application/json');
echo json_encode($viewdata)
?>