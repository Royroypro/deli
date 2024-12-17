<?php
include '../app/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : null;
    $id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : null;
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : null;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

    if ($id_pedido && $id_producto && $precio !== null) {
        try {
            $query_producto = "SELECT stock FROM productos WHERE id_producto = :id_producto";
            $stmt_producto = $pdo->prepare($query_producto);
            $stmt_producto->execute([':id_producto' => $id_producto]);
            $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);

            if ($producto && $producto['stock'] >= $cantidad) {
                $query_check = "SELECT cantidad, subtotal FROM detalle_pedido WHERE id_pedido = :id_pedido AND id_producto = :id_producto";
                $stmt_check = $pdo->prepare($query_check);
                $stmt_check->execute([':id_pedido' => $id_pedido, ':id_producto' => $id_producto]);

                if ($stmt_check->rowCount() > 0) {
                    echo json_encode(['status' => false, 'message' => 'El producto ya ha sido agregado']);
                } else {
                    $subtotal = $cantidad * $precio;
                    $query_insert = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, subtotal) VALUES (:id_pedido, :id_producto, :cantidad, :subtotal)";
                    $stmt_insert = $pdo->prepare($query_insert);
                    $stmt_insert->execute([
                        ':id_pedido' => $id_pedido,
                        ':id_producto' => $id_producto,
                        ':cantidad' => $cantidad,
                        ':subtotal' => $subtotal
                    ]);

                    echo json_encode(['status' => true, 'message' => 'Producto agregado correctamente']);
                }

                $query_update_stock = "UPDATE productos SET stock = stock - :cantidad WHERE id_producto = :id_producto";
                $stmt_update_stock = $pdo->prepare($query_update_stock);
                $stmt_update_stock->execute([':cantidad' => $cantidad, ':id_producto' => $id_producto]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Stock insuficiente']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'Datos incompletos']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Método no permitido']);
}
?>

