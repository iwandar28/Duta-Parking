<?php

require 'connect.php';

// // define('HOST', 'localhost');
// // define('USER', 'parkirot_parkirot_456');
// // define('PASS', '1Parkir123');
// // define('DB', 'parkirot_parkir');

// define('HOST', 'localhost');
// define('USER', 'root');
// define('PASS', '');
// define('DB', 'parkir');


// $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');
date_default_timezone_set("Asia/Jakarta");

$offset = $_GET['offset'];
$limit = $_GET['limit'];
$param = $_GET['param'];


 $viewdata = array(
   'status'=>200,
   'totaldata'=>0,
   'result'=>array(),
   'error'=>null
);

$query = mysqli_query($con, 
        "SELECT id FROM member WHERE pan LIKE '%$param%' OR nopol LIKE '%$param%' OR nama LIKE '%$param%' ORDER BY id ASC");
$totalData = mysqli_num_rows($query);
$viewdata['totaldata']=$totalData == null ? 0 : $totalData;

//---------------------


$query1 = mysqli_query($con, 
        "SELECT * FROM member WHERE pan LIKE '%$param%' OR nopol LIKE '%$param%' OR nama LIKE '%$param%' ORDER BY id ASC LIMIT $limit OFFSET $offset");
$datadetail = array();
$spasi = " ";
    while($output = mysqli_fetch_array($query1)){
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
        array_push($viewdata['result'], $row);  
    }
header('Content-type: application/json');
echo json_encode($viewdata)

?>