<?php

require 'koneksiBaru.php';

date_default_timezone_set("Asia/Jakarta");

if($_SERVER['REQUEST_METHOD']=="POST"){

    $rfid = '';
    $jenisK = '';
    $pintuK = '';

    $rfid = trim(isset($_POST['rfid'])) ? $_POST['rfid'] : '';
    $pintuk = trim(isset($_POST['pintukeluar'])) ? $_POST['pintukeluar'] : '';
    $jenisK = trim(isset($_POST['jeniskendaraan'])) ? $_POST['jeniskendaraan'] : '';



    //todo: sql
    //ambil rfid
    $masuk = "SELECT rfid FROM masuk WHERE rfid = '$rfid' LIMIT 1";
    $sqlMasuk = mysqli_query($con, $masuk) or die ("data error".mysqli_error());
    
    //ambil data utk json
    $data = "SELECT kode, jenis, pintu, waktu, nopol, cardnumber, cardtype FROM masuk WHERE rfid = '$rfid' LIMIT 1";
    $sqlData = mysqli_query($con, $data) or die ("data error".mysqli_error());

    //ambil status member aktif atau kadaluarsa
    $statusMember = "SELECT nama, norfid, produk, akhir, nopol, if((awal <= date(now())) AND (akhir >= date(now())), 'AKTIF','KADALUARSA') as member FROM member WHERE norfid = '$rfid' LIMIT 1 ";
    $sqlStatusMember = mysqli_query($con, $statusMember) or die ("data error 1 ".mysqli_error());

    //ambil data jenis kendaraan
    $jenisKendaraan = "SELECT nama, tarif60m, tarif1j, tarifmax FROM jeniskendaraan WHERE nama = '$jenisK'";
    $sqlJenisKendaraan = mysqli_query($con, $jenisKendaraan);
    

    //----------------------------------------------------------------------------------------------------------------------------------------------


    //todo: hitung jumlah yg ada
    //hitung jumlah kendaraan masuk
    $jumlahMasuk = mysqli_num_rows($sqlMasuk);

    //hitung jumlah member
    $jumlahMember = mysqli_num_rows($sqlStatusMember);


    //----------------------------------------------------------------------------------------------------------------------------------------------


    //todo: ambil list data tiap kolom
    //data kendaraan masuk
    $listDataMasuk = mysqli_fetch_assoc($sqlData);

    //data member
    $listDataMember = mysqli_fetch_assoc($sqlStatusMember);
    

    //data jenis kendaraan
    $listJenisKendaraan = mysqli_fetch_assoc($sqlJenisKendaraan);

    

    //----------------------------------------------------------------------------------------------------------------------------------------------


    //todo: perhitungan tarif
    //ambil waktu awal dan akhir dari data masuk
    $waktuAwal = $listDataMasuk['waktu'];
    $formatAwal = strtotime($waktuAwal);
    $waktuSkrg = date("Y-m-d h:i:s");
    $waktuAkhir = date("Y-m-d h:i:s", time());
    $selisih = time() - $formatAwal;
    $jam = floor($selisih / (60 * 60));
    $minutes = $selisih - $jam * (60 * 60);
    $menit = floor($minutes / 60);
    $perJam = $jam * $listJenisKendaraan['tarif1j']; 
    $tarif = $perJam + $listJenisKendaraan['tarif60m'];


    //----------------------------------------------------------------------------------------------------------------------------------------------


    //todo: mulai validasi dan json
    if($jumlahMasuk <= 0){
        $response = array(
            "status"  => "404",
            "message" => "KENDARAAN TIDAK TERDAFTAR",
            "result"  => '',
        );
        echo json_encode($response);
    }else if($jumlahMember > 0){

        $tarifRow = ["Tarif" => $tarif, "Jam Keluar" => $waktuSkrg, "Durasi" => "$jam Jam $menit Menit"];
        $row = array(
            "Kartu" => 'Valid',
            "Result" => array("Masuk" => $listDataMasuk, "Member" => $listDataMember, "Tarif" => $tarifRow),
    );
        //memecah data member dari db
        foreach($sqlStatusMember as $m){
            $memberOnly = $m["member"];
            $akhirKada = $m["akhir"];
            $nama = $m["nama"];
        }

        $statusMember          = $listDataMember["member"];
        $kodeMember            = $listDataMasuk["kode"];
        $pintuMasuk            = $listDataMasuk["pintu"];
        $waktuMasuk            = $listDataMasuk["waktu"];
        $jenisKendaraanKeluar  = $jenisK;

        //jenis kendaraan masuk
        foreach($sqlData as $i){
            $jeniskendaraanMasuk1 = $i["jenis"];
        }
        $jenisKendaraanMasuk = $jeniskendaraanMasuk1;
        
        if($memberOnly){
            json_encode("member aktif");
        }
    }

}else{
	$response = array(
    'status' => '403',
    'message' => 'ACCESS FORBIDDEN',
    'result'  => '',
	);
    header('Content-Type: application/json');
    echo json_encode($response);
}

?>