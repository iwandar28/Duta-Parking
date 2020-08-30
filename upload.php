<?php
//include "config.php";
$fileName = $_FILES['picture']['name'];

//$move = move_uploaded_file($_FILES['picture']['tmp_name'], 'd:\xampp\htdocs\server_parkir\images\masuk\m_'.$fileName);
$move = move_uploaded_file($_FILES['picture']['tmp_name'], '/var/www/html/DutaParkir/images/masuk/m_'.$fileName);
//$cmd = "chmod 777 -R /opt/lampp/htdocs/server_parkir/images/masuk";
//$perintah = exec($cmd); 

//$cmd = "chmod 777 -R /var/www/DutaParkir/images/masuk";
//$perintah = exec($cmd);
if($move){
echo "TERKIRIM";
echo $fileName;
echo "TERKIRIM";

} else{
echo "GAGAL";
}

?>
