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

// Consultar las respuestas
$sql = "SELECT f.pregunta, r.respuesta
        FROM respuestas r
        JOIN formularios f ON r.pregunta_num = f.pregunta_num AND r.id_formulario = f.id_formulario
        WHERE r.id_usuario = ? AND r.id_evento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $eventId);
$stmt->execute();
$result = $stmt->get_result();

// Generar tabla de respuestas
if ($result->num_rows > 0) {
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Pregunta</th><th>Respuesta</th></tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['pregunta']) . "</td>";
        echo "<td>" . htmlspecialchars($row['respuesta']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No se encontraron respuestas.</p>";
}
?>
