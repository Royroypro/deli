<?php
include '../app/config.php';
// Procesar la actualización de la cantidad
header('Content-Type: application/json');

// Obtener los datos enviados por POST
$id_detalle = isset($_POST['id_detalle']) ? intval($_POST['id_detalle']) : 0;
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

// Verificar que los datos sean válidos
if ($id_detalle > 0 && $id_pedido > 0) {
    // Conexión a la base de datos y lógica para actualizar la cantidad
    try {
        if ($cantidad > 0) {
            // Consulta SQL para actualizar la cantidad del producto
            $stmt = $pdo->prepare("UPDATE `detalle_pedido` SET `cantidad` = :cantidad WHERE `id_detalle` = :id_detalle");
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':id_detalle', $id_detalle, PDO::PARAM_INT);

            // Ejecutar la consulta de actualización
            if ($stmt->execute()) {
                // Obtener los detalles actualizados del pedido
                $stmt = $pdo->prepare("SELECT * FROM `detalle_pedido` WHERE `id_pedido` = :id_pedido");
                $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
                $stmt->execute();

                // Obtener todos los detalles
                $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Devolver los detalles actualizados junto con el estado de la actualización
                echo json_encode(['status' => true, 'message' => 'Cantidad actualizada correctamente.', 'detalles' => $detalles]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al actualizar la cantidad.']);
            }
        } else {
            // Borrar el detalle del pedido
            $stmt = $pdo->prepare("DELETE FROM `detalle_pedido` WHERE `id_detalle` = :id_detalle");
            $stmt->bindParam(':id_detalle', $id_detalle, PDO::PARAM_INT);

            // Ejecutar la consulta de eliminación
            if ($stmt->execute()) {
                // Obtener los detalles actualizados del pedido
                $stmt = $pdo->prepare("SELECT * FROM `detalle_pedido` WHERE `id_pedido` = :id_pedido");
                $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
                $stmt->execute();

                // Obtener todos los detalles
                $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Devolver los detalles actualizados junto con el estado de la actualización
                echo json_encode(['status' => true, 'message' => 'Detalle eliminado correctamente.', 'detalles' => $detalles]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al eliminar el detalle.']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Datos inválidos.']);
}
?>
