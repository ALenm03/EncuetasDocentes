<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Tu usuario de MySQL
$password = ""; // Tu contraseña de MySQL
$dbname = "nombre_base_de_datos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener usuario y contraseña del formulario
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Consulta a la base de datos
$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contraseña = '$contraseña'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Usuario y contraseña correctos
    echo json_encode(array("success" => true, "message" => "Inicio de sesión exitoso."));
} else {
    // Usuario o contraseña incorrectos
    echo json_encode(array("success" => false, "message" => "Usuario o contraseña incorrectos."));
}

$conn->close();
?>
