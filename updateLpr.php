<?php 

require 'connect.php';



// $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');
date_default_timezone_set("Asia/Jakarta");


	if ($_SERVER['REQUEST_METHOD']=="POST") {
			$kode = $_POST['kode'];
			$nopol = $_POST['nopol'];
			
			
			$kode = trim(isset($_POST['kode'])) ? $_POST['kode'] : '';
			$nopol = trim(isset($_POST['nopol'])) ? $_POST['nopol'] : '';
            
			$select = "UPDATE masuk SET nopol = '$nopol' WHERE kode = '$kode'";
			
			if(mysqli_query($con, $select)){
				$response = array(
	             'status' => '200',
	            );
	            //http_response_code(403);
	        	header('Content-Type: application/json');
	        	echo json_encode($response);
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

