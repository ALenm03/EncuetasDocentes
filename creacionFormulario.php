<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión y si es admin
if (!isset($_SESSION['user_id'])) {
    // Si no está autenticado o no es admin, redirigir a index.html
    header("Location: index.html");
    exit();
}
if ($_SESSION['user_role'] == 'user') {
    header("Location: usuario_normal.php");
    exit();
}
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
<body class="body"  style="padding-top: 150px;">

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
                            <h1>Ingresar Nombre del Formulario</h1>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="txtcaja">Ingrese el nombre del Formulario</label>
                            <input type="text" class="form-control" maxlength="50" placeholder="Nombre del formulario" id="txtcaja"> 
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
                            <h1>Editar FORMULARIO X</h1>
                        </div>
                    </div>
                    <div class="card-body asd" id="card-body-preguntas">

                        <div class="Contenedor_Btn_Agregar_Pregunta d-flex">
                            <button class="btn Btn_Agregar_Pregunta">Agregar Pregunta</button>
                            <button class="btn Btn_Guardar_Encuesta" id="guardar_formulario">Guardar Encuesta</button>
                            <button type="button" class="btn btn_guardar_form" id="guardar_formulario">Guardar Formulario</button>
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

        //Funcion que hace jalar los botones para eliminar opciones
        function Identificar_Btns_Eliminar() {
            document.querySelectorAll('[id^="eliminar_form-groupP"]').forEach(button => {
                button.addEventListener('click', function() {
                    const idToDelete = this.id.replace('eliminar_', ''); // Remueve 'eliminar_' para obtener el ID del form-group
                    const formGroup = document.getElementById(idToDelete);
                    if (formGroup) {
                        formGroup.remove(); // Elimina el form-group del DOM
                    }
                });
            });
        }
        // Función para actualizar los números de las preguntas en los labels
        function actualizarNumerosDePreguntas() {
            const preguntas = document.querySelectorAll('.Pregunta');
            preguntas.forEach((pregunta, index) => {
                const label = pregunta.querySelector('.label_pregunta');
                label.textContent = `Pregunta ${index + 1}`;
            });
        }
         Agregar_Pregunta();
        // Función para agregar una nueva pregunta
        function Agregar_Pregunta(){
            document.querySelector('.Btn_Agregar_Pregunta').addEventListener('click', function() {
            if (preguntaCounter < 10) {
                preguntaCounter++;
                const newPreguntaId = `P${preguntaCounter}`;

                // Crea el nuevo div de pregunta
                const newPregunta = document.createElement('div');
                newPregunta.className = 'form-group Pregunta d-flex';
                newPregunta.id = newPreguntaId;
                newPregunta.style = 'margin-bottom: 30px;';

                newPregunta.innerHTML = `
                    <div class="Boton_Borrar_PreguntaP1 d-flex" id="boton_borrar_${newPreguntaId}" style="flex-wrap: wrap;">
                        <button class="btn btn-danger Btn_Eliminar_Pregunta" title="Eliminar Pregunta ${preguntaCounter}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="Contenido_De_Cada_Pregunta" style="width: 90%;">
                        <label for="nombre_de_pregunta_${preguntaCounter}" class="label_pregunta">Pregunta ${preguntaCounter}</label>
                        <input type="text" class="form-control" name="Pregunta" id="nombre_de_pregunta_${preguntaCounter}" style="margin-bottom: 30px;" placeholder="Escribe la pregunta">
                        <div id="contenedor_de_respuestas_${newPreguntaId}">
                            <div class=" d-flex respuesta" id="form-group${newPreguntaId}_1" style="justify-content: flex-start;">                       
                                <input type="radio" class="Item_Form_Group" id="opcion${newPreguntaId}_1" disabled>
                                <input type="text" class="Item_Form_Group form-control" id="respuesta${newPreguntaId}_1">
                            </div>
                            <div class=" d-flex respuesta" id="form-group${newPreguntaId}_2" style="justify-content: flex-start;">
                                <input type="radio" class="Item_Form_Group" id="opcion${newPreguntaId}_2" disabled>
                                <input type="text" class="Item_Form_Group form-control" id="respuesta${newPreguntaId}_2">
                            </div>
                            <div class=" d-flex respuesta" id="form-group${newPreguntaId}_3" style="justify-content: flex-start;">
                                <button class="btn btn-danger fas fa-times Item_Form_Group" id="eliminar_form-group${newPreguntaId}_3" title="Eliminar Opción"></button>
                                <input type="radio" class="Item_Form_Group" id="opcion${newPreguntaId}_3" disabled>
                                <input type="text" class="Item_Form_Group form-control" id="respuesta${newPreguntaId}_3">
                            </div>
                            <div class=" d-flex respuesta" id="form-group${newPreguntaId}_4" style="justify-content: flex-start;">
                                <button class="btn btn-danger fas fa-times Item_Form_Group" id="eliminar_form-group${newPreguntaId}_4" title="Eliminar Opción"></button>
                                <input type="radio" class="Item_Form_Group" id="opcion${newPreguntaId}_4" disabled>
                                <input type="text" class="Item_Form_Group form-control" id="respuesta${newPreguntaId}_4">
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
                Identificar_Btns_Eliminar();


                // Añadir evento para borrar la nueva pregunta
                document.getElementById(`boton_borrar_${newPreguntaId}`).addEventListener('click', function() {
                    document.getElementById(newPreguntaId).remove();
                    preguntaCounter--;
                    actualizarNumerosDePreguntas();
                });

                //Eventos a los botones de cambiar opciones
                document.getElementById(`cambiar_incisos_a_texto_${newPreguntaId}`).addEventListener('click', function() {
                    cambiarATexto(newPreguntaId)
                });
                document.getElementById(`cambiar_incisos_a_checkbox_${newPreguntaId}`).addEventListener('click', function() {
                    cambiarACheckbox(newPreguntaId); 
                    Identificar_Btns_Eliminar();                                                                
                });
                document.getElementById(`cambiar_incisos_a_1opcion_${newPreguntaId}`).addEventListener('click', function() {
                    cambiarA1Opcion(newPreguntaId);
                    Identificar_Btns_Eliminar();
                });

                actualizarNumerosDePreguntas();
            }
            else 
            {
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
            contenedor.innerHTML = '';
            for (let i = 1; i <= 4; i++) {
                if (i <= 2){
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                        <input type="checkbox" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                        <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}">
                    </div>
                `;
                }
                else {
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                        <button class="btn btn-danger fas fa-times Item_Form_Group" id="eliminar_form-group${preguntaId}_${i}" title="Eliminar Opción"></button>
                        <input type="checkbox" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                        <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}">
                    </div>
                `;
                }
            }
        }

        //Funcion para cambiar las opciones a Opcion Multiple que solo acepta una respuesta
        function cambiarA1Opcion(preguntaId) {
            const contenedor = document.getElementById(`contenedor_de_respuestas_${preguntaId}`);
            contenedor.innerHTML = '';
            for (let i = 1; i <= 4; i++) {
                if (i <= 2){
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-groupP1_1" style="justify-content: flex-start;">                       
                        <input type="radio" class="Item_Form_Group" id="opcionP1_1" disabled="">
                        <input type="text" class="Item_Form_Group form-control" id="respuestaP1_1">
                    </div>
                `;
                }
                else {
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-groupP1_3" style="justify-content: flex-start;">
                        <button class="btn btn-danger fas fa-times Item_Form_Group" id="eliminar_form-groupP1_3" title="Eliminar Opción"></button>
                        <input type="radio" class="Item_Form_Group" id="opcionP1_3" disabled="">
                        <input type="text" class="Item_Form_Group form-control" id="respuestaP1_3">
                    </div>
                `;
                }
            }
        }














        

        // Función para guardar el formulario usando AJAX
       // Función para guardar el formulario usando AJAX
    document.getElementById('guardar_formulario').addEventListener('click', function () {


    const nombreFormulario = document.getElementById('txtcaja').value;
    const preguntas = formContainer.querySelectorAll('.form-group');
    const datosPreguntas = [];

    let formularioValido = true; // Bandera para verificar si el formulario es válido


    preguntas.forEach((pregunta, index) => {
        const preguntaTexto = pregunta.querySelector('input[type="text"]').value;
        const respuestas = pregunta.querySelectorAll('.respuesta');
        const datosRespuestas = [];

        // Validar que la pregunta tenga al menos una respuesta
        if (respuestas.length === 0) {
            alert(`La pregunta número ${index + 1} debe tener al menos una respuesta.`);
            formularioValido = false;
            return; // Salir del ciclo de esta pregunta
        }

        // Capturar el tipo de respuesta (por defecto será "parrafo")
        let tipoRespuesta = 'parrafo'; // Tipo por defecto

        if (respuestas.length < 2 && (pregunta.querySelector('input[type="checkbox"]') || pregunta.querySelector('input[type="radio"]'))) {
            alert(`La pregunta número ${index + 1} debe tener al menos dos respuestas para opción múltiple o checkbox.`);
            formularioValido = false;
            return;
        }

        respuestas.forEach(respuesta => {
            if (respuesta.querySelector('textarea')) {
                // Respuesta tipo párrafo
                const textoRespuesta = respuesta.querySelector('textarea').value;
                datosRespuestas.push(textoRespuesta);
                tipoRespuesta = 'parrafo';
            } else if (respuesta.querySelector('input[type="checkbox"]')) {
                // Respuesta tipo checkbox
                const textoOpcion = respuesta.querySelector('input[type="text"]').value;
                if (textoOpcion.trim() === "") {
                    alert(`Las opciones de checkbox en la pregunta número ${index + 1} no pueden estar vacías.`);
                    formularioValido = false;
                    return;
                }
                datosRespuestas.push(textoOpcion);
                tipoRespuesta = 'checkbox';
            } else if (respuesta.querySelector('input[type="radio"]')) {
                // Respuesta tipo opción múltiple
                const textoOpcion = respuesta.querySelector('input[type="text"]').value;
                if (textoOpcion.trim() === "") {
                    alert(`Las opciones de opción múltiple en la pregunta número ${index + 1} no pueden estar vacías.`);
                    formularioValido = false;
                    return;
                }
                datosRespuestas.push(textoOpcion);
                tipoRespuesta = 'opcion_multiple';
            }
        });

        datosPreguntas.push({
            pregunta: preguntaTexto,
            respuestas: datosRespuestas,
            tipo_respuesta: tipoRespuesta // Guardar el tipo de respuesta
        });
    });

    if (preguntaCounter <= 0) {
            formularioValido = false;
            alert('No hay preguntas'); // Muestra la alerta
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


        document.getElementById('adm_regresar').addEventListener('click', function() {
        window.location.href = 'admin.php'; 
    });
    </script>

</body>
</html>


