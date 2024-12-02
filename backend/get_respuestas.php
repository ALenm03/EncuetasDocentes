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

// Consultar el nombre del formulario
$formularioQuery = "SELECT f.nombre_formulario 
                    FROM formulario f
                    JOIN respuestas r ON r.id_formulario = f.id
                    WHERE r.id_evento = ? LIMIT 1";
$stmt = $conn->prepare($formularioQuery);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$formularioResult = $stmt->get_result();
$formulario = $formularioResult->fetch_assoc();

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

// Mostrar nombre del formulario
echo "<h5>Formulario: " . htmlspecialchars($formulario['nombre_formulario']) . "</h5>";

// Generar tabla de respuestas
if ($result->num_rows > 0) {
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Pregunta</th><th>Respuesta</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['pregunta']) . "</td>";
        echo "<td>";

        if ($row['tipo_respuesta'] === 'parrafo') {
            // Mostrar solo la respuesta proporcionada
            echo "<p> " . htmlspecialchars($row['respuesta']) . "</p>";
        } elseif ($row['tipo_respuesta'] === 'checkbox') {
            // Resaltar respuestas seleccionadas (tipo checkbox)
            $respuestasSeleccionadas = json_decode($row['respuesta'], true) ?? [];
            echo "<ul>";
            for ($i = 1; $i <= 4; $i++) {
                $respuestaKey = "respuesta_$i";
                if (!empty($row[$respuestaKey])) {
                    $esSeleccionada = in_array($row[$respuestaKey], $respuestasSeleccionadas) ? "<span class='text-success'>(Seleccionada)</span>" : "";
                    echo "<li>" . htmlspecialchars($row[$respuestaKey]) . " $esSeleccionada</li>";
                }
            }
            echo "</ul>";
        } else {
            // Resaltar la opción seleccionada (tipo opción múltiple)
            echo "<ul>";
            for ($i = 1; $i <= 4; $i++) {
                $respuestaKey = "respuesta_$i";
                if (!empty($row[$respuestaKey])) {
                    $esSeleccionada = ($row['respuesta'] === $row[$respuestaKey]) ? "<span class='text-success'>(Seleccionada)</span>" : "";
                    echo "<li>" . htmlspecialchars($row[$respuestaKey]) . " $esSeleccionada</li>";
                }
            }
            echo "</ul>";
        }

        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No se encontraron respuestas.</p>";
}
?>
