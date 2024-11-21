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

// Obtener eventos relacionados al usuario logueado
$sql = "SELECT nombre_evento, fecha_inicio, fecha_final, participantes_actuales, participantes_totales, link 
        FROM eventos 
        WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Eventos</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5">
        <h1>Mis Eventos</h1>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Final</th>
                    <th>Participantes</th>
                    <th>Link</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nombre_evento']) ?></td>
                        <td><?= htmlspecialchars($row['fecha_inicio']) ?></td>
                        <td><?= htmlspecialchars($row['fecha_final']) ?></td>
                        <td><?= htmlspecialchars($row['participantes_actuales']) ?>/<?= htmlspecialchars($row['participantes_totales']) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank"><?= htmlspecialchars($row['link']) ?></a>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-copy-link" data-link="<?= htmlspecialchars($row['link']) ?>">Copiar Link</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Agregar funcionalidad para copiar el link al portapapeles
        document.querySelectorAll('.btn-copy-link').forEach(button => {
            button.addEventListener('click', function () {
                const link = this.getAttribute('data-link');
                navigator.clipboard.writeText(link).then(() => {
                    Swal.fire('Link copiado', 'El link se ha copiado al portapapeles.', 'success');
                }).catch(err => {
                    Swal.fire('Error', 'No se pudo copiar el link.', 'error');
                });
            });
        });
    </script>
</body>

</html>

<?php
// Cerrar la conexión
$conn->close();
?>
