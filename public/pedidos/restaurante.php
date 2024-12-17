<?php

include_once '../app/config.php';
include_once '../layout/sesion.php';
include_once '../admin/layout/parte1.php';

$consulta = $pdo->prepare("SELECT nombre FROM restaurantes WHERE id_restaurante = :id_restaurante_sesion");
$consulta->execute(['id_restaurante_sesion' => $id_restaurante_sesion]);
$resultado = $consulta->fetch();
$nombre_restaurante_sesion = $resultado['nombre'] ?? null;


?>

<style>
    #pedidosTable {
        border-collapse: collapse;
        width: 100%;
    }

    #pedidosTable td, #pedidosTable th {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        font-size: 14px;
    }

    #pedidosTable tr:nth-child(even) {
        background-color: #ffd7be; /* Naranja calido */
    }

    #pedidosTable th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: center;
        background-color: #ffb347; /* Naranja mas claro */
        color: white;
    }

    #pedidosTable td:nth-child(3) {
        font-weight: bold;
    }

    #pedidosTable td:nth-child(5) {
        font-weight: bold;
    }

    #pedidosTable tr:hover {
        background-color: #ffe0cc; /* Light orange */
    }
</style>
<div class="mdl-tabs__panel" id="tabListProducts">
    <h2><?php echo ucfirst($rol_sesion); ?>: <?php echo $nombre_restaurante_sesion; ?></h2>
    <table border="1" id="pedidosTable">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Restaurante</th>
                <th>Cliente</th>
                <th>Productos</th>
                <th>Detalles</th>
                <th>Total</th>
                <th>Mt Pago</th>
                <th>Acción</th> <!-- Nueva columna para las acciones -->
            </tr>
        </thead>
        <tbody>
            <!-- Los pedidos se agregarán aquí -->
        </tbody>
    </table>
</div>
<script>
    const socket = new WebSocket('ws://localhost:8080');
    socket.onopen = () => {
        console.log("Conectado al servidor WebSocket");
        // Registrar como restaurante
        socket.send(JSON.stringify({ role: "restaurante", nombre: "<?= $nombre_restaurante_sesion ?>" }));
    };

    socket.onmessage = (event) => {
        const { pedido } = JSON.parse(event.data);
        console.log("Pedido recibido:", pedido);

        if (pedido.restaurante === "<?= $nombre_restaurante_sesion ?>") {
            // Crear una nueva fila para el pedido
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${pedido.id_pedido}</td>
                <td>${pedido.restaurante}</td>
                <td>${pedido.cliente}</td>
                <td>${pedido.productos.map(p => `${p.nombre} (x${p.cantidad})`).join(', ')}</td>
                <td>${pedido.detalles}</td>
                <td>S/ ${pedido.total.toFixed(2)}</td>
                <td>${pedido.metodo_pago}</td>
                
                
                <td>
                    <button onclick="aceptarPedido(${pedido.id_pedido})">Aceptar</button>
                    <button onclick="rechazarPedido(${pedido.id_pedido})">Rechazar</button>
                </td>
            `;
            document.getElementById('pedidosTable').querySelector('tbody').appendChild(row);
        }
    };

    // Función para manejar la acción de aceptar
    function aceptarPedido(idPedido) {
        const mensaje = {
            accion: 'respuesta_pedido',
            pedido_id: idPedido,
            respuesta: 'aceptar'
        };
        socket.send(JSON.stringify(mensaje));
        alert(`Pedido ${idPedido} aceptado.`);
    }

    // Función para manejar la acción de rechazar
    function rechazarPedido(idPedido) {
        const mensaje = {
            accion: 'respuesta_pedido',
            pedido_id: idPedido,
            respuesta: 'rechazar'
        };
        socket.send(JSON.stringify(mensaje));
        alert(`Pedido ${idPedido} rechazado.`);
    }
</script>




