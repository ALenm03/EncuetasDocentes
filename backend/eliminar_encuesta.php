<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdform";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error en la conexión']);
    exit();
}

// Obtener datos enviados por POST
$data = json_decode(file_get_contents('php://input'), true);
$nombreFormulario = $data['nombreFormulario'] ?? null;

if ($nombreFormulario) {
    // Obtener el ID del formulario basado en su nombre
    $stmt = $conn->prepare("SELECT id FROM formulario WHERE nombre_formulario = ? AND id_usuario = ?");
    $stmt->bind_param("si", $nombreFormulario, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $formularioId = $row['id'];

        // Eliminar preguntas asociadas al formulario
        $stmt = $conn->prepare("DELETE FROM formularios WHERE id_formulario = ?");
        $stmt->bind_param("i", $formularioId);
        $stmt->execute();

        // Eliminar el formulario
        $stmt = $conn->prepare("DELETE FROM formulario WHERE id = ?");
        $stmt->bind_param("i", $formularioId);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Formulario no encontrado']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
}

$conn->close();
