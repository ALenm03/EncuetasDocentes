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
    <link rel="stylesheet" href="assets/stylesVerEncuesta.css">
</head>

<body class="hold-transition login-page" style="padding-top: 120px;">

    <header class="p-3 fixed-top" style="background-color: #372549;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <h1 class="h5 m-0" style="color:white;">Panel de Administrador</h1>
            </div>

            <div class="d-flex">
                <button class="btn btn-primary mr-2" id="adm_regresar">Regresar</button>
                <form action="backend/logout.php" method="POST">
                    <button type="submit" id="adm_logout">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header text-white text-center" style="background-color: #777DA7;">
                <h3>Formulario:
                    <?php echo htmlspecialchars($nombreFormulario); ?>
                </h3>
            </div>
            <div class="card-body">
                <form action="backend/guardar_respuestas.php" method="POST">
                    <input type="hidden" name="nombre_formulario"
                        value="<?php echo htmlspecialchars($nombreFormulario); ?>">
                    <?php
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='form-group'>";
                            echo "<label class='label_pregunta' style='font-size: small; color: gray; margin-bottom: 1px;'>Pregunta " . htmlspecialchars($row['pregunta_num']) . "</label><br>";
                            echo "<label style='margin-bottom: 10px; font-size: larger; font-weight:600;'>" . htmlspecialchars($row['pregunta']) . "</label>";
                            
                            echo "<div id='contenedor_de_respuestas_P".htmlspecialchars($row['pregunta'])."' style='margin-bottom: 30px;'>";
                                switch ($row['tipo_respuesta']) {
                                    case 'parrafo':
                                        echo "<textarea name='respuesta_{$row['pregunta_num']}' class='form-control' rows='3' style='max-height:390px;'></textarea>";
                                        break;
        
                                    case 'opcion_multiple':
                                        for ($i = 1; $i <= 4; $i++) {
                                            $respuesta = $row["respuesta_$i"];
                                            if ($respuesta) {
                                                echo "<div class='form-check' style='margin-bottom: 10px;'>";
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
                                                echo "<div class='form-check' style='margin-bottom: 10px;'>";
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
                        echo "</div>";
                    ?>
                    <div class="text-center">
                        <button type="submit" class="btn mt-3" id="btn_EnviarRespuesta">Enviar respuestas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AdminLTE JavaScript -->
    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>

    <script>
        //Para regreasr al panel de admin
        document.getElementById('adm_regresar').addEventListener('click', function () {
            window.location.href = 'admin.php';
        });
    </script>
</body>

</html>

<?php
// Cerrar la conexión
$conn->close();
?>