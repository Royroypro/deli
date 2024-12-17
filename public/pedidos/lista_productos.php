<?php
include('../../app/config.php');

if (isset($_GET['id_restaurante'])) {
    $id_restaurante = intval($_GET['id_restaurante']);
    $query = "SELECT id_producto, id_restaurante, nombre, descripcion, precio, stock, imagen 
              FROM productos 
              WHERE id_restaurante = :id_restaurante AND stock > 0";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id_restaurante' => $id_restaurante]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($productos) {
        echo json_encode(['status' => true, 'productos' => $productos]);
    } else {
        echo json_encode(['status' => false, 'message' => 'No hay productos disponibles']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'ID de restaurante no especificado']);
}
?>

