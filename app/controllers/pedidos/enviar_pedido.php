<?php
include(__DIR__ . '/../../config.php');

include(__DIR__ . '/../../../layout/sesion.php');


$consulta = $pdo->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = :id_usuario_sesion");
$consulta->execute(['id_usuario_sesion' => $id_usuario_sesion]);
$resultado = $consulta->fetch();
if ($resultado) {
    $id_cliente_sesion = $resultado['id_cliente'];
} else {
    $id_cliente_sesion = null;
}

$rapartidor = null;






if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['productos']) && is_array($_POST['productos'])) {
        $pdo->beginTransaction();
        try {
            foreach ($_POST['productos'] as $restaurante => $productos) {
                $consultaRestaurante = $pdo->prepare("SELECT id_restaurante FROM restaurantes WHERE nombre = :nombre LIMIT 1");
                $consultaRestaurante->execute([
                    'nombre' => $restaurante,
                ]);
                $resultadoRestaurante = $consultaRestaurante->fetch();
                $id_restaurante = $resultadoRestaurante['id_restaurante'] ?? null;
                if ($id_restaurante === null) {
                    continue;
                }

                $total = array_reduce($productos, function ($carry, $producto) {
                    return $carry + ($producto['precio'] * $producto['cantidad']);
                }, 0);

                $consultaPedido = $pdo->prepare("INSERT INTO pedidos (id_cliente, id_restaurante, total, estado, id_repartidor) VALUES (:id_cliente, :id_restaurante, :total, 'pendiente', :id_repartidor)");
                $consultaPedido->execute([
                    'id_cliente' => $id_cliente_sesion,
                    'id_restaurante' => $id_restaurante,
                    'total' => $total,
                    'id_repartidor' => $rapartidor,
                ]);
                $id_pedido = $pdo->lastInsertId();
                foreach ($productos as $producto) {
                    $consultaProducto = $pdo->prepare("SELECT id_producto FROM productos WHERE nombre = :nombre AND id_restaurante = :id_restaurante LIMIT 1");
                    $consultaProducto->execute([
                        'nombre' => $producto['nombre'],
                        'id_restaurante' => $id_restaurante,
                    ]);
                    $resultadoProducto = $consultaProducto->fetch();
                    $id_producto = $resultadoProducto['id_producto'] ?? null;

                    if ($id_producto !== null) {
                        $consultaDetalle = $pdo->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, subtotal) VALUES (:id_pedido, :id_producto, :cantidad, :subtotal)");
                        $consultaDetalle->execute([
                            'id_pedido' => $id_pedido,
                            'id_producto' => $id_producto,
                            'cantidad' => $producto['cantidad'],
                            'subtotal' => $producto['precio'] * $producto['cantidad'],
                        ]);
                    }
                }
            }

            echo "Pedido enviado con éxito";
            echo '<script>
    setTimeout(function() {
        window.location.href = "../../../pedidos/lista_pedidos_cliente.php";
    }, 2000);
</script>';

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Failed: " . $e->getMessage();
        }
    }
}


/* CREATE TABLE `detalle_pedido` (
    `id_detalle` int NOT NULL,
    `id_pedido` int NOT NULL,
    `id_producto` int NOT NULL,
    `cantidad` int NOT NULL,
    `subtotal` decimal(10,2) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
   */
/* CREATE TABLE `pedidos` (
    `id_pedido` int NOT NULL,
    `id_cliente` int NOT NULL,
    `id_restaurante` int NOT NULL,
    `id_repartidor` int DEFAULT NULL,
    `estado` enum('pendiente','preparacion','enviado','entregado') DEFAULT 'pendiente',
    `total` decimal(10,2) NOT NULL,
    `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
 */

/*  CREATE TABLE `clientes` (
    `id_cliente` int NOT NULL,
    `id_usuario` int NOT NULL,
    `nombre_cliente` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `direccion` varchar(255) DEFAULT NULL,
    `telefono` varchar(15) DEFAULT NULL,
    `puntos_fidelidad` int DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
  -- */
