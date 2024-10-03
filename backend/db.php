<?php
$servername = "localhost";
$username = "root";       
$password = "";            
$dbname = "bdform";        

try {
    // Crear la conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configurar el modo de error de PDO a excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Manejo de errores
    echo "Error de conexión: " . $e->getMessage();
    exit();
}
?>
