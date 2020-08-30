<?php 

    require 'koneksiBaru.php';

	if ($_SERVER['REQUEST_METHOD']=="POST") {
			$pintu = '';
			
            $pintu = trim(isset($_POST['pintu'])) ? $_POST['pintu'] : '';
            
            $select = "SELECT * FROM konfirmasi WHERE pintu = '$pintu' AND status != '205' LIMIT 1";
            $perintah = mysqli_query($con, $select);
            $jumlahPerintah = mysqli_num_rows($perintah);

            // $updateStatus = "UPDATE konfirmasi SET status = '500' WHERE pintu = '$pintu'";
            // mysqli_query($con, $updateStatus) or die ("data error".mysqli_error());
            
            if($jumlahPerintah > 0){
                foreach($perintah as $i){
                    $id     = $i['id'];
                    $pintu = $i['pintu'];
                    $proses = $i['proses'];
                    $waktum = $i['waktum'];
                    $waktuk = $i['waktuk'];
                    $lama = $i['lama'];
                    $tarif = $i['tarif'];
                    $saldo = $i['saldo'];
                    $kadaluarsa = $i['kadaluarsa'];
                    $nama = $i['nama'];
                    $status = $i['status'];
                    $pesan = $i['pesan'];
                }

                $response = array(
                    'id'    => $id,
                    'pintu' => $pintu,
                    'proses' => $proses,
                    'waktu masuk' => $waktum,
                    'waktu keluar' => $waktuk,
                    'lama' => $lama,
                    'tarif' => $tarif,
                    'saldo' => $saldo,
                    'kadaluarsa' => $kadaluarsa,
                    'nama' => $nama,
                    'status' => $status,
                    'pesan' => $pesan,
                );

                header('Content-Type: application/json');
                echo json_encode($response);

                $delete = "DELETE FROM konfirmasi WHERE id ='$id'";
                mysqli_query($con, $delete) or die ("data error".mysqli_error());

            }else{
                $response = array(
                    'status' => '404',
                    'message' => 'Gagal',
                    'result'  => '',
                    );
                //http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode($response);
            }
			
	}else{
		//todo: method selain post
		$response = array(
			'status' => '403',
			'message' => 'Access Forbidden',
			'result'  => '',
			);
		//http_response_code(403);
		header('Content-Type: application/json');
		echo json_encode($response);
	}

 ?>