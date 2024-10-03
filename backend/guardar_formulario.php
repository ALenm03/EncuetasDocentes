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
$formData = json_decode($_POST['formData'] ?? '', true); // Decodifica correctamente
$nombre_formulario = $formData['nombre_formulario'] ?? '';
$preguntas = $formData['preguntas'] ?? [];

// Comprobar si se han recibido datos
if (empty($nombre_formulario) || empty($preguntas)) {
    die("Error: El nombre del formulario y las preguntas son obligatorios.");
}

// Preparar la consulta para insertar los datos
$stmt = $conn->prepare("INSERT INTO formularios (nombre_formulario, pregunta_num, pregunta, respuesta_1, respuesta_2, respuesta_3, respuesta_4, tipo_respuesta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissssss", $nombre_formulario, $pregunta_num, $pregunta, $respuesta_1, $respuesta_2, $respuesta_3, $respuesta_4, $tipo_respuesta);

// Insertar cada pregunta y sus respuestas
foreach ($preguntas as $index => $preguntaData) {
    $pregunta_num = $index + 1; // Número de la pregunta
    $pregunta = $preguntaData['pregunta'] ?? ''; // Asegúrate de que esto existe
    
    // Manejar respuestas
    $respuesta_1 = $respuesta_2 = $respuesta_3 = $respuesta_4 = null;
    
    if (isset($preguntaData['respuestas'])) {
        foreach ($preguntaData['respuestas'] as $i => $respuesta) {
            if ($i == 0) $respuesta_1 = $respuesta;
            elseif ($i == 1) $respuesta_2 = $respuesta;
            elseif ($i == 2) $respuesta_3 = $respuesta;
            elseif ($i == 3) $respuesta_4 = $respuesta;
        }
    }

    // Tipo de respuesta
    $tipo_respuesta = $preguntaData['tipo_respuesta'] ?? '';

    // Ejecutar la inserción
    if (!$stmt->execute()) {
        echo "Error al guardar la pregunta: " . $stmt->error; // Para depuración
        exit;
    }
}

// Cerrar la declaración y la conexión
$stmt->close();
$conn->close();

// Enviar un mensaje de éxito
echo "Formulario guardado con éxito.";
?>