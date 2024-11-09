<?php
$servername = "localhost"; 
$username = "naco";        
$password = "nacox2";           
$dbname = "bdform";  

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
echo "Conexión exitosa."; // Esto debe aparecer si la conexión es exitosa

?>
