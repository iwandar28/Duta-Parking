<?php
require 'koneksiBaru.php';
    date_default_timezone_set("Asia/Jakarta");

    if ($_SERVER['REQUEST_METHOD']=="POST") {

        $rfid = '';

        $rfid = trim(isset($_POST['rfid'])) ? $_POST['rfid'] : '';

        $masuk = "SELECT * FROM masuk WHERE rfid = '$rfid'";
        $sqlMasuk = mysqli_query($con, $masuk);
        $countMasuk = mysqli_num_rows($sqlMasuk);

        if($countMasuk < 1){
            $response = array(
                "status"  => "404",
                "message" => "DATA TIDAK ADA",
                "result"  => '',
                );
                header('Content-Type: application/json');
                echo json_encode($response);    
        }else{
            $delete = "DELETE FROM masuk WHERE rfid = '$rfid'";
            $sqlDelete = mysqli_query($con, $delete);
            $response = array(
                "status"  => "202",
                "message" => "uuid $rfid telah dihapus",
                "result"  => '',
                );
                header('Content-Type: application/json');
                echo json_encode($response);    
        }

    }
?>