<?php
$config = require 'config.php';
require_once 'src/Database.php';



$_SESSION['id_usuario'] = 12;
// Crear instancia de la clase Database
$db = new Database($config);

// Consultar para obtener un array con nombres y mensajes
$sql = "SELECT nombre, mensaje FROM usuarios JOIN mensajes ON usuarios.id = mensajes.id_usuario";
$stmt = $db->query($sql);
// Obtener resultados
$result = $stmt->get_result();

// Cerrar la conexión

$db->close();
?>

