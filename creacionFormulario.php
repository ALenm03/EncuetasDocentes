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

<body class="body" style="padding-top: 120px;">
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
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header nombre_del_from_bg">
                        <div class="card-title nombre_del_from text-center w-100">
                            <h3>Ingresar Nombre del Formulario</h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="txtcaja">Ingrese el nombre del Formulario</label>
                            <input type="text" class="form-control" maxlength="50" placeholder="Escribe aqui el nombre del formulario" id="txtcaja">
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
                            <h3>Editar FORMULARIO X</h3>
                        </div>
                    </div>
                    <div class="card-body asd" id="card-body-preguntas">
                        <div class="Contenedor_Btn_Agregar_Pregunta d-flex">
                            <button type="button" class="btn Btn_Agregar_Pregunta" >Agregar Pregunta</button>
                            <button type="button" class="btn Btn_Guardar_Encuesta" id="guardar_formulario">Guardar Encuesta</button>
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
    <script src="assets/AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.all.min.js"></script>

    <script>
        const formContainer = document.getElementById('card-body-preguntas');

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.Btn_Agregar_Pregunta').addEventListener('click', Agregar_Pregunta);

            Agregar_Pregunta();
            Agregar_Pregunta();
            cambiarACheckbox('P2');
            Agregar_Pregunta();
            cambiarA1Opcion('P3');
        });

        // Contador de preguntas actuales
        let preguntaCounter = 0;
        let PreguntaX = 0;

        // Función para actualizar los números de las preguntas en los labels
        function actualizarNumerosDePreguntas() {
            const preguntas = document.querySelectorAll('.Pregunta');
            preguntas.forEach((pregunta, index) => {
                const label = pregunta.querySelector('.label_pregunta');
                label.textContent = `Pregunta ${index + 1}`;
            });
        }

        // Función para agregar una nueva pregunta
        function Agregar_Pregunta() {
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
                    <input type="text" class="form-control" name="Pregunta" id="nombre_de_pregunta_${PreguntaX}" style="margin-bottom: 30px;" placeholder="Escribe la pregunta" Value="Escribe tu pregunta en este campo">
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
                    actualizarNumerosDePreguntas();
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

                actualizarNumerosDePreguntas();
            }
            else {
                alert('Se ha alcanzado el número máximo de 10 preguntas.');
            }
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
                if (valor === '' && i < 3) {
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                        <input type="checkbox" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                        <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}" value="Opcion${i}">
                    </div>
                    `;
                } else if (valor) {
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                        <input type="checkbox" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                        <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}" value="${valor}">
                    </div>
                    `;
                }
                else{
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                        <input type="checkbox" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                        <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}" >
                    </div>
                    `;
                }
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
                if (valor === '' && i < 3) {
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                        <input type="radio" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                        <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}" value="Opcion${i}">
                    </div>
                    `;
                } else if (valor) {
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                        <input type="radio" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                        <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}" value="${valor}">
                    </div>
                    `;
                }
                else {
                    contenedor.innerHTML += `
                    <div class=" d-flex respuesta" id="form-group${preguntaId}_${i}" style="justify-content: flex-start;">
                        <input type="radio" class="Item_Form_Group" id="opcion${preguntaId}_${i}" disabled>
                        <input type="text" class="Item_Form_Group form-control" id="respuesta${preguntaId}_${i}">
                    </div>
                    `;
                }
            }
        }
        
        // Función para guardar el formulario usando AJAX
        // Función para guardar el formulario usando AJAX
        document.getElementById('guardar_formulario').addEventListener('click', function () {
            const nombreFormulario = document.getElementById('txtcaja').value;
            let ChecarEspacios = nombreFormulario;
            const preguntas = formContainer.querySelectorAll('.form-group');
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
                    return;
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
                    Swal.fire('Encuesta guardada exitosamente.', '', 'success').then(() => {
                        window.location.href = 'admin.php';
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error al guardar el formulario.');
                }
            });
        });


        document.getElementById('adm_regresar').addEventListener('click', function () {
            //Alerta con sweetalert para guardar el formulario antes de salir
            Swal.fire({
                title: '¿Estás seguro de que deseas salir?',
                text: "No se guardará el formulario",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#777DA7',
                confirmButtonText: 'Salir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'admin.php';
                }
            });
        });
    </script>
</body>
</html>