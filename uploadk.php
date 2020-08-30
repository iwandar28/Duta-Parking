<?php
//include "config.php";
$fileName = $_FILES['picture']['name'];

$move = move_uploaded_file($_FILES['picture']['tmp_name'], '/var/www/html/DutaParkir/images/keluar/k_'.$fileName);

if($move){
echo "TERKIRIM";
echo $fileName;
echo "TERKIRIM";

} else{
echo "GAGAL";
}

?>
