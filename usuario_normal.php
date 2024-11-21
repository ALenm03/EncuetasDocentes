<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Verificar si el usuario es admin y redirigir si es admin
if ($_SESSION['user_role'] == 'admin') {
    header("Location: admin.php");
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
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los eventos y encuestas que el usuario ha respondido
$id_usuario = $_SESSION['user_id'];
$sql = "
    SELECT e.nombre_evento, f.nombre_formulario, e.id AS id_evento
    FROM eventos e
    INNER JOIN formulario f ON e.id_formulario = f.id
    INNER JOIN respuestas r ON r.id_evento = e.id
    WHERE r.id_usuario = ?
    GROUP BY e.id, f.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/datatables-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="assets/stylesUsr.css">
    <title>Encuestas Respondidas</title>
</head>
<body>
    <header class="p-3 fixed-top" style="background-color: #372549;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <h1 class="h5 m-0" style="color:white;">Encuestas Respondidas</h1>
            </div>
            <div class="d-flex">
                <form action="backend/logout.php" method="POST">
                    <button class="btn btn-primary" type="submit" id="adm_logout">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container mt-5 pt-5">
        <h2 class="text-center mb-4">Encuestas Respondidas</h2>
        <table id="tabla_eventos" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Encuesta</th>
                    <th>Ver Respuestas</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nombre_evento']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_formulario']) ?></td>
                        <td>
                            <a href="ver_respuestas.php?evento=<?= $row['id_evento'] ?>" class="btn btn-info btn-sm">Ver</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tabla_eventos').DataTable();
        });
    </script>
</body>
</html>
