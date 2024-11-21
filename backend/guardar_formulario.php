<?php
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

// Recibir los datos del formulario
$formData = json_decode($_POST['formData'] ?? '', true);
$nombre_formulario = $formData['nombre_formulario'] ?? '';
$preguntas = $formData['preguntas'] ?? [];

// Comprobar si se han recibido datos
if (empty($nombre_formulario) || empty($preguntas)) {
    die("Error: El nombre del formulario y las preguntas son obligatorios.");
}

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    die("Error: Usuario no autenticado.");
}
$id_usuario = $_SESSION['user_id'];

// Verificar si el nombre del formulario ya existe para el usuario
$stmtCheck = $conn->prepare("SELECT COUNT(*) FROM formulario WHERE nombre_formulario = ? AND id_usuario = ?");
$stmtCheck->bind_param("si", $nombre_formulario, $id_usuario);
$stmtCheck->execute();
$stmtCheck->bind_result($count);
$stmtCheck->fetch();
$stmtCheck->close();

if ($count > 0) {
    die("Error: Ya existe un formulario con el nombre '$nombre_formulario' para este usuario.");
}

// Iniciar una transacción
$conn->begin_transaction();

try {
    // Insertar el formulario en la tabla `formulario`
    $stmtFormulario = $conn->prepare("INSERT INTO formulario (nombre_formulario, id_usuario) VALUES (?, ?)");
    $stmtFormulario->bind_param("si", $nombre_formulario, $id_usuario);
    $stmtFormulario->execute();
    $formulario_id = $conn->insert_id; // Obtener el ID del formulario recién creado
    $stmtFormulario->close();

    // Preparar la consulta para insertar las preguntas en la tabla `formularios`
    $stmtPreguntas = $conn->prepare("INSERT INTO formularios (id_formulario, pregunta_num, pregunta, respuesta_1, respuesta_2, respuesta_3, respuesta_4, tipo_respuesta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($preguntas as $index => $preguntaData) {
        $pregunta_num = $index + 1; // Número de la pregunta
        $pregunta = $preguntaData['pregunta'] ?? '';

        // Manejar respuestas
        $respuesta_1 = $preguntaData['respuestas'][0] ?? null;
        $respuesta_2 = $preguntaData['respuestas'][1] ?? null;
        $respuesta_3 = $preguntaData['respuestas'][2] ?? null;
        $respuesta_4 = $preguntaData['respuestas'][3] ?? null;

        // Tipo de respuesta
        $tipo_respuesta = $preguntaData['tipo_respuesta'] ?? '';

        // Insertar la pregunta en la tabla `formularios`
        $stmtPreguntas->bind_param("iissssss", $formulario_id, $pregunta_num, $pregunta, $respuesta_1, $respuesta_2, $respuesta_3, $respuesta_4, $tipo_respuesta);
        $stmtPreguntas->execute();
    }

    $stmtPreguntas->close();

    // Confirmar la transacción
    $conn->commit();
    echo "Formulario guardado con éxito.";
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    die("Error: No se pudo guardar el formulario. " . $e->getMessage());
}

// Cerrar la conexión
$conn->close();
?>
