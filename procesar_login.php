<?php
// Conexión a la base de datos
$servername = "localhost"; // Cambia según tu configuración
$username = "root";        // Cambia según tu configuración
$password = "";            // Cambia según tu configuración
$dbname = "bdform";        // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_o_correo = $_POST['usuario_o_correo'];
    $password = $_POST['contraseña'];

    // Consultar por nombre de usuario o correo
    $sql = "SELECT id, name, correo, password, rol FROM usuario WHERE name = ? OR correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario_o_correo, $usuario_o_correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró algún registro
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar si la contraseña es correcta
        if (password_verify($password, $user['password'])) {
            // Inicio de sesión exitoso
            $response = [
                "success" => true,
                "message" => "Inicio de sesión exitoso. Bienvenido, " . $user['name'] . "!",
                "user" => $user['name'],
                "rol" => $user['rol']
            ];

            // Puedes actualizar aquí el campo `estatus` a 1 si es necesario
            $update_sql = "UPDATE usuario SET estatus = 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
        } else {
            // Contraseña incorrecta
            $response = [
                "success" => false,
                "message" => "Contraseña incorrecta."
            ];
        }
    } else {
        // Usuario o correo no encontrado
        $response = [
            "success" => false,
            "message" => "Usuario o correo no encontrado."
        ];
    }

    // Devolver la respuesta en formato JSON
    echo json_encode($response);
}

// Cerrar conexión
$conn->close();
?>
