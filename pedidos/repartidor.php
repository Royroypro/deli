<?php


include_once '../app/config.php';
include_once '../layout/sesion.php';
include_once '../admin/layout/parte1.php';
?>



    <style>
        h1 {
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #FFA726; /* Color anaranjado amarillento */
            color: #fff;
        }
        tbody tr:hover {
            background-color: #f5f5f5;
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
            const { pedido } = JSON.parse(event.data);
            console.log("Pedido recibido:", pedido);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${pedido.id_pedido}</td>
                <td>${pedido.cliente}</td>
                <td>${pedido.productos.map(p => `${p.nombre} (x${p.cantidad})`).join(', ')}</td>
                <td>S/ ${pedido.total.toFixed(2)}</td>
                <td>${pedido.metodo_pago}</td>
            `;
            document.getElementById('pedidosTable').querySelector('tbody').appendChild(row);
        };
    </script>


    </div>