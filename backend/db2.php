<?php
$servername = "localhost"; 
$username = "naco";        
$password = "nacox2";           
$dbname = "bdform";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Si hay un error de conexión, no imprimas en pantalla
    // Solo devuelves un mensaje JSON en eliminar_encuesta.php
    error_log("Error de conexión: " . $conn->connect_error); // Registrar en lugar de imprimir
}
?>
