<?php
include "../app/config.php";
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $nombreRestaurante = urldecode($_GET['id']);

    $consulta = $pdo->prepare("SELECT nombre, direccion, horario, contacto, imagen_logo, horarios_flexibles_restaurantes FROM restaurantes WHERE nombre = :nombre AND Estado = 1");
    $consulta->execute(['nombre' => $nombreRestaurante]);
    $detalles = $consulta->fetch();

    if ($detalles) {
        $nombre = $detalles['nombre'];
        $direccion = $detalles['direccion'];
        $horario = $detalles['horario'];
        $contacto = $detalles['contacto'];
        $imagen_logo = $detalles['imagen_logo'];
        $horarios_flexibles = $detalles['horarios_flexibles_restaurantes'] ?? 0;

        echo json_encode([
            'nombre' => $nombre,
            'direccion' => $direccion,
            'horario' => $horario,
            'contacto' => $contacto,
            'imagen_logo' => $imagen_logo,
            'horarios_flexibles' => $horarios_flexibles
        ]);
    } else {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['error' => 'No se encontraron detalles del restaurante']);
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo json_encode(['error' => 'Nombre de restaurante no especificado']);
}

