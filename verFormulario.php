<?php
// Iniciar la sesión
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

// Obtener el nombre del formulario de la URL
$nombreFormulario = $_GET['nombre_formulario'];

// Consultar las preguntas y respuestas relacionadas al formulario y usuario logueado
$sql = "SELECT pregunta_num, pregunta, respuesta_1, respuesta_2, respuesta_3, respuesta_4, tipo_respuesta 
        FROM formularios 
        WHERE nombre_formulario = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $nombreFormulario, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Formulario</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body class="hold-transition login-page">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h1>Formulario: <?php echo htmlspecialchars($nombreFormulario); ?></h1>
            </div>
            <div class="card-body">
                <form action="backend/guardar_respuestas.php" method="POST">
                    <input type="hidden" name="nombre_formulario" value="<?php echo htmlspecialchars($nombreFormulario); ?>">
                    <?php
                    // Iterar sobre las preguntas y mostrar el tipo de input correspondiente
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='form-group'>";
                        echo "<label>" . htmlspecialchars($row['pregunta']) . "</label>";

                        switch ($row['tipo_respuesta']) {
                            case 'parrafo':
                                echo "<textarea name='respuesta_{$row['pregunta_num']}' class='form-control' rows='3'></textarea>";
                                break;

                            case 'opcion_multiple':
                                for ($i = 1; $i <= 4; $i++) {
                                    $respuesta = $row["respuesta_$i"];
                                    if ($respuesta) {
                                        echo "<div class='form-check'>";
                                        echo "<input type='radio' name='respuesta_{$row['pregunta_num']}' value='" . htmlspecialchars($respuesta) . "' class='form-check-input'>";
                                        echo "<label class='form-check-label'>" . htmlspecialchars($respuesta) . "</label>";
                                        echo "</div>";
                                    }
                                }
                                break;

                            case 'checkbox':
                                for ($i = 1; $i <= 4; $i++) {
                                    $respuesta = $row["respuesta_$i"];
                                    if ($respuesta) {
                                        echo "<div class='form-check'>";
                                        echo "<input type='checkbox' name='respuesta_{$row['pregunta_num']}[]' value='" . htmlspecialchars($respuesta) . "' class='form-check-input'>";
                                        echo "<label class='form-check-label'>" . htmlspecialchars($respuesta) . "</label>";
                                        echo "</div>";
                                    }
                                }
                                break;

                            default:
                                echo "<p>Tipo de respuesta no definido.</p>";
                                break;
                        }

                        echo "</div>";
                    }
                    ?>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary mt-3">Enviar respuestas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AdminLTE JavaScript -->
    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
