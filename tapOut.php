<?php
require 'connect.php';
    date_default_timezone_set("Asia/Jakarta");

    if ($_SERVER['REQUEST_METHOD']=="POST") {

        $rfid = '';
        $jenisK = '';
        $pintuK = '';

        $rfid = trim(isset($_POST['rfid'])) ? $_POST['rfid'] : '';
        $jenisK = trim(isset($_POST['jeniskendaraan'])) ? $_POST['jeniskendaraan'] : '';
        $pintuK = trim(isset($_POST['pintukeluar'])) ? $_POST['pintukeluar'] : '';

        $masuk = "SELECT kode, jenis, pintu, waktu, nopol, cardnumber, cardtype FROM masuk WHERE rfid = '$rfid'";
        $sqlMasuk = mysqli_query($con, $masuk);
        $countMasuk = mysqli_num_rows($sqlMasuk);
        $listMasuk = mysqli_fetch_assoc($sqlMasuk);

        $deleteKeluar = "DELETE FROM keluar WHERE norfid = '$rfid' AND statuspaymentdesc = 'GAGAL' AND date(keluar) = date(NOW())";
        $sqlDelete = mysqli_query($con, $deleteKeluar);


        if($countMasuk < 1){
            $response = array(
                "status"  => "404",
                "message" => "KENDARAAN TIDAK ADA MASUK",
                "result"  => '',
                );
                echo json_encode($response);
                //sleep(2);

                $deleteUmum = "DELETE FROM konfirmasi WHERE pintu = '$pintuK'";
                mysqli_query($con, $deleteUmum);
                
                $insertError1 = "INSERT INTO konfirmasi(pintu, status, waktuk) VALUES('$pintuK','411',NOW())";
                mysqli_query($con, $insertError1);
        }else{
            //todo: ambil semua data dari masuk
            foreach($sqlMasuk as $i){
                $kode = $i['kode'];
                $waktu =$i['waktu'];
                $pintu = $i['pintu'];
                $jenis = $i['jenis'];
                $nopol = $i['nopol'];
                $cardnumber = $i['cardnumber'];
                $cardtype = $i['cardtype'];
            }



            $jenisKendaraan = "SELECT * FROM jeniskendaraan WHERE nama = '$jenisK'";
            $sqlJenisKendaraan = mysqli_query($con, $jenisKendaraan);
            $listJenisKendaraan = mysqli_fetch_assoc($sqlJenisKendaraan);

            $tarifMaximal = $listJenisKendaraan['tarifmax'];
            $intTarifMax = (int)$tarifMaximal;

            $tarif60M = $listJenisKendaraan['tarif60m'];
            $intTarif60M = (int)$tarif60M;

            $tarif1J = $listJenisKendaraan['tarif1j'];
            $intTarif1J = (int)$tarif1J;

            $waktuAwal = $listMasuk['waktu'];
            $formatAwal = strtotime($waktuAwal);
            $waktuSkrg = date("Y-m-d h:i:s");
            $waktuAkhir = date("Y-m-d h:i:s", time());
            $selisih = time() - $formatAwal;
            $jam = floor($selisih) / (60 * 60);
            $intJam = (int)$jam;

            $minutes = $selisih - $intJam * (60 * 60);
            $menit = floor($minutes / 60);
            $intMenit = (int)$menit;

            if($intJam < 4){
                $tarif = $intTarif60M; 
                
            }else if($intJam < 24){
                $tarifJamke1 = $intTarif60M;
                $sisaJam = $intJam - 4;
                $tarifSisaJam = $sisaJam * $intTarif1J;
                $sisaMenit = ($intMenit * 0) + $intTarif1J;
                $tarif = $tarifJamke1 + $sisaMenit + $tarifSisaJam;
                if($tarif >= $intTarifMax){
                    $tarif = $intTarifMax;                                   
                }
            }else{
                // $hari = $intJam / 24;
                // $intHari = (int)$hari;
                // $sisaJamPerHari = $intJam % 24;
                // $tarifMaxTiapHari = $intHari * $intTarifMax;
                // $sisaJamPerTarifJam = $sisaJamPerHari * $intTarif1J;
                // $sisaMenitPerTarifJam = ($intMenit * 0) + $intTarif1J;
                // $tarif = $tarifMaxTiapHari + $sisaJamPerTarifJam + $sisaMenitPerTarifJam;

                $hari = $intJam / 24;
                $intHari = (int)$hari;
                $sisaJamPerHari = $intJam % 24;
                $tarifMaxTiapHari = $intHari * $intTarifMax;

                if ($sisaJamPerHari < 4){
                    $sisaJamPerTarifJam =  $intTarif60M;
                    $sisaMenitPerTarifJam = 0;
                }else{
                    $sisaJamPerTarifJam =  $intTarif60M;
                    $sisaMenitPerTarifJam = ($sisaJamPerHari -3) * $intTarif1J ;
                }
                $tarif = $tarifMaxTiapHari + $sisaJamPerTarifJam + $sisaMenitPerTarifJam;

                
            }

            $tarifRowTotal = ["Tarif" => $tarif, "Jam Keluar" => $waktuSkrg, "Durasi" => "$intJam Jam $intMenit Menit"];
            
            $member = "SELECT * FROM member WHERE norfid = '$rfid'";
            $sqlMember = mysqli_query($con, $member);
            $countMember = mysqli_num_rows($sqlMember);



            if($countMember > 0){
                $ambilMember = "SELECT nama, norfid, produk, akhir, nopol, status, jeniskendaraan, if((awal <= date(now())) AND (akhir >= date(now())), 'AKTIF','KADALUARSA') as member FROM member WHERE norfid = '$rfid' LIMIT 1";
                $sqlStatusMember = mysqli_query($con, $ambilMember);
                $listMember = mysqli_fetch_assoc($sqlStatusMember);
                foreach($sqlStatusMember as $i){
                    $statusMember = $i['member'];
                    $statusJenisKendaraan = $i['jeniskendaraan'];
                    $statusStatus = $i['status'];
                    $akhirKada = $i['akhir'];
                }
                
                

                if($statusMember == 'AKTIF'){
                    if($statusJenisKendaraan == $jenisK){
                        //todo: query

                        $insert = "INSERT INTO keluar (kode, pintum, masuk, keluar, plat, pintuk, jenisk, jenism, bayar, norfid, paket, kadaluarsa) VALUES('$kode', '$pintu', '$waktu', NOW(), '$nopol', '$pintuK', '$jenisK', '$jenis', '0', '$rfid', '$statusStatus', '$akhirKada')";
                        $sqlInsert = mysqli_query($con, $insert);

                        // test
                        // $delete = "DELETE konfirmasi WHERE pintu = '$pintuK'";
                        // //
                        // $sqlDelete = mysqli_query($con, $delete);

                        $insertKon = "INSERT INTO konfirmasi (pintu, proses, waktum, waktuk, lama, tarif, saldo, kadaluarsa, nama, status, pesan, kode) VALUES('$pintuK','','$waktu', NOW(), '$intJam Jam $intMenit Menit','0', '0', '$akhirKada', 'tes1','tes2','tes3','$kode')";
                        $sqlInsertKon = mysqli_query($con, $insertKon) or die ("data error");

                        $tarifRowTotal["Tarif"] = 0;
                        $row = array(
                            "Kartu" => "Valid",
                            "Result" => array(
                                "Masuk" => $listMasuk,
                                "Member" => $listMember,
                                "Tarif" => $tarifRowTotal,
                            )
                        );
                        $result = array(
                            "status" => "200",
                            "message" => "SUCCESS",
                            "result" => $row
                        );
                        echo json_encode($result);

                        //sleep(2);
                    }else{
                        $insert = "INSERT INTO keluar (kode, pintum, masuk, keluar, plat, pintuk, jenisk, jenism, bayar, norfid, paket, kadaluarsa) VALUES('$kode', '$pintu', '$waktu', NOW(), '$nopol', '$pintuK', '$jenisK', '$jenis', '$tarif', '$rfid', '$statusStatus', '$akhirKada')";
                        $sqlInsert = mysqli_query($con, $insert);

                        // test
                        // $delete = "DELETE konfirmasi WHERE pintu = '$pintuK' ";
                        // //
                        // $sqlDelete = mysqli_query($con, $delete);

                        $insertKon = "INSERT INTO konfirmasi (pintu, proses, waktum, waktuk, lama, tarif, saldo, kadaluarsa, nama, status, pesan, kode) VALUES('$pintuK','','$waktu', NOW(), '$intJam Jam $intMenit Menit','0', '0', '$akhirKada', 'tes1','tes2','tes3','$kode')";
                        $sqlInsertKon = mysqli_query($con, $insertKon) or die ("data error");

                    

                        $row = array(
                            "Kartu" => "Valid",
                            "Result" => array(
                                "Masuk" => $listMasuk,
                                "Member" => $listMember,
                                "Tarif" => $tarifRowTotal,
                            )
                        );
                        $result = array(
                            "status" => "200",
                            "message" => "MEMBER BEDA JENIS KENDARAAN",
                            "result" => $row
                        );
                        echo json_encode($result);
                        //sleep(2);
                    }

                }else{


                    $insert = "INSERT INTO keluar (kode, pintum, masuk, keluar, plat, pintuk, jenisk, jenism, bayar, norfid, paket, kadaluarsa) VALUES('$kode', '$pintu', '$waktu', NOW(), '$nopol', '$pintuK', '$jenisK', '$jenis', '$tarif', '$rfid', '$statusStatus', '$akhirKada')";
                    $sqlInsert = mysqli_query($con, $insert);

                    // test
                    // $delete = "DELETE konfirmasi WHERE pintu = '$pintuK'";
                    // //
                    // $sqlDelete = mysqli_query($con, $delete);

                    $insertKon = "INSERT INTO konfirmasi (pintu, proses, waktum, waktuk, lama, tarif, saldo, kadaluarsa, nama, status, pesan, kode) VALUES('$pintuK','','$waktu', NOW(), '$intJam Jam $intMenit Menit','0', '0', '$akhirKada', 'tes1','tes2','tes3','$kode')";
                    $sqlInsertKon = mysqli_query($con, $insertKon) or die ("data error");
                    
                    $row = array(
                        "Kartu" => "Valid",
                        "Result" => array(
                            "Masuk" => $listMasuk,
                            "Member" => $listMember,
                            "Tarif" => $tarifRowTotal,
                        )
                    );
                    $result = array(
                        "status" => "200",
                        "message" => "SUCCESS",
                        "result" => $row
                    );
                    echo json_encode($result);
                    //sleep(2);
                }
            }else{
                

                $insert = "INSERT INTO keluar (kode, pintum, masuk, keluar, plat, pintuk, jenisk, jenism, bayar, norfid, paket, kadaluarsa) VALUES('$kode', '$pintu', '$waktu', NOW(), '', '$pintuK', '$jenisK', '$jenis', '$tarif', '$rfid', '', '')";
                $sqlInsert = mysqli_query($con, $insert);

                // test
                // $delete = "DELETE konfirmasi WHERE pintu = '$pintuK' ";
                // //
                // $sqlDelete = mysqli_query($con, $delete);

                $insertKon = "INSERT INTO konfirmasi (pintu, proses, waktum, waktuk, lama, tarif, saldo, kadaluarsa, nama, status, pesan, kode) VALUES('$pintuK','','$waktu', NOW(), '$intJam Jam $intMenit Menit','0', '0', '', 'tes1','tes2','tes3','$kode')";
                $sqlInsertKon = mysqli_query($con, $insertKon) or die ("data error");

                $listUmum = array(
                    "nama" => "UMUM",
                    "norfid" => "",
                    "produk" => "",
                    "akhir" => "",
                    "nopol" => "",
                    "member" => ""
                );
                $row = array(
                    "Kartu" => "Valid",
                    "Result" => array(
                        "Masuk" => $listMasuk,
                        "Member" => $listUmum,
                        "Tarif" => $tarifRowTotal,
                    )
                );
                $result = array(
                    "status" => "200",
                    "message" => "SUCCESS",
                    "result" => $row
                );
                echo json_encode($result);
                //sleep(2);
            }
        }

    }
?>