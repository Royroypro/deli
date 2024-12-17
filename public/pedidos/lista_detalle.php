<?php
include '../app/config.php';

$id_pedido = isset($_GET['id_pedido']) ? intval($_GET['id_pedido']) : null;

if ($id_pedido) {
    try {
        // Realizar la consulta SQL para obtener los detalles del pedido junto con el nombre del producto
        $consulta = $pdo->prepare("SELECT dp.id_detalle, dp.id_pedido, dp.id_producto, dp.cantidad, dp.subtotal, p.nombre AS nombre_producto
                                   FROM detalle_pedido dp
                                   JOIN productos p ON dp.id_producto = p.id_producto
                                   WHERE dp.id_pedido = :id_pedido");

        $consulta->execute(['id_pedido' => $id_pedido]);

        $detalles = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if ($detalles) {
            echo json_encode(['status' => true, 'detalles' => $detalles]);
        } else {
            echo json_encode(['status' => false, 'message' => 'No se encontraron detalles para este pedido']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'message' => 'Error en la consulta: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'No se proporcionó el id del pedido']);
}
?>
