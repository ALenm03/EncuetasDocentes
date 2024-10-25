<?php
ini_set('display_errors', 0); // Desactiva la visualización de errores
error_reporting(0); // No reportar errores
session_start();

require 'db2.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Obtener datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);
$nombreFormulario = $data['nombreFormulario'] ?? '';
$idUsuario = $_SESSION['user_id'];

if (empty($nombreFormulario)) {
    echo json_encode(['success' => false, 'message' => 'No se recibió el nombre del formulario']);
    exit();
}

// Preparar la consulta para eliminar
$sql = "DELETE FROM formularios WHERE nombre_formulario = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param("si", $nombreFormulario, $idUsuario);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Encuesta eliminada con éxito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró ninguna encuesta para eliminar']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
