<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdform";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener parámetros
$userId = $_GET['user_id'] ?? 0;
$eventId = $_GET['event_id'] ?? 0;

// Consultar las respuestas del usuario
$sql = "SELECT f.pregunta, f.respuesta_1, f.respuesta_2, f.respuesta_3, f.respuesta_4, 
               f.tipo_respuesta, r.respuesta
        FROM respuestas r
        JOIN formularios f ON r.pregunta_num = f.pregunta_num AND r.id_formulario = f.id_formulario
        WHERE r.id_usuario = ? AND r.id_evento = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $eventId);
$stmt->execute();
$result = $stmt->get_result();

// Inicializar un array para contar las respuestas seleccionadas
$respuestasContadas = [
    'respuesta_1' => 0,
    'respuesta_2' => 0,
    'respuesta_3' => 0,
    'respuesta_4' => 0,
];

// Contar las respuestas seleccionadas solo de tipo opción múltiple
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Solo contar las respuestas de tipo 'opción múltiple'
        if ($row['tipo_respuesta'] === 'opcion_multiple') {
            // Verificar cuál fue la respuesta seleccionada
            if ($row['respuesta'] === $row['respuesta_1']) {
                $respuestasContadas['respuesta_1']++;
            } elseif ($row['respuesta'] === $row['respuesta_2']) {
                $respuestasContadas['respuesta_2']++;
            } elseif ($row['respuesta'] === $row['respuesta_3']) {
                $respuestasContadas['respuesta_3']++;
            } elseif ($row['respuesta'] === $row['respuesta_4']) {
                $respuestasContadas['respuesta_4']++;
            }
        }
    }
}

// Pasar las respuestas al frontend para graficar
echo json_encode($respuestasContadas);
?>
