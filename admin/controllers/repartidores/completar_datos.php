<?php

include ('../../../app/config.php');




try {
    $nombre_usuario = $_POST['nombre_usuario'];
    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $telefono = $_POST['telefono'];
    $vehiculo = $_POST['vehiculo'];

    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre = ?");
    $stmt->execute([$nombre_usuario]);
    $id_usuario_sesion = $stmt->fetchColumn();


    $stmt = $pdo->prepare("INSERT INTO repartidores (id_usuario, id_vehiculo, nombre, apellido_paterno, apellido_materno, telefono, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_usuario_sesion, $vehiculo, $nombre, $apellido_paterno, $apellido_materno, $telefono, 'activo']);

    echo "Ha sido registrado correctamente";

    echo '<meta http-equiv="Refresh" content="2; url=' . $URL . '/login" />';
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

