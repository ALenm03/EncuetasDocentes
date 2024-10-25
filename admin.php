<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión y si es admin
if (!isset($_SESSION['user_id'])) {
    // Si no está autenticado o no es admin, redirigir a index.html
    header("Location: index.html");
    exit();
}
if ($_SESSION['user_role'] == 'user') {
    header("Location: usuario_normal.php");
    exit();
}


// Conexión a la base de datos
$servername = "localhost"; // Cambia según tu configuración
$username = "root";        // Cambia según tu configuración
$password = "";            // Cambia según tu configuración
$dbname = "bdform";        // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener encuestas relacionadas al usuario logueado sin repeticiones
$sql = "SELECT f.id, f.nombre_formulario 
        FROM formularios f 
        WHERE f.id_usuario = ? 
        GROUP BY f.nombre_formulario";
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
    <title>Admin</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body id="adm_body">

    <!-- Header fijo con el botón de Cerrar Sesión -->
    <div id="header_pagina">
        <h2>Panel de Administrador</h2>
        <div class="header-buttons">
            <form action="backend/logout.php" method="POST">
                <button type="submit" id="adm_logout">Cerrar sesión</button>
            </form>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="card col-md-12">
                <div class="card-header adm_CabezaTabla">
                    <div class="card-title">
                        <h1>Encuestas</h1>
                        <button type="button" id="crear_nueva_encuesta">Nueva Encuesta</button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover text-nowrap table-head-fixed" id="Tablita">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Encuesta</th>
                                <th>Ver Encuesta</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Mostrar las encuestas en la tabla
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$count}</td>";
                                echo "<td>{$row['nombre_formulario']}</td>";
                                echo "<td><button class='btn_ver'>Ver</button></td>";
                                echo "<td><button class='btn_editar'>Editar</button></td>";
                                echo "<td><button class='btn_eliminar_encuesta'>Eliminar</button></td>";
                                echo "</tr>";
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.all.min.js"></script>

    <script>
        // Agregar eventos a los botones de eliminar
        document.querySelectorAll('.btn_eliminar_encuesta').forEach(button => {
            button.addEventListener('click', function () {
                Swal.fire({
                    title: '¿Seguro que quieres eliminar la encuesta?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4281A4',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aquí puedes agregar la lógica para eliminar la encuesta
                        Swal.fire('Eliminado', 'La encuesta ha sido eliminada.', 'success');
                    }
                });
            });
        });

        document.getElementById('crear_nueva_encuesta').addEventListener('click', function() {
            window.location.href = 'creacionFormulario.php'; 
        });
    </script>

</body>
</html>
