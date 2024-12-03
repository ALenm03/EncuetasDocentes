<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
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

// Obtener el ID del evento
$eventId = $_GET['id_evento'] ?? 0;

// Consultar los usuarios que respondieron
$sql = "SELECT DISTINCT u.id, u.name AS nombre_usuario
        FROM respuestas r
        JOIN usuario u ON r.id_usuario = u.id
        WHERE r.id_evento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuestas del Evento</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="assets/StylesVerResultados.css">
</head>
<body id="body">
    <header class="p-3 fixed-top" style="background-color: #372549;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <h1 class="h5 m-0" style="color:white;">Panel de Administrador</h1>
            </div>
            
            <div class="d-flex header-buttons">
                <i id="toggle-dark-mode" class="fas fa-moon"></i>
                <button class="btn btn-primary mr-2" id="adm_regresar">Regresar</button>
                <form action="backend/logout.php" method="POST" style="margin: 0;">
                    <button class="btn btn-primary" type="submit" id="adm_logout">Cerrar sesión</button>
                </form>
            </div>

        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header adm_CabezaTabla">
                        <h1>Usuarios que Respondieron</h1>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nombre_usuario']) ?></td>
                                        <td>
                                            <button 
                                                class="btn btn-ver-respuestas" 
                                                data-user-id="<?= htmlspecialchars($row['id']) ?>"
                                                data-event-id="<?= htmlspecialchars($eventId) ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#respuestasModal">
                                                Ver Respuestas
                                            </button>
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

    <!-- Modal para mostrar las respuestas -->
    <!-- Modal para mostrar las respuestas -->
    <div class="modal fade" id="respuestasModal" tabindex="-1" aria-labelledby="respuestasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="respuestasModalLabel">Respuestas</h5>
                </div>
                <div class="modal-body">
                    <div id="modalRespuestasContent">
                        <p>Cargando respuestas...</p>
                    </div>
                    <!-- Contenedor para el gráfico -->
                    <canvas id="graficoRespuestas" style="display: none; max-width: 100%;"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="generarGrafico" class="btn btn-primary">Generar Gráfico</button>
                </div>
            </div>
        </div>
    </div>


    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('adm_regresar').addEventListener('click', function () {window.location.href = 'admin.php';});
    </script>
    <script>
        $(document).ready(function () {
            $('.btn-ver-respuestas').on('click', function () {
                const userId = $(this).data('user-id');
                const eventId = $(this).data('event-id');

                $('#modalRespuestasContent').html('<p>Cargando respuestas...</p>');
                $.ajax({
                    url: 'backend/get_respuestas.php',
                    method: 'GET',
                    data: { user_id: userId, event_id: eventId },
                    success: function (response) {
                        $('#modalRespuestasContent').html(response);
                    },
                    error: function () {
                        $('#modalRespuestasContent').html('<p>Error al cargar las respuestas.</p>');
                    }
                });
            });
        });
    </script>
    <script>
        // Selecciona el ícono y el cuerpo
        const toggleDarkModeIcon = document.getElementById('toggle-dark-mode');
        const bodyElement = document.getElementById('body');

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
        document.querySelectorAll('.btn-view-responses').forEach(button => {
            button.addEventListener('click', function () {
                const eventId = this.getAttribute('data-event-id');
                window.location.href = `verResultados.php?id_evento=${eventId}`;
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    $(document).ready(function () {
        let conteoRespuestas = { respuesta1: 0, respuesta2: 0, respuesta3: 0, respuesta4: 0 };

        $('.btn-ver-respuestas').on('click', function () {
            const userId = $(this).data('user-id');
            const eventId = $(this).data('event-id');

            $('#modalRespuestasContent').html('<p>Cargando respuestas...</p>');
            $('#graficoRespuestas').hide(); // Ocultar el gráfico inicialmente

            $.ajax({
                url: 'backend/get_respuestas.php',
                method: 'GET',
                data: { user_id: userId, event_id: eventId },
                success: function (response) {
                    $('#modalRespuestasContent').html(response);

                    // Reiniciar el conteo
                    conteoRespuestas = { respuesta1: 0, respuesta2: 0, respuesta3: 0, respuesta4: 0 };

                    // Procesar respuestas seleccionadas para el gráfico
                    procesarRespuestasParaGrafico();
                },
                error: function () {
                    $('#modalRespuestasContent').html('<p>Error al cargar las respuestas.</p>');
                }
            });
        });

        $('#generarGrafico').on('click', function () {
            generarGrafico();
        });

        function procesarRespuestasParaGrafico() {
            // Buscar cada fila de respuestas dentro de la tabla
            $('#modalRespuestasContent table tbody tr').each(function () {
                const respuestasHtml = $(this).find('td:last').html(); // Extraer HTML de las respuestas

                // Buscar cada respuesta seleccionada
                $(respuestasHtml).find('li').each(function (index) {
                    if ($(this).text().includes('(Seleccionada)')) {
                        // Incrementar el conteo correspondiente
                        if (index === 0) conteoRespuestas.respuesta1++;
                        else if (index === 1) conteoRespuestas.respuesta2++;
                        else if (index === 2) conteoRespuestas.respuesta3++;
                        else if (index === 3) conteoRespuestas.respuesta4++;
                    }
                });
            });
        }

        function generarGrafico() {
            const labels = ["Respuesta 1", "Respuesta 2", "Respuesta 3", "Respuesta 4"];
            const values = [
                conteoRespuestas.respuesta1,
                conteoRespuestas.respuesta2,
                conteoRespuestas.respuesta3,
                conteoRespuestas.respuesta4
            ];

            const ctx = document.getElementById('graficoRespuestas').getContext('2d');
            $('#graficoRespuestas').show(); // Mostrar el canvas del gráfico

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Veces seleccionadas',
                        data: values,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>

</body>
</html>
