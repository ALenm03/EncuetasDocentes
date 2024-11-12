<?php
// Configuración de conexión a la base de datos
$servername = "localhost"; 
$username = "NACO";        
$password = "NACOX2";           
$dbname = "bdform";    

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Encriptar la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Definir el rol por defecto como "user"
    $rol = 'user';

    // Insertar el usuario en la base de datos
    $sql = "INSERT INTO usuario (name, password, rol, estatus, correo) VALUES (?, ?, ?, 0, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $hashed_password, $rol, $email);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Usuario agregado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al registrar usuario"]);
    }

    $stmt->close();
    $conn->close();
}
?>
