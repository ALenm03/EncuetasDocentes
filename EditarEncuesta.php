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

// Obtener el nombre del formulario de la URL y habilitar el modo de edición
$nombreFormulario = $_GET['nombre_formulario'];
$editar = isset($_GET['editar']) ? true : false;

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
    <title>Editar Formulario</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
</head>
<body class="hold-transition login-page">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h1>Editar Formulario</h1>
            </div>
            <div class="card-body">
                <form action="backend/guardar_edicion.php" method="POST">
                    <!-- Campo editable para el nombre del formulario -->
                    <div class="form-group">
                        <label>Nombre del Formulario</label>
                        <input type="text" name="nuevo_nombre_formulario" class="form-control" value="<?php echo htmlspecialchars($nombreFormulario); ?>">
                        <input type="hidden" name="nombre_formulario_original" value="<?php echo htmlspecialchars($nombreFormulario); ?>">
                    </div>
                    
                    <?php
                    // Iterar sobre las preguntas y mostrar campos editables para preguntas y respuestas
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='form-group'>";
                        echo "<label>Pregunta " . htmlspecialchars($row['pregunta_num']) . "</label>";
                        echo "<input type='text' name='pregunta_{$row['pregunta_num']}' value='" . htmlspecialchars($row['pregunta']) . "' class='form-control'>";
                        
                        switch ($row['tipo_respuesta']) {
                            case 'parrafo':
                                echo "<textarea name='respuesta_{$row['pregunta_num']}_1' class='form-control' rows='3'>" . htmlspecialchars($row['respuesta_1']) . "</textarea>";
                                break;

                            case 'opcion_multiple':
                            case 'checkbox':
                                for ($i = 1; $i <= 4; $i++) {
                                    $respuesta = $row["respuesta_$i"];
                                    if ($respuesta) {
                                        echo "<input type='text' name='respuesta_{$row['pregunta_num']}_$i' value='" . htmlspecialchars($respuesta) . "' class='form-control mb-2'>";
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
                    
                    <button type="submit" class="btn btn-success mt-3">Guardar cambios</button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
