<?php 

	require 'connect.php';

	if ($_SERVER['REQUEST_METHOD']=="POST") {

        $status = '';
        $pesan = '';
        $pintu = '';
        
        $status = trim(isset($_POST['status'])) ? $_POST['status'] : '';
        $pesan = trim(isset($_POST['pesan'])) ? $_POST['pesan'] : '';
        $pintu = trim(isset($_POST['pintu'])) ? $_POST['pintu'] : '';

    
        if(empty($status) || empty($pesan || empty($pintu))){
            $response = array(
                'status' => '404',
                'message' => 'ADA KEY YANG BELUM DI INPUT',
                'result'  => '',
                );
            //http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode($response);
        }else{
            $insert = "INSERT INTO konfirmasi VALUES('','$pintu','','','','','','','','','$status','$pesan')";
            if(mysqli_query($con, $insert)){
                $response = array(
                    'status' => $status,
                    'message' => $pesan,
                    'result'  => $pintu,
                    );
                //http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode($response);
            }else{
                $response = array(
                    'status' => '400',
                    'message' => 'DATA GAGAL MASUK',
                    'result'  => '',
                    );
                //http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode($response);   
            }
        }
    }
 ?>