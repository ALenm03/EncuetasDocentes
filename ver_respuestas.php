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

// Obtener el ID del evento de la URL
$id_evento = $_GET['evento'];

// Verificar que el ID del evento no esté vacío
if (empty($id_evento)) {
    die("Evento no válido.");
}

// Verificar que el usuario respondió a este evento
$id_usuario = $_SESSION['user_id'];
$sql_verificar = "
    SELECT e.id AS id_evento, f.nombre_formulario 
    FROM eventos e
    INNER JOIN formulario f ON e.id_formulario = f.id
    INNER JOIN respuestas r ON r.id_evento = e.id
    WHERE e.id = ? AND r.id_usuario = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("ii", $id_evento, $id_usuario);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

// Verificar si el evento existe y el usuario tiene acceso
if ($result_verificar->num_rows === 0) {
    die("No tienes acceso a este evento o no has respondido esta encuesta.");
}

$evento = $result_verificar->fetch_assoc();

// Obtener las preguntas y las respuestas del usuario
$sql_preguntas_respuestas = "
    SELECT p.pregunta, p.tipo_respuesta, p.respuesta_1, p.respuesta_2, p.respuesta_3, p.respuesta_4, r.respuesta 
    FROM formularios p
    LEFT JOIN respuestas r ON p.pregunta_num = r.pregunta_num AND r.id_evento = ? AND r.id_usuario = ?
    WHERE p.id_formulario = (SELECT id_formulario FROM eventos WHERE id = ?)";
$stmt_preguntas_respuestas = $conn->prepare($sql_preguntas_respuestas);
$stmt_preguntas_respuestas->bind_param("iii", $id_evento, $id_usuario, $id_evento);
$stmt_preguntas_respuestas->execute();
$result_preguntas_respuestas = $stmt_preguntas_respuestas->get_result();

// Cargar preguntas y respuestas
$preguntas = [];
while ($row = $result_preguntas_respuestas->fetch_assoc()) {
    $preguntas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuestas de Encuesta</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/stylesUsr.css">

</head>
<body id="adm_body">
    <header class="p-3 fixed-top" style="background-color: #372549;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <h1 class="h5 m-0" style="color:white;">Encuesta Respondida</h1>
            </div>
            <div class="d-flex">
                <i id="toggle-dark-mode" class="fas fa-moon"></i>
                <button class="btn btn-primary mr-2" id="usr_regresar">Regresar</button>
                <form action="backend/logout.php" method="POST">
                    <button class="btn btn-primary" type="submit" id="adm_logout">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </header>


    <div class="container" style=" margin-top: 120px;">
        <h1 class="text-center"><?= htmlspecialchars($evento['nombre_formulario']) ?></h1>
        <div class="mt-4">
            <?php foreach ($preguntas as $index => $pregunta): ?>
                <div class="card mb-3">
                    <div class="card-header Pregunta_Respondida">
                        <strong><?= $index + 1 ?>. <?= htmlspecialchars($pregunta['pregunta']) ?></strong>
                    </div>
                    <div class="card-body">
                        <?php if ($pregunta['tipo_respuesta'] === 'parrafo'): ?>
                            <p><strong>Tu respuesta:</strong> <?= htmlspecialchars($pregunta['respuesta']) ?></p>
                        <?php elseif ($pregunta['tipo_respuesta'] === 'opcion_multiple'): ?>
                            <p><strong>Opciones disponibles:</strong></p>
                            <ul>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <?php if (!empty($pregunta["respuesta_$i"])): ?>
                                        <li>
                                            <?= htmlspecialchars($pregunta["respuesta_$i"]) ?>
                                            <?= ($pregunta['respuesta'] === $pregunta["respuesta_$i"]) ? "<span class='text-success'>(Seleccionada)</span>" : "" ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </ul>
                        <?php elseif ($pregunta['tipo_respuesta'] === 'checkbox'): ?>
                            <p><strong>Opciones disponibles:</strong></p>
                            <ul>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <?php if (!empty($pregunta["respuesta_$i"])): ?>
                                        <li>
                                            <?= htmlspecialchars($pregunta["respuesta_$i"]) ?>
                                            <?= (is_array(json_decode($pregunta['respuesta'], true)) && in_array($pregunta["respuesta_$i"], json_decode($pregunta['respuesta'], true))) ? "<span class='text-success'>(Seleccionada)</span>" : "" ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="usuario_normal.php" class="btn btn-primary" id="btn_volver">Volver</a>
    </div>




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

    <script>
        document.getElementById('usr_regresar').addEventListener('click', function () {
            window.location.href = 'usuario_normal.php';
        });
    </script>
</body>
</html>
