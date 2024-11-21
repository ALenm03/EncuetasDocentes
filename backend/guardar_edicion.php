<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado.']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdform";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Error al conectar a la base de datos.']));
}

$formData = json_decode($_POST['formData'], true);

if (!is_array($formData) || !isset($formData['nombre_formulario'], $formData['nombre_original'], $formData['preguntas'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos inválidos enviados.']);
    exit();
}

$nombreFormulario = $formData['nombre_formulario'];
$nombreOriginal = $formData['nombre_original'];
$preguntas = $formData['preguntas'];
$userId = $_SESSION['user_id'];

// Obtener el ID del formulario
$stmt = $conn->prepare("SELECT id FROM formulario WHERE nombre_formulario = ? AND id_usuario = ?");
$stmt->bind_param("si", $nombreOriginal, $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Formulario no encontrado.']);
    exit();
}

$idFormulario = $row['id'];

// Iniciar una transacción
$conn->begin_transaction();

try {
    // Actualizar el nombre del formulario (si cambió)
    if ($nombreFormulario !== $nombreOriginal) {
        $stmt = $conn->prepare("UPDATE formulario SET nombre_formulario = ? WHERE id = ?");
        $stmt->bind_param("si", $nombreFormulario, $idFormulario);
        $stmt->execute();

        if ($stmt->error) {
            throw new Exception("Error al actualizar el nombre del formulario: " . $stmt->error);
        }
    }

    // Procesar cada pregunta
    foreach ($preguntas as $index => $pregunta) {
        $preguntaTexto = $pregunta['pregunta'];
        $tipoRespuesta = $pregunta['tipo_respuesta'];
        $respuestas = isset($pregunta['respuestas']) ? $pregunta['respuestas'] : [null, null, null, null];

        $respuesta1 = isset($respuestas[0]) ? $respuestas[0] : null;
        $respuesta2 = isset($respuestas[1]) ? $respuestas[1] : null;
        $respuesta3 = isset($respuestas[2]) ? $respuestas[2] : null;
        $respuesta4 = isset($respuestas[3]) ? $respuestas[3] : null;

        $preguntaNum = $index + 1;

        // Verificar si la pregunta ya existe
        $stmt = $conn->prepare("SELECT pregunta_num FROM formularios WHERE id_formulario = ? AND pregunta_num = ?");
        $stmt->bind_param("ii", $idFormulario, $preguntaNum);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Pregunta existe: Actualizar
            $stmt = $conn->prepare("
                UPDATE formularios 
                SET pregunta = ?, tipo_respuesta = ?, respuesta_1 = ?, respuesta_2 = ?, respuesta_3 = ?, respuesta_4 = ? 
                WHERE id_formulario = ? AND pregunta_num = ?");
            $stmt->bind_param(
                "ssssssii",
                $preguntaTexto,
                $tipoRespuesta,
                $respuesta1,
                $respuesta2,
                $respuesta3,
                $respuesta4,
                $idFormulario,
                $preguntaNum
            );
            $stmt->execute();
        } else {
            // Pregunta nueva: Insertar
            $stmt = $conn->prepare("
                INSERT INTO formularios (id_formulario, pregunta_num, pregunta, tipo_respuesta, respuesta_1, respuesta_2, respuesta_3, respuesta_4) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "iissssss",
                $idFormulario,
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
            throw new Exception("Error al procesar las preguntas: " . $stmt->error);
        }
    }

    // Eliminar preguntas no incluidas en la edición
    $numeroPreguntas = count($preguntas);
    $stmt = $conn->prepare("DELETE FROM formularios WHERE id_formulario = ? AND pregunta_num > ?");
    $stmt->bind_param("ii", $idFormulario, $numeroPreguntas);
    $stmt->execute();

    if ($stmt->error) {
        throw new Exception("Error al eliminar preguntas antiguas: " . $stmt->error);
    }

    // Confirmar transacción
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Formulario actualizado correctamente.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>
