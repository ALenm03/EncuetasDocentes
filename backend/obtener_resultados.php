<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdform";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos"]);
    exit();
}

// Verificar si se recibió el ID del evento
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id_evento'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID del evento no proporcionado", "data" => $data]);
    exit();
}

$id_evento = intval($_POST['id_evento']);

// Obtener respuestas relacionadas al evento
$sql = "SELECT r.id_usuario, r.pregunta_num, r.respuesta 
        FROM respuestas r
        WHERE r.id_evento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_evento);
$stmt->execute();
$result = $stmt->get_result();

// Convertir resultados en un arreglo
$respuestas = [];
while ($row = $result->fetch_assoc()) {
    $respuestas[] = $row;
}

echo json_encode($respuestas);
$conn->close();
?>
