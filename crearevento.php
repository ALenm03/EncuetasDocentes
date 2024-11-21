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

// Obtener encuestas del usuario logueado
$sql = "SELECT id, nombre_formulario FROM formulario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$encuestas = [];
while ($row = $result->fetch_assoc()) {
    $encuestas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Evento</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/stylesAdmin.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Crear Evento para Encuestas</h1>
        <?php if (count($encuestas) > 0): ?>
        <form id="form_crear_evento" action="backend/guardarEvento.php" method="POST">
            <div class="form-group">
                <label for="nombre_evento">Nombre del evento:</label>
                <input type="text" name="nombre_evento" id="nombre_evento" class="form-control" maxlength="255" placeholder="Nombre del evento" required>
            </div>
            <div class="form-group">
                <label for="id_formulario">Selecciona la encuesta:</label>
                <select name="id_formulario" id="id_formulario" class="form-control" required>
                    <option value="" disabled selected>Selecciona una encuesta</option>
                    <?php foreach ($encuestas as $encuesta): ?>
                        <option value="<?= htmlspecialchars($encuesta['id']) ?>"><?= htmlspecialchars($encuesta['nombre_formulario']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_final">Fecha de finalización:</label>
                <input type="date" name="fecha_final" id="fecha_final" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="participantes_totales">Número de participantes:</label>
                <input type="number" name="participantes_totales" id="participantes_totales" class="form-control" min="1" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Crear Evento</button>
            </div>
        </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <strong>No tienes encuestas disponibles.</strong><br>
                Por favor, crea una encuesta antes de programar un evento.
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script>
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaFinalInput = document.getElementById('fecha_final');

        // Actualizar el mínimo de fecha final cuando cambie la fecha de inicio
        fechaInicioInput.addEventListener('change', function () {
            const fechaInicio = this.value;
            fechaFinalInput.min = fechaInicio; // No permitir fechas anteriores a la fecha de inicio
        });

        // Prevenir que la fecha final sea menor a la fecha de inicio
        fechaFinalInput.addEventListener('change', function () {
            if (this.value < fechaInicioInput.value) {
                alert('La fecha final no puede ser anterior a la fecha de inicio.');
                this.value = fechaInicioInput.value;
            }
        });
    </script>
</body>
</html>
