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

// Obtener el ID del evento desde la URL o usar 0 si no está presente
$eventId = isset($_GET['id_evento']) ? $_GET['id_evento'] : 0;

// Verificar si el ID del evento es válido
if ($eventId == 0) {
    die("Evento no válido.");
}

// Consultar los usuarios que respondieron
$sql = "SELECT DISTINCT u.id, u.name AS nombre_usuario
        FROM respuestas r
        JOIN usuario u ON r.id_usuario = u.id
        WHERE r.id_evento = ?";
$stmt = $conn->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if (!$stmt) {
    die("Error en la consulta SQL: " . $conn->error);
}

$stmt->bind_param("i", $eventId);
$stmt->execute();

// Verificar si la ejecución fue exitosa
$result = $stmt->get_result();
if (!$result) {
    die("Error al ejecutar la consulta: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuestas del Evento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <style>
        #graficaBarra {
            display: block;
            width: 100%;
            height: 400px; /* Ajusta el tamaño según sea necesario */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Usuarios que Respondieron</h1>
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
                                class="btn btn-primary btn-ver-respuestas" 
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

    <!-- Modal para mostrar las respuestas -->
    <div class="modal fade" id="respuestasModal" tabindex="-1" aria-labelledby="respuestasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="respuestasModalLabel">Respuestas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="modalRespuestasContent">
                        <p>Cargando respuestas...</p>
                    </div>
                    <!-- Canvas para la gráfica -->
                    <canvas id="graficaBarra"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success btn-ver-grafica" data-user-id="" data-event-id="">Ver Gráfica</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            // Ver respuestas y cargar la información
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

            // Ver gráfica
            // Ver gráfica
            $('.btn-ver-grafica').on('click', function () {
                    const userId = $(this).data('user-id');
                    const eventId = $(this).data('event-id');

                    $.ajax({
                        url: 'backend/get_respuestas_grafica.php',  // Asegúrate de que esta URL sea correcta
                        method: 'GET',
                        data: { user_id: userId, event_id: eventId },
                        success: function (response) {
                            const data = JSON.parse(response); // Los datos de las respuestas contadas

                            // Verifica si las respuestas están presentes
                            if (data.respuesta_1 !== undefined && data.respuesta_2 !== undefined && data.respuesta_3 !== undefined && data.respuesta_4 !== undefined) {
                                const ctx = document.getElementById('graficaBarra').getContext('2d');

                                // Destruir cualquier gráfico existente
                                if (window.myChart) {
                                    window.myChart.destroy();
                                }

                                // Crear la gráfica
                                setTimeout(function() {
                                    window.myChart = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Respuesta 1', 'Respuesta 2', 'Respuesta 3', 'Respuesta 4'],
                                            datasets: [{
                                                label: 'Cantidad de Selecciones',
                                                data: [data.respuesta_1, data.respuesta_2, data.respuesta_3, data.respuesta_4],
                                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                                borderColor: 'rgba(54, 162, 235, 1)',
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                }, 300); // Esperar 300 ms para asegurar que el modal esté completamente visible

                                // Mostrar el modal con la gráfica
                                $('#respuestasModal').modal('show');
                            } else {
                                alert('No se recibieron los datos correctamente');
                            }
                        },
                        error: function () {
                            alert('Error al cargar la gráfica.');
                        }
                    });
                });
        });
    </script>
</body>
</html>
