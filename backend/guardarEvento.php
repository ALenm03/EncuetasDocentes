<?php
// Iniciar sesión
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

// Recibir datos del formulario
$id_usuario = $_SESSION['user_id'];
$id_formulario = $_POST['id_formulario'];
$nombre_evento = $_POST['nombre_evento'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_final = $_POST['fecha_final'];
$participantes_totales = $_POST['participantes_totales'];

// Generar un enlace único para el evento
$link = "contestarencuesta.php?evento=" . uniqid();

// Insertar el evento en la base de datos
$sql = "
    INSERT INTO eventos (id_usuario, id_formulario, nombre_evento, fecha_inicio, fecha_final, participantes_totales, participantes_actuales, link) 
    VALUES (?, ?, ?, ?, ?, ?, 0, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisssis", $id_usuario, $id_formulario, $nombre_evento, $fecha_inicio, $fecha_final, $participantes_totales, $link);

if ($stmt->execute()) {
    // Redirigir a admin.php con un mensaje de éxito
    echo "<script>
        alert('Evento creado exitosamente.');
        window.location.href = '/EncuetasDocentes/admin.php';
    </script>";
} else {
    // Mostrar un mensaje de error y regresar al formulario
    echo "<script>
        alert('Error al crear el evento. Por favor, intenta de nuevo.');
        window.history.back();
    </script>";
}

// Cerrar conexión
$conn->close();
?>
