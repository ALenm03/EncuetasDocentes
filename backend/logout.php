<?php
// Iniciar la sesión
session_start();

// Destruir la sesión
session_destroy();

// Redirigir a la página de inicio
header("Location: /EncuetasDocentes/index.html");
exit();
?>
