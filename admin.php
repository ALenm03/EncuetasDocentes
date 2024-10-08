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
    <title>Admin</title>
</head>
<body>
    <h1>ERES ADMIN</h1>
    <img src="img/soy_admin.jpg" alt="Imagen de admin">
    <!-- Botón de Cerrar Sesión -->
    <form action="backend/logout.php" method="POST">
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
