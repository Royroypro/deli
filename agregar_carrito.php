<?php
session_start();

// Verifica si es un método POST y que los datos necesarios estén presentes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén los datos enviados como JSON
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (isset($data['id_producto'], $data['nombre'], $data['precio'], $data['cantidad'], $data['restaurante'])) {
        $id_producto = $data['id_producto'];
        $nombre = $data['nombre'];
        $precio = $data['precio'];
        $cantidad = $data['cantidad'];
        $restaurante = $data['restaurante']; // Agregado para restaurante

        // Inicializa el carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Agrega o actualiza el producto en el carrito
        if (!isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto] = [
                'id' => $id_producto,
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => $cantidad,
                'restaurante' => $restaurante, // Agregado para restaurante
            ];
        } else {
            $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
        }

        // Respuesta de éxito
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Producto agregado al carrito']);
        exit;
    } else {
        // Respuesta de error por datos incompletos
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        exit;
    }
} else {
    // Respuesta de error por método no permitido
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}
?>

