<?php
include "../app/config.php";
header('Content-Type: application/json');

if (isset($_GET['nombre'])) {
    $nombreCliente = urldecode($_GET['nombre']);

    $consulta = $pdo->prepare("SELECT nombre_cliente, direccion, telefono, puntos_fidelidad FROM clientes WHERE nombre_cliente = :nombre");
    $consulta->execute(['nombre' => $nombreCliente]);
    $detalles = $consulta->fetch();

    if ($detalles) {
        $nombre_cliente = $detalles['nombre_cliente'];
        $direccion = $detalles['direccion'];
        $telefono = $detalles['telefono'];
        $puntos_fidelidad = $detalles['puntos_fidelidad'] ?? 0;

        echo json_encode([
            'nombre_cliente' => $nombre_cliente,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'puntos_fidelidad' => $puntos_fidelidad
        ]);
    } else {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['error' => 'No se encontraron detalles del cliente']);
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo json_encode(['error' => 'Nombre de cliente no especificado']);
}

