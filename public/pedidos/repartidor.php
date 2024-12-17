<?php


include_once '../app/config.php';
include_once '../layout/sesion.php';
include_once '../admin/layout/parte1.php';
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

    #pedidosTable tr:hover {
        background-color: #ffe0cc; /* Light orange */
    }
</style>
<div class="mdl-tabs__panel" id="tabListProducts">
    <h1>Repartidor: <?php echo $nombres_sesion; ?></h1>
    <table id="pedidosTable">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Productos</th>
                <th>Estado del restaurante</th>
                <th>Detalles</th>
                <th>Total</th>
                <th>Mt Pago</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los pedidos se agregarán aquí -->
        </tbody>
    </table>
    <script>
    const socket = new WebSocket('ws://localhost:8080/deli');

    socket.onopen = () => {
        console.log("Conectado al servidor WebSocket");
        // Registrar como repartidor
        socket.send(JSON.stringify({ role: "repartidor" }));
    };

    socket.onmessage = (event) => {
        const data = JSON.parse(event.data);
        console.log("Datos recibidos:", data);

        if (data.origen === 'restaurante' && data.mensaje === 'Nuevo pedido para reparto' && data.pedido_id) {
            const pedidoId = data.pedido_id;
            const estado = data.estado;
            const pedido = data.pedido;

            if (pedido) {
                console.log("Pedido recibido con ID:", pedidoId);

                // Crear una nueva fila con los datos del pedido
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${pedidoId}</td>
                    <td>${pedido.cliente}</td>
                    <td>${pedido.productos
                        .map(producto => `${producto.nombre} (x${producto.cantidad})`)
                        .join('<br>')}
                    </td>
                    <td>${estado}</td>
                    <td>${pedido.detalles}</td>
                    <td>S/ ${pedido.total.toFixed(2)}</td>
                    <td>${pedido.metodo_pago}</td>
                `;

                // Agregar la fila al cuerpo de la tabla
                document.getElementById('pedidosTable').querySelector('tbody').appendChild(row);
            } else {
                console.log("El pedido recibido no contiene datos válidos.");
            }
        } else {
            console.log("No se recibió un pedido válido o no pertenece al repartidor.");
        }
    };

    socket.onclose = () => {
        console.log("Conexión con el servidor WebSocket cerrada.");
    };

    socket.onerror = (error) => {
        console.error("Error en WebSocket:", error);
    };
</script>





