<?php
// Iniciar sesión
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado.']);
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
    die(json_encode(['status' => 'error', 'message' => 'Error al conectar a la base de datos.']));
}

// Obtener datos enviados desde el frontend
$formData = json_decode($_POST['formData'], true);

if (!is_array($formData) || !isset($formData['nombre_formulario'], $formData['nombre_original'], $formData['preguntas'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos inválidos enviados.']);
    exit();
}

$nombreFormulario = $formData['nombre_formulario'];
$nombreOriginal = $formData['nombre_original'];
$preguntas = $formData['preguntas'];
$userId = $_SESSION['user_id'];

// Validar si el nuevo nombre del formulario ya existe para el mismo usuario
$stmt = $conn->prepare("SELECT 1 FROM formularios WHERE nombre_formulario = ? AND id_usuario = ?");
$stmt->bind_param("si", $nombreFormulario, $userId);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0 && $nombreFormulario !== $nombreOriginal) {
    echo json_encode(['status' => 'error', 'message' => 'El nombre del formulario ya existe.']);
    exit();
}

// 1. Actualizar el nombre del formulario
$stmt = $conn->prepare("UPDATE formularios SET nombre_formulario = ? WHERE nombre_formulario = ? AND id_usuario = ?");
$stmt->bind_param("ssi", $nombreFormulario, $nombreOriginal, $userId);
$stmt->execute();

if ($stmt->error) {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el nombre del formulario: ' . $stmt->error]);
    exit();
}

// 2. Procesar cada pregunta
foreach ($preguntas as $index => $pregunta) {
    $preguntaTexto = $pregunta['pregunta'];
    $tipoRespuesta = $pregunta['tipo_respuesta'];
    $respuestas = isset($pregunta['respuestas']) ? $pregunta['respuestas'] : [null, null, null, null];

    $respuesta1 = isset($respuestas[0]) ? $respuestas[0] : null;
    $respuesta2 = isset($respuestas[1]) ? $respuestas[1] : null;
    $respuesta3 = isset($respuestas[2]) ? $respuestas[2] : null;
    $respuesta4 = isset($respuestas[3]) ? $respuestas[3] : null;

    $preguntaNum = $index + 1;

    $stmt = $conn->prepare("SELECT pregunta_num FROM formularios WHERE nombre_formulario = ? AND id_usuario = ? AND pregunta_num = ?");
    $stmt->bind_param("sii", $nombreFormulario, $userId, $preguntaNum);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Pregunta existe: Actualizar
        $stmt = $conn->prepare("
            UPDATE formularios 
            SET pregunta = ?, tipo_respuesta = ?, respuesta_1 = ?, respuesta_2 = ?, respuesta_3 = ?, respuesta_4 = ? 
            WHERE nombre_formulario = ? AND id_usuario = ? AND pregunta_num = ?");
        $stmt->bind_param(
            "ssssssssi",
            $preguntaTexto,
            $tipoRespuesta,
            $respuesta1,
            $respuesta2,
            $respuesta3,
            $respuesta4,
            $nombreFormulario,
            $userId,
            $preguntaNum
        );
        $stmt->execute();
    } else {
        // Pregunta nueva: Insertar
        $stmt = $conn->prepare("
            INSERT INTO formularios (nombre_formulario, id_usuario, pregunta_num, pregunta, tipo_respuesta, respuesta_1, respuesta_2, respuesta_3, respuesta_4) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "siissssss",
            $nombreFormulario,
            $userId,
            $preguntaNum,
            $preguntaTexto,
            $tipoRespuesta,
            $respuesta1,
            $respuesta2,
            $respuesta3,
            $respuesta4
        );
        $stmt->execute();
    }

    if ($stmt->error) {
        echo json_encode(['status' => 'error', 'message' => 'Error al procesar las preguntas: ' . $stmt->error]);
        exit();
    }
}

// 3. Eliminar preguntas no incluidas en la edición
$numeroPreguntas = count($preguntas);
$stmt = $conn->prepare("
    DELETE FROM formularios 
    WHERE nombre_formulario = ? AND id_usuario = ? AND pregunta_num > ?");
$stmt->bind_param("sii", $nombreFormulario, $userId, $numeroPreguntas);
$stmt->execute();

if ($stmt->error) {
    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar preguntas antiguas: ' . $stmt->error]);
    exit();
}

// Respuesta de éxito
echo json_encode(['status' => 'success', 'message' => 'Formulario actualizado correctamente.']);
?>
