<?php
$host = 'localhost';
$user = 'root';
$password = 'admin';
$dbase = 'barbearia';

$conn = new mysqli($host, $user,$password, $dbase);

if($conn->connect_error == 0){
    
} else{
    echo "não conectado";
    exit;
}
?>