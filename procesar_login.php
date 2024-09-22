<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mi_base_de_datos";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$usuario = $_POST['usuario_o_correo'];
$contraseña = $_POST['contraseña'];

// Escapar los valores para evitar inyecciones SQL
$usuario = $conn->real_escape_string($usuario);
$contraseña = $conn->real_escape_string($contraseña);

// Consulta a la base de datos
$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' OR correo = '$usuario'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Usuario encontrado
    $row = $result->fetch_assoc();
    
    // Verificar la contraseña
    if (password_verify($contraseña, $row['contraseña'])) {
        // Inicio de sesión exitoso
        $rol = $row['rol']; // Obtener el rol del usuario
        
        if ($rol == 'admin') {
            // Redirigir a la página de administrador
            echo json_encode(array("success" => true, "redirect" => "admin.html"));
        } else {
            // Redirigir a la página de usuario normal
            echo json_encode(array("success" => true, "redirect" => "usuario_normal.html"));
        }
    } else {
        // Contraseña incorrecta
        echo json_encode(array("success" => false, "message" => "Contraseña incorrecta."));
    }
} else {
    // Usuario no encontrado
    echo json_encode(array("success" => false, "message" => "Usuario no encontrado."));
}

// Cerrar la conexión
$conn->close();
?>
