<?php

require 'connect.php';

// define('HOST', 'localhost');
// define('USER', 'root');
// define('PASS', '');
// define('DB', 'parkir');

// $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');

    date_default_timezone_set("Asia/Jakarta");

    // $mainInsert = "INSERT INTO konfirmasi VALUE('','','','','','','','','','')";
    // mysqli_query($con, $mainInsert);

    if ($_SERVER['REQUEST_METHOD']=="POST") {
        //todo: deklarasi variabel

        $rfid = $_POST['rfid'];
        $jenisK = $_POST['jeniskendaraan'];
        $pintuK = $_POST['pintukeluar'];

        // $rfid = '';
        // $jenisK = '';
        // $pintuK = '';
        
        // //todo: deklarasi & validasi jika

        // $rfid = trim(isset($_POST['rfid'])) ? $_POST['rfid'] : '';
        // $pintuk = trim(isset($_POST['pintukeluar'])) ? $_POST['pintukeluar'] : '';
        // $jenisK = trim(isset($_POST['jeniskendaraan'])) ? $_POST['jeniskendaraan'] : '';


        $masukUmum = "SELECT norfid, status FROM member WHERE norfid = '$rfid' AND status = '3'";
        $perintahUmum = mysqli_query($con, $masukUmum);
        $jumlahUmum = mysqli_num_rows($perintahUmum);

        $kode = "SELECT substr(concat(curdate()+0,time_to_sec(curtime()),'$pintuK'),4) as kode FROM penomeran LIMIT 1";
        $uniqKode = mysqli_query($con, $kode);
        foreach($uniqKode as $i){
            $kodeBaru = $i['kode'];
        }


    if($jumlahUmum > 0){

        $response = array(
        "status"  => "200",
        "message" => "TERIMAKASIH",
        "kode" => $kodeBaru,
        );
        echo json_encode($response);
        
        $deleteUmum = "DELETE FROM konfirmasi WHERE pintu = '$pintuK'";
        mysqli_query($con, $deleteUmum);
        
        $insertError1 = "INSERT INTO konfirmasi(pintu, status, waktum, waktuk, nama,kode)
         VALUES('$pintuK','100',NOW(),NOW(),'Super Admin','$kodeBaru')";
        mysqli_query($con, $insertError1);



        $insertError2 = "INSERT INTO keluar2(kode, masuk, keluar, bayar, norfid, pintuk, jenisk) VALUES('$kodeBaru',NOW(),NOW(),'0','$rfid','$pintuK','$jenisK')";
        mysqli_query($con, $insertError2);
    }else{

        $response = array(
            "status"  => "403",
            "message" => "KARTU SALAH",
            'kode' => $kodeBaru,
            );
            echo json_encode($response);
        
            $deleteUmum = "DELETE FROM konfirmasi WHERE pintu = '$pintuK'";
            mysqli_query($con, $deleteUmum);
            
            $insertError1 = "INSERT INTO konfirmasi(pintu, status, waktum, waktuk) VALUES('$pintuK','kartu salah',NOW(),NOW())";
            mysqli_query($con, $insertError1);

            $insertError2 = "INSERT INTO keluar2(kode, masuk, keluar, bayar, norfid, pintuk, jenisk, statuskartu) VALUES('$kodeBaru',NOW(),NOW(),'0','$rfid','$pintuK','$jenisK','kartu salah')";
            mysqli_query($con, $insertError2);
        }
    }else{
        //todo: method selain post
            $response = array(
                'status' => '403',
                'message' => 'ACCESS FORBIDDEN',
                'result'  => '',
              );
              //http_response_code(403);
              header('Content-Type: application/json');
              echo json_encode($response);

        }
?>