<?php

use FontLib\Table\Type\loca;

include "../../config.php";



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $nombre_usuario = isset($_POST['nombre_usuario']) ? trim($_POST['nombre_usuario']) : '';
    $nombreCliente = isset($_POST['nombreCliente']) ? trim($_POST['nombreCliente']) : '';
    $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';

    // Validate data (basic example)
    if (empty($nombreCliente) || empty($direccion)) {
        echo "Name and address are required.";
        exit;
    }


    // Consultar para traer el id del usuario por su nombre
    try {
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre = :nombre_usuario LIMIT 1");
        $stmt->execute(['nombre_usuario' => $nombre_usuario]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_usuario_sesion = $userData['id_usuario'] ?? null;
    } catch (Exception $e) {
        echo "Error al consultar el ID del usuario: " . $e->getMessage();
        exit;
    }
/*     echo "El usuario es id: ".$id_usuario_sesion;
    // Process the data (e.g., save to database)
    echo "Name: $nombreCliente \n";
    echo "Address: $direccion \n";
    echo "Phone: $telefono \n"; */
    // Example: Assuming you have a function saveClient that handles database operations
    try {
        $stmt = $pdo->prepare("INSERT INTO clientes (id_usuario, nombre_cliente, direccion, telefono) VALUES (:id_usuario, :nombre_cliente, :direccion, :telefono)");
        $stmt->execute(array(
            ':id_usuario' => $id_usuario_sesion,
            ':nombre_cliente' => $nombreCliente,
            ':direccion' => $direccion,
            ':telefono' => $telefono,
        ));

        echo "Ha sido registrado correctamente";
        
        echo '<meta http-equiv="Refresh" content="2; url=' . $URL . 'login/cliente.php" />';
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

