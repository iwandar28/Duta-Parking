<?php
    require 'koneksiBaru.php';
    require 'key.php';
    date_default_timezone_set("Asia/Jakarta");

    // $mainInsert = "INSERT INTO konfirmasi VALUE('','','','','','','','','','')";
    // mysqli_query($con, $mainInsert);

    if ($_SERVER['REQUEST_METHOD']=="POST") {
        //todo: deklarasi variabel
        $token = '';
        $rfid = '';
        $jenisK = '';
        // $namakartu = '';
        // $noref = '';
        $pintuK = '';
        
        //todo: deklarasi & validasi jika
        $token = trim(isset($_POST['token'])) ? $_POST['token'] : '';
        $rfid = trim(isset($_POST['rfid'])) ? $_POST['rfid'] : '';
        $pintuk = trim(isset($_POST['pintukeluar'])) ? $_POST['pintukeluar'] : '';
        $jenisK = trim(isset($_POST['jeniskendaraan'])) ? $_POST['jeniskendaraan'] : '';
        // $namaKartu = trim(isset($_POST['namakartu'])) ? $_POST['namakartu'] : '';
        // $noref = trim(isset($_POST['noref'])) ? $_POST['noref'] : '';


        $masukUmum = "SELECT rfid FROM masuk WHERE rfid = '$rfid' LIMIT 1";
        $perintahUmum = mysqli_query($con, $masukUmum) or die ("data error".mysqli_error());
        $jumlahUmum = mysqli_num_rows($perintahUmum);


        //todo: validasi key kosong
        if(empty($token) || empty($rfid) || empty($pintuk) || empty($jenisK)){
            $rowPesanBlokir = array(
                "status" => "403",
                "message" => "ADA KEY YANG BELUM DI INPUT",
                "result" => '',
                );
            $pesanBlokir = $rowPesanBlokir;
            echo json_encode($pesanBlokir);

            $pintuK1 = $_POST['pintukeluar'];
            
            $insertError1 = "INSERT INTO konfirmasi(pintu, status, waktuk) VALUES('$pintuK1','401',NOW())";
            //$updateError1 = "UPDATE konfirmasi SET pintu = '$pintuK1', status = '401', waktuk = NOW() WHERE pintu = '$pintuK1'";
            mysqli_query($con, $insertError1) or die ("data error".mysqli_error());
                
        }else{
            //todo: validasi token salah
            if($token != $key){
                $response = array(
                    'status' => '403',
                    'message' => 'INVALID ACCESS KEY',
                    'result' => '',
                  );
                  //http_response_code(403);
                  header('Content-Type: application/json');
                  echo json_encode($response);

                  $pintuK2 = $_POST['pintukeluar'];
                  
                  $updateError1 = "INSERT INTO konfirmasi(pintu, status, waktuk) VALUES('$pintuK2','402',NOW())";
                  mysqli_query($con, $updateError1) or die ("data error".mysqli_error());
        

            }else if($jumlahUmum <= 0){
                $response = array(
                    "status"  => "403",
                    "message" => "KARTU TIDAK VALID",
                    "result"  => '',
                );
                echo json_encode($response);

                $pintuK3 = $_POST['pintukeluar'];
                  
                $updateError1 = "INSERT INTO konfirmasi(pintu, status, waktuk) VALUES('$pintuK3','411',NOW())";
                mysqli_query($con, $updateError1) or die ("data error".mysqli_error());
            }else{
                //todo: validasi token benar
                $jenisMasuk = "SELECT jenis FROM masuk WHERE rfid = '$rfid' LIMIT 1";
                $perintahMasuk = mysqli_query($con, $jenisMasuk) or die ("data error".mysqli_error());
                //todo: mengeluarkan jenis kendaraan dari db
                foreach($perintahMasuk as $i){
                    $jmm = $i['jenis'];
                }
                //todo: validasi jika terdapat jenis kendaraan
                if(isset($jmm)){
                    //todo: validasi jika jenis kendaraan berbeda
                    if($jenisK != $jmm){  
                        $result = array(
                            "status"  => "403",
                            "message" => "KENDARAAN DILARANG KELUAR",
                            "result"  => '',
                        );                            
                            echo json_encode($result);

                            $pintuK10 = $_POST['pintukeluar'];
                  
                            $updateError1 = "INSERT INTO konfirmasi(pintu, status, waktuk) VALUES('$pintuK10','410',NOW())";
                            mysqli_query($con, $updateError1) or die ("data error".mysqli_error());    
                    }else{
                        //todo: validasi jika jenis kendaraan sesuai
                        $masuk = "SELECT kode, jenis, pintu, waktu, nopol, cardnumber, cardtype FROM masuk WHERE rfid = '$rfid' LIMIT 1";
                        $perintah = mysqli_query($con, $masuk) or die ("data error".mysqli_error());

                        //todo: UBAH ---------------------------------
                        $member = "SELECT nama, norfid, produk, akhir, nopol, if((awal <= date(now())) AND (akhir >= date(now())), 'AKTIF','KADALUARSA') as member FROM member WHERE norfid = '$rfid' LIMIT 1 ";
                        $perintah2 = mysqli_query($con, $member) or die ("data error 1 ".mysqli_error());
                        
                    
                        $jk1 = "SELECT nama, tarif60m, tarif1j, tarifmax FROM jeniskendaraan WHERE nama = '$jenisK'";
                        $perintah3 = mysqli_query($con, $jk1) or die ("data error 3 ".mysqli_error());

                        $jum = mysqli_num_rows($perintah);

                        //todo: validasi jika kendaraan ada atau lebih dari 1
                        if($jum > 0){
                            $listMasuk = mysqli_fetch_assoc($perintah);
                            $listJenisKendaraan = mysqli_fetch_assoc($perintah3);
                            var_dump($listJenisKendaraan);

                            $jum1 = mysqli_num_rows($perintah2);
                            $listMember = mysqli_fetch_assoc($perintah2);
                            
                            //todo: mengambil waktu awal dan akhir
                            $waktuAwal = $listMasuk['waktu'];
                            $formatAwal = strtotime($waktuAwal);
                            $waktuSkrg = date("Y-m-d h:i:s");
                            $waktuAkhir = date("Y-m-d h:i:s", time());

                            //todo: mencari selisih, jam dan menit
                            $selisih = time() - $formatAwal;
                            $jam = floor($selisih / (60 * 60));
                            $minutes = $selisih - $jam * (60 * 60);
                            $menit = floor($minutes / 60);

                            //todo: membuat tarif
                            $perJam = $jam * $listJenisKendaraan['tarif1j']; 
                            $tarif = $perJam + $listJenisKendaraan['tarif60m'];
                            
                            //todo: membuat json member
                            if($jum1> 0){
                                $tarifRow = ["Tarif" => $tarif, "Jam Keluar" => $waktuSkrg, "Durasi" => "$jam Jam $menit Menit"];
                                $row = array(
                                        "Kartu" => 'Valid',
                                        "Result" => array("Masuk" => $listMasuk, "Member" => $listMember, "Tarif" => $tarifRow),
                                );

                                //todo: mengambil status member dari db
                                foreach($perintah2 as $m){
                                    $memberOnly = $m["member"];
                                    $akhirKada = $m["akhir"];
                                    $nama = $m["nama"];
                                }

                                $statusMember = $listMember["member"];
                                $kodeMember            = $listMasuk["kode"];
                                $pintuMasuk            = $listMasuk["pintu"];
                                $waktuMasuk            = $listMasuk["waktu"];
                                $jenisKendaraanKeluar  = $jenisK;

                                foreach($perintah as $i){
                                    $jeniskendaraanMasuk1 = $i["jenis"];
                                }
                                $jenisKendaraanMasuk = $jeniskendaraanMasuk1;

                                //todo: validasi jika status adalah member
                                if($memberOnly){
                                    //todo: validasi jika status adalah member aktif
                                    $cekInsert = "SELECT COUNT(kode) AS JumlahKode FROM keluar WHERE kode = '$kodeMember'";
                                    $perintahKeluar1 = mysqli_query($con, $cekInsert) or die ("data error".mysqli_error());
                                    foreach($perintahKeluar1 as $i){
                                        $jumlahKodeKeluar = $i['JumlahKode'];
                                    }

                                    if($memberOnly == "AKTIF"){
                                        $nopolTabelMasuk = mysqli_fetch_assoc($perintah);
                                        foreach($perintah as $i){
                                            $nopolMember = $i["nopol"];
                                        }
    
                                        $platMember = $nopolMember;
                                        $tarif = 0;
                                        $tarifAktif = $tarif;
                                        $tarifRowAktif = ["Tarif" => $tarifAktif, "Jam Keluar" => $waktuSkrg, "Durasi" => "$jam Jam $menit Menit"];
                                        $rowAktif = array(
                                            "Kartu" => 'VALID',
                                            "Result" => array(
                                                              "Masuk" => $listMasuk, 
                                                              "Member" => $listMember, 
                                                              "Tarif"  => $tarifRowAktif
                                                             ),
                                        );
                                        $resultAktif = $rowAktif;
                                        $resultMemberAktif = array(
                                            "status"  => "200",
                                            "message" => "SUCCESS",
                                            "result"  => $resultAktif,
                                        );
                                             
                                        echo json_encode($resultMemberAktif);
                        
                                        $bayarAktif = $tarifRowAktif["Tarif"];

                                        if($jumlahKodeKeluar == 0){
                                            //todo: insert data member aktif
                                            $insertMemberAktif = "INSERT INTO keluar VALUE('$kodeMember','$pintuMasuk','$waktuMasuk',NOW(),'$platMember','$pintuk','$jenisKendaraanKeluar','$jenisKendaraanMasuk','$bayarAktif','$bayarAktif','','','','','','','','','','','$statusMember','','','','','')";
                                            mysqli_query($con, $insertMemberAktif);

                                            $pintuK4 = $_POST['pintukeluar'];
                                            //echo($pintuK4);
                                            
                                            
                                            $updateError1 = "INSERT INTO konfirmasi (id, pintu, status, waktuk, waktum, lama, tarif, saldo, kadaluarsa, nama) VALUES('','$pintuK4','201',NOW(), '$waktuMasuk', '$jam Jam $menit Menit','$tarifAktif','','$akhirKada','$nama')";
                                            //$updateError1 = "UPDATE konfirmasi SET pintu = '$pintuK4', status = '201', waktuk = NOW(), proses = '0', waktum = '$waktuMasuk', lama = '$jam Jam $menit Menit', tarif = '$tarifAktif', saldo = '', kadaluarsa = '$akhirKada', nama = '$nama'  WHERE pintu = '$pintuK4'";
                                            mysqli_query($con, $updateError1) or die ("data error".mysqli_error());
                                            
                                        }else{
                                            //todo: delete data member aktif
                                            $deleteMemberAktif = "DELETE FROM keluar WHERE kode = '$kodeMember'";
                                            mysqli_query($con, $deleteMemberAktif);
                                            
                                            $insertMemberAktifRepeat = "INSERT INTO keluar VALUE('$kodeMember','$pintuMasuk','$waktuMasuk',NOW(),'$platMember','$pintuk','$jenisKendaraanKeluar','$jenisKendaraanMasuk','$bayarAktif','$bayarAktif','','','','','','','','','','','$statusMember','','','','','')";
                                            mysqli_query($con, $insertMemberAktifRepeat);

                                            $pintuK5 = $_POST['pintukeluar'];
                  
                                            $updateError1 = "INSERT INTO konfirmasi (id, pintu, status, waktuk, waktum, lama, tarif, saldo, kadaluarsa, nama) VALUES('','$pintuK5','201',NOW(), '$waktuMasuk', '$jam Jam $menit Menit','$tarifAktif','','$akhirKada','$nama')";
                                            mysqli_query($con, $updateError1) or die ("data error".mysqli_error());
                                        }

                                    }else{
                                            //todo: validasi jika status adalah member kadaluarsa
                                            $result = $row;
                                            $resultMemberKadaluarsa = array(
                                                "status"  => "200",
                                                "message" => "SUCCESS",
                                                "result"  => $result,
                                            );
                                            echo json_encode($resultMemberKadaluarsa);
    
                                            
                                            $statusMember          = $listMember["member"];
                                            $kodeMember            = $listMasuk["kode"];
                                            $pintuMasuk            = $listMasuk["pintu"];
                                            $waktuMasuk            = $listMasuk["waktu"];
                                            $plat                  = $listMember["nopol"];
                                            $jenisKendaraanKeluar  = $jenisK;
                                            foreach($perintah as $i){
                                                $jeniskendaraanMasuk1 = $i["jenis"];
                                            }
                                            $jenisKendaraanMasuk = $jeniskendaraanMasuk1;
                                            $bayarKadaluarsa = $tarifRow["Tarif"];
    
                                            if($jumlahKodeKeluar == 0){
                                                //todo: insert data member kadaluarsa
                                                $insertMemberKadaluarsa = "INSERT INTO keluar VALUE('$kodeMember','$pintuMasuk','$waktuMasuk',NOW(),'$plat','$pintuk','$jenisKendaraanKeluar','$jenisKendaraanMasuk','$bayarKadaluarsa','$bayarKadaluarsa','','','','','','','','','','','$statusMember','','','','','')";
                                                mysqli_query($con, $insertMemberKadaluarsa);
                                                
                                                $pintuK6 = $_POST['pintukeluar'];
                  
                                                $updateError1 = "INSERT INTO konfirmasi (id, pintu, status, waktuk, waktum, lama, tarif, saldo, kadaluarsa, nama) VALUES('','$pintuK6','205',NOW(), '$waktuMasuk', '$jam Jam $menit Menit','$bayarKadaluarsa','','$akhirKada','$nama')";
                                                mysqli_query($con, $updateError1) or die ("data error".mysqli_error());
                                            }else{
                                                //todo: delete data member aktif
                                                $deleteMemberKadaluarsa = "DELETE FROM keluar WHERE kode = '$kodeMember'";
                                                mysqli_query($con, $deleteMemberKadaluarsa);
                                                
                                                $insertMemberKadaluarsa = "INSERT INTO keluar VALUE('$kodeMember','$pintuMasuk','$waktuMasuk',NOW(),'$plat','$pintuk','$jenisKendaraanKeluar','$jenisKendaraanMasuk','$bayarKadaluarsa','$bayarKadaluarsa','','','','','','','','','','','$statusMember','','','','','')";
                                                mysqli_query($con, $insertMemberKadaluarsa);
                                                
                                                $pintuK7 = $_POST['pintukeluar'];
                  
                                                $updateError1 = "INSERT INTO konfirmasi (id, pintu, status, waktuk, waktum, lama, tarif, saldo, kadaluarsa, nama) VALUES('','$pintuK7','205',NOW(), '$waktuMasuk', '$jam Jam $menit Menit','$bayarKadaluarsa','','$akhirKada','$nama')";
                                                mysqli_query($con, $updateError1) or die ("data error".mysqli_error());
                                            }
                                            
                                        }
                                }
                            }else{
                                //todo: umum
                                $umum =  "SELECT rfid FROM masuk WHERE rfid = '$rfid' LIMIT 1";
                                $perintahUmum = mysqli_query($con, $umum) or die ("data error".mysqli_error());

                                $jumlahUmum = mysqli_num_rows($perintahUmum);

                                foreach($perintahUmum as $i){
                                    $rfidUmum = $i['rfid'];
                                }
                                    if($jumlahUmum > 0){
                                        $nopolTabelMasuk = mysqli_fetch_assoc($perintah);
                                        foreach($perintah as $i){
                                            $nopolUmum = $i["nopol"];
                                        }

                                        $tarifRowUmum = ["Tarif" => $tarif, "Jam Keluar" => $waktuSkrg, "Durasi" => "$jam Jam $menit Menit"];
                                        $listUmum = ["nama" => "UMUM", 
                                                     "norfid" => "$rfidUmum",
                                                     "produk" => "",
                                                     "akhir" => "",                                                     
                                                     "nopol" => "$nopolUmum",
                                                     "member" => ""];
                                        $row = array(
                                            "Kartu" => 'VALID',
                                            "Result" => array("Masuk" => $listMasuk, "Member" => $listUmum, "Tarif" => $tarifRowUmum),
                                        );
                                        $result = $row;
                                        $result = array(
                                            "status"  => "200",
                                            "message" => "SUCCESS",
                                            "result"  => $result,
                                        );
                                        echo json_encode($result);

                                        $statusUmum          = $listUmum["nama"];
                                        $kodeUmum            = $listMasuk["kode"];
                                        $pintuMasukUmum          = $listMasuk["pintu"];
                                        $waktuMasukUmum            = $listMasuk["waktu"];
                                        $platUmum                  = $nopolUmum;
                                        $jenisKendaraanKeluarUmum  = $jenisK;
                                        foreach($perintah as $i){
                                            $jeniskendaraanMasuk2 = $i["jenis"];
                                        }
                                        $jenisKendaraanMasukUmum = $jeniskendaraanMasuk2;
                                        $bayarUmum = $tarifRowUmum["Tarif"];

                                        $cekInsert1 = "SELECT COUNT(kode) AS JumlahKode FROM keluar WHERE kode = '$kodeUmum'";
                                        $perintahKeluar2 = mysqli_query($con, $cekInsert1) or die ("data error".mysqli_error());
                                        foreach($perintahKeluar2 as $i){
                                            $jumlahKodeKeluar1 = $i['JumlahKode'];
                                        }

                                        if($jumlahKodeKeluar1 == 0){
                                            //todo: insert data umum
                                            $insertUmum = "INSERT INTO keluar VALUE('$kodeUmum','$pintuMasukUmum','$waktuMasukUmum',NOW(),'$platUmum','$pintuk','$jenisKendaraanKeluarUmum','$jenisKendaraanMasukUmum','$bayarUmum','$bayarUmum','','','','','','','','','','','$statusUmum','','','','','')";
                                            mysqli_query($con, $insertUmum);
                                            
                                            $pintuK8 = $_POST['pintukeluar'];

                                            $updateError1 = "INSERT INTO konfirmasi (id, pintu, status, waktuk, waktum, lama, tarif, saldo, kadaluarsa, nama) VALUES('','$pintuK8','205',NOW(), '$waktuMasukUmum', '$jam Jam $menit Menit','$bayarUmum','','','UMUM')";
                                            //$updateError1 = "UPDATE konfirmasi SET pintu = '$pintuK8', status = '205', waktuk = NOW(), proses = '0', waktum = '$waktuMasukUmum', lama = '$jam Jam $menit Menit', tarif = '$bayarUmum', saldo = '', kadaluarsa = '', nama = 'umum'  WHERE pintu = '$pintuK8'";
                                            mysqli_query($con, $updateError1) or die ("data error".mysqli_error());
                                        }else{
                                            //todo: update data umum aktif
                                            $deleteUmum = "DELETE FROM keluar WHERE kode = '$kodeUmum'";
                                            mysqli_query($con, $deleteUmum);
                                            
                                            $insertUmum = "INSERT INTO keluar VALUE('$kodeUmum','$pintuMasukUmum','$waktuMasukUmum',NOW(),'$platUmum','$pintuk','$jenisKendaraanKeluarUmum','$jenisKendaraanMasukUmum','$bayarUmum','$bayarUmum','','','','','','','','','','','$statusUmum','','','','','')";
                                            mysqli_query($con, $insertUmum);  

                                            $pintuK9 = $_POST['pintukeluar'];
                  
                                            $updateError1 = "INSERT INTO konfirmasi (id, pintu, status, waktuk, waktum, lama, tarif, saldo, kadaluarsa, nama) VALUES('','$pintuK9','205',NOW(), '$waktuMasukUmum', '$jam Jam $menit Menit','$bayarUmum','','','UMUM')";
                                            mysqli_query($con, $updateError1) or die ("data error".mysqli_error());
                                        }
                                }                      
                            }                       
                        }   
                    }                   
                }
            } 
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