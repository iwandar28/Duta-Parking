<?php 

require 'koneksiBaru.php';


	$penomeren = "SELECT kode, waktu, if(now() < waktu, 'GAGAL','sukses') as status FROM `penomeran` WHERE kode = 'masuk'";
	$perintahPenomeran = mysqli_query($con, $penomeren);
	$jumPenomeran = mysqli_num_rows($perintahPenomeran);

	foreach($perintahPenomeran as $i){
		$statusPenomeran = $i['status'];
	}

	if ($_SERVER['REQUEST_METHOD']=="POST") {
			$rfid = '';
			$pintu = '';
			
			$rfid = trim(isset($_POST['rfid'])) ? $_POST['rfid'] : '';
			$pintu = trim(isset($_POST['pintu'])) ? $_POST['pintu'] : '';

            
            $kode = "SELECT substr(concat(curdate()+0,time_to_sec(curtime()),'$pintu'),4) as kode FROM penomeran LIMIT 1";
            $uniqKode = mysqli_query($con, $kode);
            foreach($uniqKode as $i){
                $kodeBaru = $i['kode'];
            }
            
            if(empty($rfid) && empty($pintu)){
                $response = array(
                    'status' => '403',
                    'message' => 'RFID DAN PINTU MASUK KOSONG',
                    'kode' => $kodeBaru,
                );
                //http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode($response);
            }else{
                $selectMember = "SELECT norfid, status FROM member WHERE norfid = '$rfid' AND status = '3'";
                $sqlMember = mysqli_query($con, $selectMember);
                $jumMember = mysqli_num_rows($sqlMember);

                if($jumMember > 0){
                    $insertMember = "INSERT INTO masuk2 VALUES ('$kodeBaru',NOW(),'$rfid','FREE PASS','$pintu','','','FREE PASS','')";
                    $sqlInsert = mysqli_query($con, $insertMember);
                    $response = array(
                        'status' => '200',
                        'message' => 'SUPER BERHASIL MASUK',
                        'kode' => $kodeBaru,
                    );
                    //http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                }else{
                    $response = array(
                        'status' => '403',
                        'message' => 'MEMBER GAGAL MASUK',
                        'kode' => $kodeBaru,
                    );
                    header('Content-Type: application/json');
                    echo json_encode($response);
                }
            }

            // if($statusPenomeran == 'GAGAL'){
            //     $response = array(
            //         'status' => '403',
            //         'message' => 'data gagal',
            //       );
            //       //http_response_code(403);
            //       header('Content-Type: application/json');
            //       echo json_encode($response);	
            //  }else{
            //     $perintahKode = mysqli_query($con, $kode);
	
            //     $jumlahRfid = "SELECT COUNT(rfid) AS JumlahRfid FROM masuk WHERE rfid = '$rfid'";
            //     $perintahKode1 = mysqli_query($con, $jumlahRfid);
        
            //     foreach($perintahKode1 as $i){
            //         $jumRfid = $i['JumlahRfid'];
            //     }
        
            //     // echo json_encode($jumRfid); 
        
            //     foreach($perintahKode as $i){
            //         $kodeBaru = $i['kode'];
            //     }
        
            //     $kodeAnyar = $kodeBaru; 
        
            //     $response = array();
        
            //     $insert = "INSERT INTO masuk VALUE('$kodeAnyar',NOW(),'$pintu','$jenis','$rfid','','','$cardnumber','$cardtype')";
            //      if($jumRfid == 0){
            //             if(mysqli_query($con, $insert)){
            //                 $response = array(
            //                     'status' => '200',
            //                     'message' => 'DATA BERHASIL MASUK',
            //                   );
            //                   //http_response_code(403);
            //                   header('Content-Type: application/json');
            //                   echo json_encode($response);
            //             } else{
            //                 $response = array(
            //                     'status' => '403',
            //                     'message' => 'DATA GAGAL MASUK',
            //                   );
            //                   //http_response_code(403);
            //                   header('Content-Type: application/json');
            //                   echo json_encode($response);				
            //             }
            //         }else if($jumRfid > 0){
            //             $scanMasuk = "SELECT waktu FROM masuk WHERE rfid = '$rfid'";
            //             $perintahMasuk = mysqli_query($con, $scanMasuk);
            //             foreach($perintahMasuk as $i){
            //                 $ambilWaktu = $i['waktu'];
            //             }
            //             $resultTransaksi = array('waktu' => $ambilWaktu);
            //             $response = array(
            //                 'status' => '403',
            //                 'message' => 'DATA SUDAH ADA',
            //                 'result' => $resultTransaksi,
            //               );
            //               //http_response_code(403);
            //               header('Content-Type: application/json');
            //               echo json_encode($response);
            //         }else{
			// 			$response = array(
			// 				'status' => '403',
			// 				'message' => 'DATA GAGAL MASUK',
			// 			  );
			// 			  //http_response_code(403);
			// 			  header('Content-Type: application/json');
			// 			  echo json_encode($response);				
			// 		}
                
            //  }

			// $kode = "SELECT substr(concat(curdate()+0,time_to_sec(curtime()),'$pintu'),4) as kode FROM penomeran LIMIT 1";
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