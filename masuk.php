<?php 

require 'connect.php';




	if ($_SERVER['REQUEST_METHOD']=="POST") {
			$rfid = '';
			$jenis = '';
			$pintu = '';
			$token = '';
			$cardnumber = '';
			$cardtype = '';
			$kodePintu = '';
			
			$rfid = trim(isset($_POST['rfid'])) ? $_POST['rfid'] : '';
			$jenis = trim(isset($_POST['jenis'])) ? $_POST['jenis'] : '';
			$pintu = trim(isset($_POST['pintu'])) ? $_POST['pintu'] : '';
			$token = trim(isset($_POST['token'])) ? $_POST['token'] : '';
			$cardnumber = trim(isset($_POST['cardnumber'])) ? $_POST['cardnumber'] : '';
            $cardtype = trim(isset($_POST['cardtype'])) ? $_POST['cardtype'] : '';
            $kodePintu = trim(isset($_POST['kodepintu'])) ? $_POST['kodepintu'] : '';
            



            $penomeren = "SELECT substr(concat(curdate()+0,time_to_sec(curtime()),'$kodePintu'),4) as kode FROM penomeran LIMIT 1";
            $perintahPenomeran = mysqli_query($con, $penomeren);
            $jumPenomeran = mysqli_num_rows($perintahPenomeran);
            

            foreach($perintahPenomeran as $i){
                $kode =  $i['kode'];
            }



            $masuk = "SELECT * FROM masuk WHERE rfid = '$rfid'";
            $sqlMasuk = mysqli_query($con, $masuk);
            $countMasuk = mysqli_num_rows($sqlMasuk);
            foreach($sqlMasuk as $i){
                $waktu = $i['waktu'];
            }

            if($countMasuk < 1){
                $insert = "INSERT INTO masuk (kode, waktu, jenis,  pintu, rfid, cardnumber, cardtype) VALUES('$kode', NOW(),'$jenis','$pintu', '$rfid', '$cardnumber', '$cardtype')";
                $sqlInsert = mysqli_query($con, $insert);
 

                $response = array(
                    "status"  => "200",
                    "message" => "SUKSES",
                    "kode" => $kode
                    );
                    echo json_encode($response);
                

            }else{
                $response = array(
                    "status"  => "403",
                    "waktu" => $waktu,
                    "kode" => $kode
                    );
                    echo json_encode($response);
            }
    }
           
 ?>