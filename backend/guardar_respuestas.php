<?php
// Iniciar sesión
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: index.html");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdform";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$id_evento = $_POST['id_evento'];
$id_usuario = $_SESSION['user_id'];

// Validar si el usuario ya respondió esta encuesta
$sql_verificar = "SELECT 1 FROM respuestas WHERE id_evento = ? AND id_usuario = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("ii", $id_evento, $id_usuario);
$stmt_verificar->execute();
if ($stmt_verificar->get_result()->num_rows > 0) {
    echo "Ya has respondido esta encuesta.";
    exit();
}

// Guardar respuestas
foreach ($_POST as $key => $value) {
    if (strpos($key, 'respuesta_') === 0) {
        $pregunta_num = str_replace('respuesta_', '', $key);
        $respuesta = is_array($value) ? implode(", ", $value) : $value;

        $sql_insert = "INSERT INTO respuestas (id_evento, id_usuario, pregunta_num, respuesta) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiis", $id_evento, $id_usuario, $pregunta_num, $respuesta);
        $stmt_insert->execute();
    }
}

// Actualizar el número de participantes
$sql_update = "UPDATE eventos SET participantes_actuales = participantes_actuales + 1 WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("i", $id_evento);
$stmt_update->execute();

header("Location: /EncuetasDocentes/usuario_normal.php");
exit();
?>
