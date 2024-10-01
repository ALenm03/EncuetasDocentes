<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Si no ha iniciado sesión, redirigir a index.html
    header("Location: index.html");
    exit();
}

// Verificar si el usuario es admin y redirigir si es admin
if ($_SESSION['user_role'] == 'admin') {
    // Si el usuario es admin, redirigir a admin.html
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario Normal</title>
</head>
<body>
    <h1>Eres un usuario normal</h1>
    <img src="img/soy_miembro.jpg" alt="Imagen de usuario normal">
    <!-- Botón de Cerrar Sesión -->
    <form action="backend/logout.php" method="POST">
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>


