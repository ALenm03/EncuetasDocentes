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
    <title>Formulario Dinámico</title>
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body class="body" style="padding-top: 150px;">

    <div id="header_pagina">
        <h2>Panel de Administrador</h2>
        <div class="header-buttons">
            <button id="adm_regresar">Regresar</button>
            <form action="backend/logout.php" method="POST">
                <button type="submit" id="adm_logout">Cerrar sesión</button>
            </form>
        </div>

    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header nombre_del_from_bg">
                        <div class="card-title nombre_del_from text-center w-100">
                            <h1>Editar nombre del Formulario: <?php echo htmlspecialchars($nombreFormulario);?></h1>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="txtcaja">Edite el nombre</label>
                            <input type="text" class="form-control" maxlength="50" placeholder="Nuevo nombre del formulario"
                                id="txtcaja">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header nombre_del_from_bg">
                        <div class="card-title nombre_del_from text-center w-100">
                            <h1>Preguntas de <?php echo htmlspecialchars($nombreFormulario); ?></h1>
                        </div>
                    </div>
                    <div class="card-body asd" id="card-body-preguntas">
                        <?php
                            // Iterar sobre las preguntas y mostrar campos editables para preguntas y respuestas
                            while ($row = $result->fetch_assoc()) {
                            echo "<div class='form-group Pregunta d-flex' id='P". htmlspecialchars($row['pregunta_num']) ."' style = 'margin-bottom: 30px;'>";
                                echo "<div class='Boton_Borrar_PreguntaP1 d-flex' style='flex-wrap: wrap;'>" ;
                                    echo "<button class='btn btn-danger Btn_Eliminar_Pregunta' id='boton_borrar_P". htmlspecialchars($row['pregunta_num']) ."' title='Eliminar Pregunta'>";
                                        echo "<i class='fas fa-trash'></i>";
                                    echo "</button>";
                                echo"</div>";

                                echo "<div class='Contenido_De_Cada_Pregunta' style='width: 90%;'> ";
                                    echo "<label class='label_pregunta'>Pregunta " . htmlspecialchars($row['pregunta_num']) . "</label>";
                                    echo "<input type='text' name='pregunta_{$row['pregunta_num']}' value='".htmlspecialchars($row['pregunta'])."' class='form-control' id='nombre_de_pregunta_". htmlspecialchars($row['pregunta_num']) ."' style='margin-bottom: 30px'>";


                                echo "<div id='contenedor_de_respuestas_P".htmlspecialchars($row['pregunta'])."'>";

                                        switch ($row['tipo_respuesta']) {
                                            
                                            case 'parrafo':
                                                echo"<div class=' d-flex respuesta' id='P{$row['pregunta_num']}_1' style='justify-content: flex-start;'>";
                                                    echo "<textarea name='respuesta_{$row['pregunta_num']}_1' class='form-control' rows='3' disabled>" . htmlspecialchars($row['respuesta_1']) . "</textarea>";
                                                echo"</div>";
                                                break;

                                            case 'opcion_multiple':
                                                for ($i = 1; $i <= 4; $i++) {
                                                    $respuesta = $row["respuesta_$i"];
                                                    if ($respuesta) {
                                                        echo"<div class=' d-flex respuesta' id='P{$row['pregunta_num']}_$i' style='justify-content: flex-start;'>";
                                                        echo "<input type='radio' class='Item_Form_Group' name='respuesta_P{$row['pregunta_num']}_$i' disabled>";
                                                        echo "<input type='text' class='Item_Form_Group form-control' name='respuesta_{$row['pregunta_num']}_$i' value='" . htmlspecialchars($respuesta) . "' class='form-control mb-2'>";
                                                        echo"</div>";
                                                    }
                                                    else {
                                                        echo"<div class=' d-flex respuesta' id='P{$row['pregunta_num']}_$i' style='justify-content: flex-start;'>";
                                                        echo "<input type='radio' class='Item_Form_Group' name='respuesta_P{$row['pregunta_num']}_$i' disabled>";
                                                        echo "<input type='text' class='Item_Form_Group form-control' name='respuesta_{$row['pregunta_num']}_$i' class='form-control mb-2'>";
                                                        echo"</div>";
                                                    }
                                                }
                                                break;
                                            case 'checkbox':
                                                for ($i = 1; $i <= 4; $i++) {
                                                    $respuesta = $row["respuesta_$i"];
                                                    if ($respuesta) {
                                                        echo"<div class=' d-flex respuesta' id='P{$row['pregunta_num']}_$i' style='justify-content: flex-start;'>";
                                                        echo "<input type='checkbox' class='Item_Form_Group' name='respuesta_P{$row['pregunta_num']}_$i' disabled>";
                                                        echo "<input type='text' class='Item_Form_Group form-control' name='respuesta_{$row['pregunta_num']}_$i' value='" . htmlspecialchars($respuesta) . "' class='form-control mb-2'>";
                                                        echo"</div>";
                                                    }
                                                    else {
                                                        echo"<div class=' d-flex respuesta' id='P{$row['pregunta_num']}_$i' style='justify-content: flex-start;'>";
                                                        echo "<input type='checkbox' class='Item_Form_Group' name='respuesta_P{$row['pregunta_num']}_$i' disabled>";
                                                        echo "<input type='text' class='Item_Form_Group form-control' name='respuesta_{$row['pregunta_num']}_$i' class='form-control mb-2'>";
                                                        echo"</div>";
                                                    }
                                                }
                                                break;

                                            default:
                                                echo "<p>Tipo de respuesta no definido.</p>";
                                                break;
                                        }
                                echo"</div>";
        
                                echo"<div class='d-flex' style='flex-wrap: wrap; justify-content: center'>";
                                    echo "<button class='btn Btn_Cambiar_a_Texto' id='cambiar_incisos_a_texto_P".htmlspecialchars($row['pregunta'])."'>Cambiar a Texto</button>";
                                    echo "<button class='btn Btn_Cambiar_a_Checkbox' id='cambiar_incisos_a_checkbox_P".htmlspecialchars($row['pregunta'])."'>Cambiar a checkbox</button>";
                                    echo "<button class='btn Btn_Cambiar_a_OpMul' id='cambiar_incisos_a_1opcion_P".htmlspecialchars($row['pregunta'])."'>Cambiar a Opción Múltiple</button>";
                                echo"</div>";


                                echo "</div>";
                            echo "</div>"; 

                                    /*
                                    echo "<input type='text' name='pregunta_{$row['pregunta_num']}' value='" . htmlspecialchars($row['pregunta']) . "' class='form-control'>";
                                    
                                    */
                                }?>
                        <div class="Contenedor_Btn_Agregar_Pregunta d-flex">
                            <button type="button" class="btn Btn_Agregar_Pregunta" >Agregar Pregunta</button>
                            <button type="button" class="btn Btn_Guardar_Encuesta" id="guardar_formulario">Actualizar Encuesta</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AdminTLE JavaScript -->
    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/AdminLTE-3.2.0/dist/js/adminlte.js"></script>
    <script>
        const formContainer = document.getElementById('card-body-preguntas');

        // Contador de preguntas actuales
        let preguntaCounter = 0;
        let PreguntaX = 0;

        //Cada vez que se cargue completamente el formulario se ejecutara la funcion para contar las preguntas y agregar las fuinciones a los botones
        document.addEventListener('DOMContentLoaded', function() {
            const totalPreguntas = document.getElementsByClassName('Pregunta').length;
            console.log('Total de preguntas:', totalPreguntas);
            preguntaCounter = totalPreguntas;
            console.log('Total de preguntaCounter:', preguntaCounter);
            PreguntaX = totalPreguntas;
            console.log('Total de PreguntaX:', PreguntaX);


            const BotonesDeEliminarDelFormularioCargado = document.querySelectorAll('.Btn_Eliminar_Pregunta');
            BotonesDeEliminarDelFormularioCargado.forEach(button => {
                button.addEventListener('click', function() {
                    const preguntaId = this.id.replace('boton_borrar_', '');
                    document.getElementById(preguntaId).remove();
                    preguntaCounter--;
                    actualizarLabels();
                });
            });

            const cambiarTextoButtons = document.querySelectorAll('.Btn_Cambiar_a_Texto');
            cambiarTextoButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const preguntaId = this.id.replace('cambiar_incisos_a_texto_', '');
                    cambiarATexto(preguntaId);
                });
            });

            const cambiarCheckboxButtons = document.querySelectorAll('.Btn_Cambiar_a_Checkbox');
            cambiarCheckboxButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const preguntaId = this.id.replace('cambiar_incisos_a_checkbox_', '');
                    cambiarACheckbox(preguntaId);
                });
            });

            const cambiarOpMulButtons = document.querySelectorAll('.Btn_Cambiar_a_OpMul');
            cambiarOpMulButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const preguntaId = this.id.replace('cambiar_incisos_a_1opcion_', '');
                    cambiarA1Opcion(preguntaId);
                });
            });
        });

        // Función para actualizar los números de las preguntas en los labels
        function actualizarLabels() {
            const preguntas = document.querySelectorAll('.Pregunta');
            preguntas.forEach((pregunta, index) => {
                const label = pregunta.querySelector('.label_pregunta');
                label.textContent = `Pregunta ${index + 1}`;
            });
        }
        Agregar_Pregunta();
        // Función para agregar una nueva pregunta
        function Agregar_Pregunta() {
            document.querySelector('.Btn_Agregar_Pregunta').addEventListener('click', function () {
                if (preguntaCounter < 10) {
                    preguntaCounter++;
                    PreguntaX++;
                    const newPreguntaId = `P${PreguntaX}`;

                    // Crea el nuevo div de pregunta
                    const newPregunta = document.createElement('div');
                    newPregunta.className = 'form-group Pregunta d-flex';
                    newPregunta.id = newPreguntaId;
                    newPregunta.style = 'margin-bottom: 30px;';
                    newPregunta.innerHTML = `
                    <div class="Boton_Borrar_PreguntaP1 d-flex" id="boton_borrar_${newPreguntaId}" style="flex-wrap: wrap;">
                        <button class="btn btn-danger Btn_Eliminar_Pregunta" title="Eliminar Pregunta">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="Contenido_De_Cada_Pregunta" style="width: 90%;">
                        <label for="nombre_de_pregunta_${PreguntaX}" class="label_pregunta">Pregunta ${PreguntaX}</label>
                        <input type="text" class="form-control" name="Pregunta" id="nombre_de_pregunta_${PreguntaX}" style="margin-bottom: 30px;" placeholder="Escribe la pregunta">

                        <div id="contenedor_de_respuestas_${newPreguntaId}">
                            <div class=" d-flex respuesta" style="justify-content: flex-start;">
                                <textarea class="Item_Form_Group form-control" id="respuesta${newPreguntaId}" rows="4" style="max-width: 50%; max-height:100px; min-height:100px;" readonly></textarea>
                            </div>
                        </div>

                        <div class="d-flex" style="flex-wrap: wrap; justify-content: center">
                            <button class="btn Btn_Cambiar_a_Texto" id="cambiar_incisos_a_texto_${newPreguntaId}">Cambiar a Texto</button>
                            <button class="btn Btn_Cambiar_a_Checkbox" id="cambiar_incisos_a_checkbox_${newPreguntaId}">Cambiar a checkbox</button>
                            <button class="btn Btn_Cambiar_a_OpMul" id="cambiar_incisos_a_1opcion_${newPreguntaId}">Cambiar a Opción Múltiple</button>
                        </div>
                    </div>
                `;

                    // Añadir la nueva pregunta al contenedor de la card-body
                    document.querySelector('#card-body-preguntas').insertBefore(newPregunta, document.querySelector('.Contenedor_Btn_Agregar_Pregunta'));


                    // Añadir evento para borrar la nueva pregunta
                    document.getElementById(`boton_borrar_${newPreguntaId}`).addEventListener('click', function () {
                        document.getElementById(newPreguntaId).remove();
                        preguntaCounter--;
                        actualizarLabels();
                    });
                    //Eventos a los botones de cambiar opciones
                    document.getElementById(`cambiar_incisos_a_texto_${newPreguntaId}`).addEventListener('click', function () {
                        cambiarATexto(newPreguntaId)
                    });
                    document.getElementById(`cambiar_incisos_a_checkbox_${newPreguntaId}`).addEventListener('click', function () {
                        cambiarACheckbox(newPreguntaId);
                    });
                    document.getElementById(`cambiar_incisos_a_1opcion_${newPreguntaId}`).addEventListener('click', function () {
                        cambiarA1Opcion(newPreguntaId);
                    });

                    actualizarLabels();
                }
                else {
                    alert('Se ha alcanzado el número máximo de 10 preguntas.');
                }
            });
        }
        // Funciones para cambiar a campo de texto
        function cambiarATexto(preguntaId) {
            const contenedor = document.getElementById(`contenedor_de_respuestas_${preguntaId}`);
            contenedor.innerHTML = `
                <div class=" d-flex respuesta" style="justify-content: flex-start;">
                    <textarea class="Item_Form_Group form-control" id="respuesta${preguntaId}" rows="4" style="max-width: 50%; max-height:100px; min-height:100px;" readonly></textarea>
                </div>
            `;
        }
        //Funcion para cambiar las opciones a Checkbox
        function cambiarACheckbox(preguntaId) {
            const contenedor = document.getElementById(`contenedor_de_respuestas_${preguntaId}`);
            const respuestasExistentes = contenedor.querySelectorAll('.respuesta input[type="text"]');
            const respuestasValores = Array.from(respuestasExistentes).map(input => input.value);
            contenedor.innerHTML = '';
            for (let i = 1; i <= 4; i++) {
                const valor = respuestasValores[i - 1] || '';
                contenedor.innerHTML += `
                <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                    <input type="checkbox" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                    <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}" value="${valor}">
                </div>
                `;
            }
        }

        //Funcion para cambiar las opciones a Opcion Multiple que solo acepta una respuesta
        function cambiarA1Opcion(preguntaId) {
            const contenedor = document.getElementById(`contenedor_de_respuestas_${preguntaId}`);
            const respuestasExistentes = contenedor.querySelectorAll('.respuesta input[type="text"]');
            const respuestasValores = Array.from(respuestasExistentes).map(input => input.value);
            contenedor.innerHTML = '';
            for (let i = 1; i <= 4; i++) {
                const valor = respuestasValores[i - 1] || '';
                contenedor.innerHTML += `
                <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                    <input type="radio" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                    <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}" value="${valor}">
                </div>
                `;
            }
        }
        //Regrear a la pagina de admin
        document.getElementById('adm_regresar').addEventListener('click', function () {
            window.location.href = 'admin.php';
        });
        //Guardar Formulario
        document.getElementById('guardar_formulario').addEventListener('click', function () {
            const nombreFormulario = document.getElementById('txtcaja').value;
            let ChecarEspacios = nombreFormulario;
            const preguntas = formContainer.querySelectorAll('.Pregunta');
            const datosPreguntas = [];


            let formularioValido = true; // Bandera para verificar si el formulario es válido

            
            preguntas.forEach((pregunta, index) => {
                const preguntaTexto = pregunta.querySelector('input[type="text"]').value;
                const respuestas = pregunta.querySelectorAll('.respuesta');
                const datosRespuestas = [];
                let tipoRespuesta = 'parrafo';
                let OpSinRespuesta = 0; //Cada pregunta inicia con un contador de respuestas vacias en 0
                
                if (preguntaTexto.trim() === "") {
                    formularioValido = false;
                    alert('No puede haber preguntas sin Nombre'); // Muestra la alerta
                }
                respuestas.forEach(respuesta => {
                    //TEXTO
                    if (respuesta.querySelector('textarea')) {
                        datosRespuestas.push(null);
                        tipoRespuesta = 'parrafo';
                    } //CHECKBOX
                    else if (respuesta.querySelector('input[type="checkbox"]')) {
                        const textoOpcion = respuesta.querySelector('input[type="text"]').value;
                        //Si la opcion esta vacia
                        if (textoOpcion.trim() === "") {
                            tipoRespuesta = 'checkbox'; //cambia el tipo de respuesta
                            OpSinRespuesta++; //suma 1 a la cant de opciones vacias
                            if (OpSinRespuesta == 3) { //Si llega a contar 3 opciones vacias hace el formulario invalido
                                alert(`Debe de haber almenos 2 opciones escritas en la Pregunta ${index + 1}.`);
                                formularioValido = false;
                                return;
                            }
                            return;
                        }
                        else //Si no esta vacia guarda la respuesta
                        {
                            datosRespuestas.push(textoOpcion);
                            tipoRespuesta = 'checkbox';
                        }
                    } //OpMULT lo mismo pero con OpMultiple
                    else if (respuesta.querySelector('input[type="radio"]')) {
                        const textoOpcion = respuesta.querySelector('input[type="text"]').value;
                        if (textoOpcion.trim() === "") {
                            tipoRespuesta = 'opcion_multiple';
                            OpSinRespuesta++;
                            if (OpSinRespuesta == 3){
                                alert(`Debe de haber almenos 2 opciones escritas en la Pregunta ${index + 1}.`);
                                formularioValido = false;
                                return;
                            }
                        }else {
                            datosRespuestas.push(textoOpcion);
                            tipoRespuesta = 'opcion_multiple';
                        }
                    }
                });

                datosPreguntas.push({
                    pregunta: preguntaTexto,
                    respuestas: datosRespuestas,
                    tipo_respuesta: tipoRespuesta // Guardar el tipo de respuesta
                });
            });
            //Si no hay preguntas no se guarda
            if (preguntaCounter <= 0) {
                formularioValido = false;
                alert('No hay preguntas');
            }
            //Si no hay nombre de formulario no se guarda
            if (nombreFormulario.trim() === "") {
                formularioValido = false;
                alert('No hay nombre del formulario');
            }
            // Si el formulario no es válido, no se envía
            if (!formularioValido) {
                return;
            }
            // Crear un objeto para enviar
            const formData = {
                nombre_formulario: nombreFormulario,
                preguntas: datosPreguntas
            };

            // Enviar datos al servidor usando AJAX
            $.ajax({
                type: 'POST',
                url: 'backend/guardar_formulario.php',
                data: { formData: JSON.stringify(formData) },
                contentType: 'application/x-www-form-urlencoded',
                success: function (response) {
                    alert('Encuesta guardada exitosamente.'); // Muestra la alerta
                    window.location.href = 'admin.php'; // Redirige a admin.php
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error al guardar el formulario.');
                }
            });
        });
    </script>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
