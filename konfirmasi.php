<?php 

require 'koneksiBaru.php';
    // require 'koneksiBaru.php';

	if ($_SERVER['REQUEST_METHOD']=="POST") {

		$nobuktimkp = '';
		$nobuktiduta = '';
		$amounttrx = '';
		$lastbalance = '';
		$statuspayment = '';
		$statuspaymentdesc = '';
		
		$nobuktimkp = trim(isset($_POST['nobuktimkp'])) ? $_POST['nobuktimkp'] : '';
		$nobuktiduta = trim(isset($_POST['nobuktiduta'])) ? $_POST['nobuktiduta'] : '';
		$amounttrx = trim(isset($_POST['amounttrx'])) ? $_POST['amounttrx'] : '';
		$lastbalance = trim(isset($_POST['lastbalance'])) ? $_POST['lastbalance'] : '';
		$statuspaymentdesc = trim(isset($_POST['statuspaymentdesc'])) ? $_POST['statuspaymentdesc'] : '';
		$statuspayment = trim(isset($_POST['statuspayment'])) ? $_POST['statuspayment'] : '';
        
		$updateKeluar = "UPDATE keluar SET amounttrx = '$amounttrx', noref = '$nobuktimkp', statuspaymentdesc = '$statuspaymentdesc', saldoakhir = '$lastbalance' WHERE kode = '$nobuktiduta' ";
        
        $selectKeluar = "SELECT pintuk FROM keluar WHERE kode = '$nobuktiduta'";
		$perintahKeluar = mysqli_query($con, $selectKeluar);
    

		foreach($perintahKeluar as $i){
			$pintuKeluar = $i['pintuk'];
        }
        
		
		// $insertKonfirmasi = "INSERT INTO konfirmasi  set saldo = '$lastbalance', tarif = '$amounttrx', status = '$statuspayment' WHERE pintu = '$pintuKeluar'";

		
		if(empty($nobuktimkp) || empty($nobuktiduta) || empty($statuspaymentdesc) || empty($statuspayment)){
			$response = array(
				'status' => '404',
				'message' => 'ADA KEY YANG BELUM DI INPUT',
				'result'  => '',
				);
			//http_response_code(403);
			header('Content-Type: application/json');
			echo json_encode($response);
		}else if(mysqli_query($con, $updateKeluar)){
			
			$insertKonfirmasi = "INSERT INTO konfirmasi  (saldo, tarif, status) VALUES ('$lastbalance','$amounttrx','$statuspayment') WHERE pintu = '$pintuKeluar'";
			$konfirmasiKeluar = mysqli_query($con, $insertKonfirmasi);
			$lastBalanceCheck = array('last balance' => $lastbalance);
        	$response = array(
			'status' => '200',
			'message' => 'SUKSES KONFIRMASI',
			'result'  => $lastBalanceCheck,
			);
			//http_response_code(403);
			//echo json_encode($response);
			header('Content-Type: application/json');

			// if($statuspayment == '200'){
			// 	$deleteMasuk = "DELETE FROM masuk WHERE kode = '$nobuktiduta'";
			// 	mysqli_query($con, $deleteMasuk);
			// 	$response = array(
			// 		'status' => '201',
			// 		'message' => 'SUKSES KONFIRMASI',
			// 		'result'  => '',
			// 		);
			// 	//http_response_code(403);
			// 	header('Content-Type: application/json');
			// 	echo json_encode($response);
			// }
		}else{
			$response = array(
				'status' => '404',
				'message' => 'Update Gagal',
				'result'  => '',
				);
			//http_response_code(403);
			header('Content-Type: application/json');
			echo json_encode($response);
		}
	}

 ?>