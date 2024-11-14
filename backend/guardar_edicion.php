<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Conexión a la base de datos
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

// Obtener el nombre original y el nuevo nombre del formulario
$nombreFormularioOriginal = $_POST['nombre_formulario_original'];
$nuevoNombreFormulario = $_POST['nuevo_nombre_formulario'];
$userId = $_SESSION['user_id'];

// Actualizar el nombre del formulario en todas las entradas con el mismo nombre y usuario
$sqlUpdateNombre = "UPDATE formularios SET nombre_formulario = ? WHERE nombre_formulario = ? AND id_usuario = ?";
$stmtUpdateNombre = $conn->prepare($sqlUpdateNombre);
$stmtUpdateNombre->bind_param("ssi", $nuevoNombreFormulario, $nombreFormularioOriginal, $userId);
$stmtUpdateNombre->execute();

// Recorrer las preguntas y respuestas para actualizar cada una según el número de pregunta
foreach ($_POST as $key => $valor) {
    if (strpos($key, 'pregunta_') === 0) {
        $preguntaNum = str_replace('pregunta_', '', $key);

        // Actualizar pregunta
        $sqlUpdatePregunta = "UPDATE formularios SET pregunta = ? WHERE nombre_formulario = ? AND pregunta_num = ? AND id_usuario = ?";
        $stmtUpdatePregunta = $conn->prepare($sqlUpdatePregunta);
        $stmtUpdatePregunta->bind_param("ssii", $valor, $nuevoNombreFormulario, $preguntaNum, $userId);
        $stmtUpdatePregunta->execute();
    } elseif (preg_match('/respuesta_(\d+)_(\d+)/', $key, $matches)) {
        // Actualizar respuestas según el número de pregunta y opción
        $preguntaNum = $matches[1];
        $opcionNum = $matches[2];

        $sqlUpdateRespuesta = "UPDATE formularios SET respuesta_$opcionNum = ? WHERE nombre_formulario = ? AND pregunta_num = ? AND id_usuario = ?";
        $stmtUpdateRespuesta = $conn->prepare($sqlUpdateRespuesta);
        $stmtUpdateRespuesta->bind_param("ssii", $valor, $nuevoNombreFormulario, $preguntaNum, $userId);
        $stmtUpdateRespuesta->execute();
    }
}

// Cerrar conexiones y redirigir al usuario
$stmtUpdateNombre->close();
$stmtUpdatePregunta->close();
$stmtUpdateRespuesta->close();
$conn->close();

header("Location: /EncuetasDocentes/admin.php");
exit();
?>
