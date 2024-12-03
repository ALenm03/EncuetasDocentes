<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Verificar si el usuario es "normal" y no "admin"
if ($_SESSION['user_role'] !== 'user') {
    header("Location: index.html");
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

// Obtener el hash del evento desde el parámetro de la URL
$eventoHash = $_GET['evento'];

// Verificar que el hash no esté vacío
if (empty($eventoHash)) {
    die("El evento no es válido.");
}

// Buscar el evento por el hash del link
$sql_evento = "SELECT e.*, f.nombre_formulario 
               FROM eventos e 
               INNER JOIN formulario f ON e.id_formulario = f.id
               WHERE e.link LIKE ?";
$stmt_evento = $conn->prepare($sql_evento);
$link_param = "%$eventoHash"; // Buscar eventos que contengan el hash
$stmt_evento->bind_param("s", $link_param);
$stmt_evento->execute();
$result_evento = $stmt_evento->get_result();

// Verificar si el evento existe
if ($result_evento->num_rows === 0) {
    die("El evento no existe, ya finalizó, alcanzó el límite de participantes o no es válido.");
}

$evento = $result_evento->fetch_assoc();

// Validar las fechas y el número de participantes
$current_date = date("Y-m-d");
if ($current_date > $evento['fecha_final'] || $current_date < $evento['fecha_inicio']) {
    die("El evento ya no está disponible.");
}
if ($evento['participantes_actuales'] >= $evento['participantes_totales']) {
    die("El evento alcanzó el límite de participantes.");
}

// Verificar si el usuario ya contestó la encuesta
$id_usuario = $_SESSION['user_id'];
$sql_respuesta = "SELECT 1 
                  FROM respuestas 
                  WHERE id_usuario = ? 
                  AND id_evento = ?";
$stmt_respuesta = $conn->prepare($sql_respuesta);
$stmt_respuesta->bind_param("ii", $id_usuario, $evento['id']);
$stmt_respuesta->execute();
$result_respuesta = $stmt_respuesta->get_result();

if ($result_respuesta->num_rows > 0) {
    die("Ya has respondido a esta encuesta.");
}

// Obtener las preguntas relacionadas al formulario
$sql_preguntas = "SELECT * 
                  FROM formularios 
                  WHERE id_formulario = ?";
$stmt_preguntas = $conn->prepare($sql_preguntas);
$stmt_preguntas->bind_param("i", $evento['id_formulario']);
$stmt_preguntas->execute();
$result_preguntas = $stmt_preguntas->get_result();
$preguntas = [];
while ($row = $result_preguntas->fetch_assoc()) {
    $preguntas[] = $row;
}

// Si se envían respuestas, procesarlas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger respuestas del formulario
    $respuestas = $_POST['respuesta'];

    // Guardar cada respuesta en la base de datos
    foreach ($respuestas as $pregunta_num => $respuesta) {
        // Convertir respuestas múltiples (checkbox) a JSON si es un array
        if (is_array($respuesta)) {
            $respuesta = json_encode($respuesta);
        }

        $sql_guardar_respuesta = "INSERT INTO respuestas (id_evento, id_usuario, id_formulario, pregunta_num, respuesta)
                                  VALUES (?, ?, ?, ?, ?)";
        $stmt_guardar_respuesta = $conn->prepare($sql_guardar_respuesta);
        $stmt_guardar_respuesta->bind_param("iiiss", $evento['id'], $id_usuario, $evento['id_formulario'], $pregunta_num, $respuesta);
        $stmt_guardar_respuesta->execute();
    }

    // Incrementar el número de participantes actuales
    $sql_actualizar_participantes = "UPDATE eventos 
                                     SET participantes_actuales = participantes_actuales + 1 
                                     WHERE id = ?";
    $stmt_actualizar = $conn->prepare($sql_actualizar_participantes);
    $stmt_actualizar->bind_param("i", $evento['id']);
    $stmt_actualizar->execute();

    // Redirigir al usuario a la página de usuario normal
    header("Location: usuario_normal.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Encuesta</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header CabezaTabla">
                    <h1 class="text-center"><?= htmlspecialchars($evento['nombre_formulario']) ?></h1>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="">
                            <?php foreach ($preguntas as $index => $pregunta): ?>
                                <div class="form-group">
                                    <label><strong><?= $index + 1 ?>. <?= htmlspecialchars($pregunta['pregunta']) ?></strong></label>
                                    <?php if ($pregunta['tipo_respuesta'] === 'parrafo'): ?>
                                        <textarea name="respuesta[<?= $pregunta['pregunta_num'] ?>]" class="form-control" rows="4" required></textarea>
                                    <?php elseif ($pregunta['tipo_respuesta'] === 'opcion_multiple'): ?>
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <?php if (!empty($pregunta["respuesta_$i"])): ?>
                                                <div class="form-check">
                                                    <input type="radio" name="respuesta[<?= $pregunta['pregunta_num'] ?>]" value="<?= htmlspecialchars($pregunta["respuesta_$i"]) ?>" class="form-check-input" required>
                                                    <label class="form-check-label"><?= htmlspecialchars($pregunta["respuesta_$i"]) ?></label>
                                                </div>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    <?php elseif ($pregunta['tipo_respuesta'] === 'checkbox'): ?>
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <?php if (!empty($pregunta["respuesta_$i"])): ?>
                                                <div class="form-check">
                                                    <input type="checkbox" name="respuesta[<?= $pregunta['pregunta_num'] ?>][]" value="<?= htmlspecialchars($pregunta["respuesta_$i"]) ?>" class="form-check-input">
                                                    <label class="form-check-label"><?= htmlspecialchars($pregunta["respuesta_$i"]) ?></label>
                                                </div>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-primary btn-block">Enviar Respuestas</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        

    </div>

    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');

            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Evita el envío inmediato del formulario

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Una vez enviadas, no podrás modificar tus respuestas.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: '¡Enviado!',
                            text: 'Tus respuestas se han registrado correctamente.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Envía el formulario después de mostrar la confirmación
                        setTimeout(() => {
                            form.submit();
                        }, 2000);
                    }
                });
            });
        });
    </script>

</body>

</html>
