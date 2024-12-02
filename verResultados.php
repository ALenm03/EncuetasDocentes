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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .respuesta-seleccionada {
            background-color: #d4edda;
            font-weight: bold;
        }
    </style>
    <style>
    .respuesta-seleccionada {
            background-color: #d4edda;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 4px;
            display: inline-block;
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
