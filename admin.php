<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión y si es admin
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}
if ($_SESSION['user_role'] == 'user') {
    header("Location: usuario_normal.php");
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

// Obtener encuestas relacionadas al usuario logueado sin repeticiones
$sql = "SELECT f.id, f.nombre_formulario 
        FROM formulario f 
        WHERE f.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();


// Obtener eventos relacionados al usuario logueado
$sql2 = "SELECT nombre_evento, fecha_inicio, fecha_final, participantes_actuales, participantes_totales, link 
        FROM eventos 
        WHERE id_usuario = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $_SESSION['user_id']);
$stmt2->execute();
$result2 = $stmt2->get_result();
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
    <link rel="stylesheet" href="assets/stylesAdmin.css">
</head>
<body id="adm_body">

    <!-- Header fijo con el botón de Cerrar Sesión -->
    <header class="p-3 fixed-top" style="background-color: #372549;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <h1 class="h5 m-0" style="color:white;">Panel de Administrador</h1>
            </div>
            
            <div class="d-flex header-buttons">
                <i id="toggle-dark-mode" class="fas fa-moon"></i>
                <form action="backend/logout.php" method="POST" style="margin: 0;">
                    <button type="submit" id="adm_logout">Cerrar sesión</button>
                </form>
            </div>

        </div>
    </header>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header adm_CabezaTabla">
                        <div class="card-title">
                            <h1>Encuestas</h1>
                            <button type="button" id="crear_nueva_encuesta">Nueva Encuesta</button>
                        </div>
                    </div>
                    <div class="card-body" >
                        <table class="table table-responsive-md table-head-fixed table-striped" id="Tablita">
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
                                    echo "<td><a href='verFormulario.php?nombre_formulario={$row['nombre_formulario']}' class='btn_ver' style='padding:3px; color:white; margin-right:5px;'> Ver </a> <a class='btn_ver' style='padding: 3.5px; color:white; '>Respuesta de usuario</a> <a class='btn_ver' style='padding:3px; color:white;'>Grafica</a></td>";
                                    echo "<td><a href='EditarEncuesta.php?nombre_formulario={$row['nombre_formulario']}' class='btn_editar' style='padding:3px; color:white;'>Editar</a></td>";
                                    echo "<td><a class='btn_eliminar_encuesta' data-id='{$row['id']}' data-nombre='{$row['nombre_formulario']}' style='padding:3px; color:white;'>Eliminar</a></td>";
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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header adm_CabezaTabla">
                        <div class="card-title">
                            <h1>Mis Eventos</h1>
                            <button type="button" id="crear_evento">Crear Evento</button>
                        </div>
                    </div>
                    <div class="card-body" >
                        <table class="table table-responsive-md table-head-fixed table-striped" id="Tablita2">
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
                                <?php while ($row2 = $result2->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row2['nombre_evento']) ?></td>
                                        <td><?= htmlspecialchars($row2['fecha_inicio']) ?></td>
                                        <td><?= htmlspecialchars($row2['fecha_final']) ?></td>
                                        <td><?= htmlspecialchars($row2['participantes_actuales']) ?>/<?= htmlspecialchars($row2['participantes_totales']) ?></td>
                                        <td>
                                            <a href="<?= htmlspecialchars($row2['link']) ?>" target="_blank"><?= htmlspecialchars($row2['link']) ?></a>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-copy-link" data-link="<?= htmlspecialchars($row2['link']) ?>">Copiar Link</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <script>
        // Agregar funcionalidad para copiar el link al portapapeles
        document.querySelectorAll('.btn-copy-link').forEach(button => {
            button.addEventListener('click', function () {
                const link = this.getAttribute('data-link');
                navigator.clipboard.writeText("localhost/EncuetasDocentes/" + link).then(() => {
                    Swal.fire('Link copiado', 'El link se ha copiado al portapapeles.', 'success');
                }).catch(err => {
                    Swal.fire('Error', 'No se pudo copiar el link.', 'error');
                });
            });
        });
    </script>
    <script>
        // Agregar eventos a los botones de eliminar
        document.querySelectorAll('.btn_eliminar_encuesta').forEach(button => {
            button.addEventListener('click', function () {
                const idFormulario = this.getAttribute('data-id');
                const nombreFormulario = this.getAttribute('data-nombre');

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
                        // Enviar solicitud AJAX al backend para eliminar
                        fetch('backend/eliminar_encuesta.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ nombreFormulario }) // Enviar solo el nombre del formulario
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Respuesta del servidor:', data);
                            if (data.success) {
                                Swal.fire('Eliminado', 'La encuesta ha sido eliminada.', 'success');
                                // Eliminar la fila de la tabla en la interfaz
                                this.closest('tr').remove();
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar la encuesta.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error en la solicitud:', error);
                            Swal.fire('Error', 'Ocurrió un problema al eliminar la encuesta.', 'error');
                        });
                    }
                });
            });
        });

        // Redirigir para crear nueva encuesta
        document.getElementById('crear_nueva_encuesta').addEventListener('click', function() {
            window.location.href = 'creacionFormulario.php'; 
        });
        // Redirigir para crear un nuevo evento
        document.getElementById('crear_evento').addEventListener('click', function() {
            Swal.fire({
                title: '¿Estas seguro de que quieres crear un evento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4281A4',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, crear',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'crearevento.php';
                }
            });
        });

    </script>
    <script>
        // Selecciona el ícono y el cuerpo
        const toggleDarkModeIcon = document.getElementById('toggle-dark-mode');
        const bodyElement = document.getElementById('adm_body');

        // Función para aplicar el modo oscuro
        function applyDarkMode(isDarkMode) {
            if (isDarkMode) {
                bodyElement.classList.add('dark-mode');
            } else {
                bodyElement.classList.remove('dark-mode');
            }
        }

        // Leer el estado del modo oscuro desde LocalStorage al cargar la página
        const isDarkMode = localStorage.getItem('dark-mode') === 'true';
        applyDarkMode(isDarkMode);

        // Agregar evento al ícono
        toggleDarkModeIcon.addEventListener('click', function () {
            // Alternar el modo oscuro
            const darkModeActive = bodyElement.classList.toggle('dark-mode');

            // Guardar el estado en LocalStorage
            localStorage.setItem('dark-mode', darkModeActive);
        });
    </script>


</body>
</html>