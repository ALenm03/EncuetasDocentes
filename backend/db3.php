<?php
// db.php
try {
    $db = new PDO("mysql:host=localhost;dbname=bdform", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos";
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
