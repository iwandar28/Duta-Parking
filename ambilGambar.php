<?php 

require 'connect.php';

// define('HOST', 'localhost');
// define('USER', 'root');
// define('PASS', '');
// define('DB', 'parkir');


// $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');
date_default_timezone_set("Asia/Jakarta");


	if ($_SERVER['REQUEST_METHOD']=="POST") {
			$pintu = $_POST['pintu'];
			
			$pintu = trim(isset($_POST['pintu'])) ? $_POST['pintu'] : '';
            
			$select = "SELECT * FROM masuk WHERE pintu = '$pintu' AND nopol2 is NULL LIMIT 1";
			$sqlSelect = mysqli_query($con, $select);
			$jum  = mysqli_num_rows($sqlSelect);


			if($jum > 0){

				foreach($sqlSelect as $i){
			 		$kode = $i['kode'];
			 	}

				$response = array(
	                'status' => '200',
	                'kode' => $kode,
	                );
	            //http_response_code(403);
	            header('Content-Type: application/json');
	            echo json_encode($response);

				$update = "UPDATE masuk SET nopol2 = 'on' WHERE kode = '$kode'";
				$sqlUpdate = mysqli_query($con, $update);

					
			}else{
				$response = array(
	                'status' => '400',
	                );
	            //http_response_code(403);
	            header('Content-Type: application/json');
	            echo json_encode($response);
			}




			
    }
           
 ?>

