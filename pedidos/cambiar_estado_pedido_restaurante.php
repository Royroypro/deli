<?php
include "../app/config.php";
header('Content-Type: application/json');
$id_pedido = $_GET['id_pedido'] ?? null;
$estado = $_GET['estado'] ?? null;

if ($id_pedido && $estado) {
    if (!in_array($estado, ['pendiente', 'aceptado', 'preparacion', 'enviado', 'entregado', 'cancelar', 'tomado', 'notomado'])) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'El estado no es v lido']);

        exit;
    }

    try {
        $consulta = $pdo->prepare("UPDATE pedidos SET estado = :estado WHERE id_pedido = :id_pedido");
        $consulta->execute([
            'estado' => $estado,
            'id_pedido' => $id_pedido
        ]);

        if ($consulta->rowCount() > 0) {
            echo json_encode(['estado' => 'success']);
        } else {
            echo json_encode(['estado' => 'error', 'mensaje' => 'No se encontr  el pedido con ese id']);
        }
    } catch (PDOException $e) {
        if (stripos($e->getMessage(), 'Data truncated for column')) {
            echo json_encode(['estado' => 'error', 'mensaje' => 'El estado debe ser una palabra de 50 caracteres como m ximo']);
        } else {
            echo json_encode(['estado' => 'error', 'mensaje' => 'Error al cambiar el estado del pedido: ' . strip_tags($e->getMessage())]);
        }
    }
} else {
    echo json_encode(['estado' => 'error', 'mensaje' => 'No se encontraron los par metros id_pedido y estado']);
}


