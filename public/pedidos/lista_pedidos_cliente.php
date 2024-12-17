<?php
include('../../app/config.php');
include('../../layout/sesion.php');
include "../../admin/layout/parte1.php";
?>
<?php include "../../layout/parte1.php"; ?>
<style>
    body {
        margin-top: 70px;
    }
</style>

<body>

    <!-- pageContent -->


    <section class="full-width header-well">
        <div class="full-width header-well-icon">
            <i class="zmdi zmdi-shopping-cart"></i>
        </div>
        <div class="full-width header-well-text">
            <p class="text-condensedLight">
                <span class="text-dark">Pedidos</span>
            </p>
        </div>
    </section>
    <div class="full-width divider-menu-h"></div>
    <div class="mdl-grid">

        <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">

            <div class="table-responsive">

                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th style="text-align: center;">Date</th>
                            <th style="text-align: center;">Restaurante</th>
                            <th style="text-align: center;">Cliente</th>
                            <th style="text-align: center;">Productos</th>
                            <th style="text-align: center;">Estado</th>
                            <th style="text-align: center;">Total</th>
                            <th style="text-align: center;">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1; // Inicializar el contador
                        // Seleccionar todos los pedidos asociados al cliente de sesión
                        $sql = "SELECT p.id_pedido, r.nombre as restaurante, c.nombre_cliente as cliente, p.estado, p.total, p.fecha 
                        FROM pedidos p 
                        LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante 
                        LEFT JOIN clientes c ON p.id_cliente = c.id_cliente 
                        LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
                        WHERE u.id_usuario = :id_usuario";

                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id_usuario', $id_usuario_sesion);
                        $stmt->execute();
                        $pedidos = $stmt->fetchAll();

                        foreach ($pedidos as $pedido) {
                            echo "<tr>
            <td>" . $counter++ . "</td> <!-- Mostrar y aumentar el contador -->
            <td>" . date('d/m/Y H:i:s', strtotime($pedido['fecha'])) . "</td>
            <td>" . htmlspecialchars($pedido['restaurante']) . "</td>
            <td>" . htmlspecialchars($pedido['cliente']) . "</td>";
                            // Obtener los productos del pedido
                            $consulta = $pdo->prepare("
            SELECT dp.cantidad, p.nombre, p.precio 
            FROM detalle_pedido dp 
            INNER JOIN productos p ON dp.id_producto = p.id_producto 
            WHERE dp.id_pedido = :id_pedido");
                            $consulta->bindParam(':id_pedido', $pedido['id_pedido']);
                            $consulta->execute();
                            $productos = $consulta->fetchAll();

                            // Concatenar productos con cantidades
                            $productosStr = "";
                            foreach ($productos as $producto) {
                                $productosStr .= htmlspecialchars($producto['nombre']) . " x " . intval($producto['cantidad']) . " S/ " . number_format($producto['precio'], 2) . "<br>";
                            }

                            echo "<td style='text-align:center;'>" . $productosStr . "</td>";
                            echo "<td style='text-align:center; color: " . ($pedido['estado'] == 'pendiente' ? 'orange' : ($pedido['estado'] == 'preparacion' ? 'green' : '')) . ";'>" . htmlspecialchars($pedido['estado']) . "</td>
          <td style='text-align:center;'>". "S/" . number_format($pedido['total'], 2) . "</td>
          <td style='text-align:center;'><a href='mas_detalles_cliente.php?id_pedido=" . $pedido['id_pedido'] . "'>Más detalles</a></td>
        </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>