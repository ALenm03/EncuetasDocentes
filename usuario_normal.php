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
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="assets/stylesUsr.css">
    <title>Usuario Normal</title>
</head>
<body id="adm_body">

    <!-- Header fijo con el botón de Cerrar Sesión -->
    <header class="p-3 fixed-top">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <h1 class="h5 m-0" style="color:white;">Panel de Administrador</h1>
            </div>
            
            <div class="d-flex">
                <form action="backend/logout.php" method="POST">
                    <button class="btn btn-primary" type="submit" id="adm_logout">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </header>

    
    <script src="assets/AdminLTE-3.2.0/plugins/jquery/jquery.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/datatables/jquery.dataTables.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/datatables-responsive/js/dataTables.responsive.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/datatables-responsive/js/responsive.bootstrap4.js"></script>
    <script src="assets/AdminLTE-3.2.0/plugins/sweetalert2/sweetalert2.all.min.js"></script>


</body>
</html>
