<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdform";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$nombreFormulario = $_POST['nombre_formulario'];
$userId = $_SESSION['user_id'];

// Preparar consultas para insertar o actualizar respuestas
$selectSql = "SELECT id FROM respuestas WHERE formulario = ? AND pregunta = ? AND id_usuario = ?";
$insertSql = "INSERT INTO respuestas (formulario, pregunta, respuesta, id_usuario) VALUES (?, ?, ?, ?)";
$updateSql = "UPDATE respuestas SET respuesta = ? WHERE id = ?";

$selectStmt = $conn->prepare($selectSql);
$insertStmt = $conn->prepare($insertSql);
$updateStmt = $conn->prepare($updateSql);

// Recorrer las respuestas del formulario
foreach ($_POST as $key => $respuesta) {
    if (strpos($key, 'respuesta_') === 0) {
        $preguntaNum = str_replace('respuesta_', '', $key);

        // Si la respuesta es un array (checkbox), convierte en cadena
        if (is_array($respuesta)) {
            $respuesta = implode(", ", $respuesta);
        }

        // Comprobar si ya existe una respuesta para esta pregunta y usuario
        $selectStmt->bind_param("ssi", $nombreFormulario, $preguntaNum, $userId);
        $selectStmt->execute();
        $selectResult = $selectStmt->get_result();

        if ($selectResult->num_rows > 0) {
            // Si ya existe, actualizar la respuesta
            $row = $selectResult->fetch_assoc();
            $respuestaId = $row['id'];
            $updateStmt->bind_param("si", $respuesta, $respuestaId);
            $updateStmt->execute();
        } else {
            // Si no existe, insertar una nueva respuesta
            $insertStmt->bind_param("sssi", $nombreFormulario, $preguntaNum, $respuesta, $userId);
            $insertStmt->execute();
        }
    }
}

// Cerrar conexiones y redirigir al usuario
$selectStmt->close();
$insertStmt->close();
$updateStmt->close();
$conn->close();

header("Location: /EncuetasDocentes/admin.php");
exit();
?>
