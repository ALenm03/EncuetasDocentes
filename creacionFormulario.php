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
                            <h1>Preguntas</h1>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="form-group">
                            <div id="form-preguntas">  </div>

                            <button type="button" class="btn btn_anadir_pregunta" id="anadir_pregunta">Añadir Pregunta</button>
                            <button type="button" class="btn btn_guardar_form" id="guardar_formulario">Guardar Formulario</button>
                            <button type="button" class="btn btn_borrar" id="borrar_pregunta">Borrar Última Pregunta</button>
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
        
        document.getElementById("txtcaja").value = "formulario";  // Aquí establecemos el valor "formulario" en el input con id "txtcaja"


        const formContainer = document.getElementById('form-preguntas');

        // Función para agregar una nueva pregunta
        document.getElementById('anadir_pregunta').addEventListener('click', function () {
            const questionDiv = document.createElement('div');
            questionDiv.classList.add('form-group', 'mb-4');

            // Contar cuántas preguntas existen y asignar el número de la nueva pregunta
            const numPreguntas = formContainer.querySelectorAll('.form-group').length + 1;

            // Crear el label para el número de la pregunta
            const questionLabel = document.createElement('label');
            questionLabel.textContent = `Pregunta número ${numPreguntas}`;
            questionLabel.classList.add('form-label', 'mb-2');

            // Crear el campo para ingresar la pregunta
            const questionInput = document.createElement('input');
            questionInput.setAttribute('type', 'text');
            questionInput.setAttribute('placeholder', 'Escribe la pregunta');
            questionInput.classList.add('form-control', 'mb-2', 'pregunta');

            // Crear el contenedor para los botones de tipo de respuesta
            const buttonGroup = document.createElement('div');
            buttonGroup.classList.add('btn-group', 'mb-2');

            // Variable para almacenar el tipo de respuesta seleccionado
            let tipoSeleccionado = null;

            // Botón para agregar respuesta tipo párrafo
            const btnParrafo = document.createElement('button');
            btnParrafo.classList.add('btn', 'btn-secondary');
            btnParrafo.textContent = 'Añadir Respuesta Párrafo';
            btnParrafo.addEventListener('click', function () {
                if (tipoSeleccionado !== 'parrafo') {
                    borrarRespuestas(questionDiv);
                    tipoSeleccionado = 'parrafo';
                    agregarRespuesta(questionDiv, 'parrafo');
                }
            });

            // Botón para agregar respuesta tipo checkbox
            const btnCheckbox = document.createElement('button');
            btnCheckbox.classList.add('btn', 'btn-secondary');
            btnCheckbox.textContent = 'Añadir Respuesta Checkbox';
            btnCheckbox.addEventListener('click', function () {
                if (tipoSeleccionado !== 'checkbox') {
                    borrarRespuestas(questionDiv);
                    tipoSeleccionado = 'checkbox';
                }
                const respuestas = questionDiv.querySelectorAll('.respuesta');
                if (respuestas.length < 4) {
                    agregarRespuesta(questionDiv, 'checkbox');
                }
            });

            // Botón para agregar respuesta tipo opción múltiple
            const btnOpcionMultiple = document.createElement('button');
            btnOpcionMultiple.classList.add('btn', 'btn-secondary');
            btnOpcionMultiple.textContent = 'Añadir Respuesta Opción Múltiple';
            btnOpcionMultiple.addEventListener('click', function () {
                if (tipoSeleccionado !== 'opcion_multiple') {
                    borrarRespuestas(questionDiv);
                    tipoSeleccionado = 'opcion_multiple';
                }
                const respuestas = questionDiv.querySelectorAll('.respuesta');
                if (respuestas.length < 4) {
                    agregarRespuesta(questionDiv, 'opcion_multiple');
                }
            });

            // Botón para borrar la última respuesta añadida
            const btnBorrarAnterior = document.createElement('button');
            btnBorrarAnterior.classList.add('btn', 'btn-secondary');
            btnBorrarAnterior.textContent = 'Borrar Última Respuesta';
            btnBorrarAnterior.addEventListener('click', function () {
                borrarUltimaRespuesta(questionDiv);
            });

            // Añadir los botones al grupo de botones
            buttonGroup.appendChild(btnParrafo);
            buttonGroup.appendChild(btnCheckbox);
            buttonGroup.appendChild(btnOpcionMultiple);
            buttonGroup.appendChild(btnBorrarAnterior);

            // Añadir los elementos al contenedor de la pregunta
            questionDiv.appendChild(questionLabel);
            questionDiv.appendChild(questionInput);
            questionDiv.appendChild(buttonGroup);
            formContainer.appendChild(questionDiv);
        });

        // Función para agregar la respuesta según el tipo seleccionado
        function agregarRespuesta(questionDiv, tipoRespuesta) {
            let respuestaDiv;

            if (tipoRespuesta === 'parrafo') {
                respuestaDiv = document.createElement('textarea');
                respuestaDiv.setAttribute('placeholder', 'Escribe tu respuesta aquí...');
                respuestaDiv.classList.add('form-control', 'respuesta', 'mb-2', 'parrafo');
            } else if (tipoRespuesta === 'checkbox') {
                respuestaDiv = document.createElement('div');
                respuestaDiv.classList.add('respuesta', 'mb-2');

                const checkbox = document.createElement('input');
                checkbox.setAttribute('type', 'checkbox');
                checkbox.classList.add('mr-2');

                const optionInput = document.createElement('input');
                optionInput.setAttribute('type', 'text');
                optionInput.setAttribute('placeholder', 'Escribe una opción');
                optionInput.classList.add('form-control', 'd-inline-block', 'w-50', 'respuesta-input');

                respuestaDiv.appendChild(checkbox);
                respuestaDiv.appendChild(optionInput);
            } else if (tipoRespuesta === 'opcion_multiple') {
                respuestaDiv = document.createElement('div');
                respuestaDiv.classList.add('respuesta', 'mb-2');

                const radio1 = document.createElement('input');
                radio1.setAttribute('type', 'radio');
                radio1.setAttribute('name', 'opcionMultiple');
                radio1.classList.add('mr-2');

                const optionInput1 = document.createElement('input');
                optionInput1.setAttribute('type', 'text');
                optionInput1.setAttribute('placeholder', 'Escribe una opción');
                optionInput1.classList.add('form-control', 'd-inline-block', 'w-50', 'respuesta-input');

                respuestaDiv.appendChild(radio1);
                respuestaDiv.appendChild(optionInput1);
                respuestaDiv.appendChild(document.createElement('br'));
            }
            questionDiv.appendChild(respuestaDiv);
        }

        // Función para borrar todas las respuestas de una pregunta
        function borrarRespuestas(questionDiv) {
            const respuestas = questionDiv.querySelectorAll('.respuesta');
            respuestas.forEach(respuesta => respuesta.remove());
        }

        // Función para borrar la última respuesta añadida
        function borrarUltimaRespuesta(questionDiv) {
            const respuestas = questionDiv.querySelectorAll('.respuesta');
            if (respuestas.length > 0) {
                const ultimaRespuesta = respuestas[respuestas.length - 1];
                ultimaRespuesta.remove();
            }
        }

        // Función para borrar la última pregunta añadida
        document.getElementById('borrar_pregunta').addEventListener('click', function () {
            const preguntas = formContainer.querySelectorAll('.form-group');
            if (preguntas.length > 0) {
                const ultimaPregunta = preguntas[preguntas.length - 1];
                ultimaPregunta.remove();
            }
        });

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
                if (textoRespuesta.trim() === "") {
                    alert(`La respuesta de la pregunta número ${index + 1} no puede estar vacía.`);
                    formularioValido = false;
                    return;
                }
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
            alert(response);
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            alert('Error al guardar el formulario.');
        }
        });

        });

        document.getElementById('adm_regresar').addEventListener('click', function() {
        window.location.href = 'admin.html'; 
    });
    </script>

</body>
</html>