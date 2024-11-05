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

// Iniciar la sesión
session_start();

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
            // Guardar información del usuario en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['rol'];
            
            // Actualizar el campo estatus a 1
            $update_sql = "UPDATE usuario SET estatus = 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();

            // Redirigir según el rol del usuario
            if ($user['rol'] == 'admin') {
                $response = [
                    "success" => true,
                    "message" => "Inicio de sesión exitoso. Bienvenido, " . $user['name'] . "!",
                    "redirect" => "admin.php"
                ];
            } elseif ($user['rol'] == 'user') {
                $response = [
                    "success" => true,
                    "message" => "Inicio de sesión exitoso. Bienvenido, " . $user['name'] . "!",
                    "redirect" => "usuario_normal.php"
                ];
            }
            echo json_encode($response);
            exit();
        }
    } else {
        // Usuario o correo no encontrado
        $response = [
            "success" => false,
            "message" => "El usuario o contraseña son incorrectos"
        ];
        echo json_encode($response);
    }
}

// Cerrar la conexión
$conn->close();
?>
