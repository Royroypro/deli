<?php

include(__DIR__ . '/../../config.php');

include(__DIR__ . '/../../../layout/sesion.php');
include(__DIR__ . '../../../../admin/layout/parte1.php');
// Verificar si los datos del carrito fueron enviados
if (!isset($_POST['carrito'])) {
    echo '<script>window.location.href="' . $URL . '/index.php"</script>';
    exit;
}

// Decodificar los datos del carrito recibidos
$carrito = json_decode($_POST['carrito'], true);

// Verificar que los datos del carrito sean válidos
if (!$carrito || !isset($carrito['total']) || !isset($carrito['productos'])) {
    echo "Carrito inválido.";
    exit;
}


// Clasificar los productos por restaurante
$productosPorRestaurante = [];
foreach ($carrito['productos'] as $producto) {
    $restaurante = $producto['restaurante'] ?? 'Desconocido';
    if (!isset($productosPorRestaurante[$restaurante])) {
        $productosPorRestaurante[$restaurante] = [];
    }
    $productosPorRestaurante[$restaurante][] = $producto;
}



$consulta = $pdo->prepare("SELECT nombre_cliente FROM clientes WHERE id_usuario = :id_usuario_sesion");
$consulta->execute(['id_usuario_sesion' => $id_usuario_sesion]);
$resultado = $consulta->fetch();


if ($resultado) {
    $nombre_cliente_sesion = $resultado['nombre_cliente'];
} else {
    $nombre_cliente_sesion = 'Desconocido';
}




$consulta = $pdo->prepare("SELECT nombre FROM repartidores WHERE estado = 'activo' ORDER BY RAND() LIMIT 1");
$consulta->execute();
$resultado = $consulta->fetch();
if ($resultado) {
    $repartidor = $resultado['nombre'];
} else {
    $repartidor = 'No hay repartidores disponibles';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente</title>
    <style>
        #carrito {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #carrito > li {
            margin-bottom: 1em;
        }

        #carrito > li > strong {
            font-weight: bold;
        }

        #carrito > li > ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #carrito > li > ul > li {
            padding: 0.5em;
        }

        #carrito > li > label {
            display: block;
            margin-bottom: 0.5em;
        }

        #carrito > li > select {
            width: 100%;
            padding: 0.5em;
            margin-bottom: 1em;
        }

        #carrito > li > textarea {
            width: 100%;
            height: 100px;
            padding: 0.5em;
            margin-bottom: 1em;
        }

        #carrito > li > button {
            background-color: #4CAF50;
            color: white;
            padding: 0.5em 1em;
            border: none;
            cursor: pointer;
            margin-top: 1em;
        }

        #carrito > li > button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    
    <h2>Carrito de Compras</h2>
    <ul id="carrito">
        <?php foreach ($productosPorRestaurante as $restaurante => $productos): ?>
            <li>
                <strong><?php echo htmlspecialchars($restaurante); ?></strong>
                <ul>
                    <?php foreach ($productos as $producto): ?>
                        <li>
                            <?php echo htmlspecialchars($producto['nombre']); ?> x <?php echo htmlspecialchars($producto['cantidad']); ?> - S/ <?php echo number_format($producto['precio'], 2); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <label for="metodoPago_<?php echo htmlspecialchars($restaurante); ?>">Método de Pago:</label>
                <select id="metodoPago_<?php echo htmlspecialchars($restaurante); ?>" class="metodoPago">
                    <option value="efectivo">Efectivo</option>
                    <option value="yape">Yape</option>
                    <option value="transferencia">Transferencia</option>
                </select>
                <label for="detalles_<?php echo htmlspecialchars($restaurante); ?>">Detalles:</label>
                <textarea id="detalles_<?php echo htmlspecialchars($restaurante); ?>" cols="20" rows="1" style="height: 30px;" class="detalles"></textarea>
                <button data-restaurante="<?php echo htmlspecialchars($restaurante); ?>" class="enviarPedidoRestaurante">Enviar Pedido a <?php echo htmlspecialchars($restaurante); ?></button>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Pedidos</h3>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
        }
        th {
            background-color: #FFCC00;
            color: #333;
        }
        .dropbtn {
            background-color: #FF6600;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
            z-index: 1;
            border-radius: 4px;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
<table id="pedidosTable">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Restaurante</th>
                <th>Repartidor</th>
                <th>Productos</th>
                <th>Total</th>
                <th>Mt Pago</th>
                <th>Detalles</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los pedidos se agregarán aquí -->
        </tbody>
    </table>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const carrito = {
        total: <?php echo json_encode($carrito['total']); ?>,
        productos: <?php echo json_encode($carrito['productos']); ?>,
        repartidor: "<?php echo $repartidor; ?>",
        nombre_cliente: "<?php echo htmlspecialchars($nombre_cliente_sesion); ?>",
    };

    const socket = new WebSocket('ws://localhost:8080/deli');

    socket.onopen = () => {
        console.log("Conectado al servidor WebSocket");
        socket.send(JSON.stringify({ role: "cliente" }));
    };

    socket.onerror = (error) => {
        console.error("Error en WebSocket:", error);
        alert("No se pudo conectar al servidor.");
    };

    socket.onclose = () => {
        console.log("Conexión cerrada con el servidor WebSocket");
    };

    // Recibimos el mensaje con el estado actualizado del pedido
    socket.onmessage = (event) => {
        console.log("Mensaje recibido del servidor:", event.data); // Log para verificar
        const data = JSON.parse(event.data);

        if (data.pedido_id) {
            // Verifica si el mensaje contiene información del pedido
            actualizarEstadoPedido(data);
        }
    };
// Enviar pedido al restaurante
document.querySelectorAll('.enviarPedidoRestaurante').forEach(button => {
    button.addEventListener('click', () => {
        const restaurante = button.getAttribute('data-restaurante');
        const productos = carrito.productos.filter(producto => producto.restaurante === restaurante);
        if (productos.length === 0) {
            alert(`No hay productos en el carrito para ${restaurante}.`);
            return;
        }

        const metodoPago = document.getElementById(`metodoPago_${restaurante}`).value;
        const detalles = document.getElementById(`detalles_${restaurante}`).value;
        const total = productos.reduce((total, producto) => total + producto.cantidad * producto.precio, 0);

        const pedido = {
            id_pedido: Math.floor(10000 + Math.random() * 90000),
            cliente: carrito.nombre_cliente,
            restaurante: restaurante,
            repartidor: carrito.repartidor,
            productos: productos,
            total: total,
            metodo_pago: metodoPago,
            detalles: detalles,
            estado: "Pendiente" // El estado inicial del pedido es "Pendiente"
        };

        // Verificar si el pedido ya existe en la tabla
        const filas = document.querySelectorAll('#pedidosTable tbody tr');
        let pedidoDuplicado = false;

        filas.forEach(fila => {
            const celdas = fila.children;
            const clienteTabla = celdas[1].textContent;
            const restauranteTabla = celdas[2].textContent;
            const repartidorTabla = celdas[3].textContent;
            const productosTabla = celdas[4].textContent;
            const totalTabla = parseFloat(celdas[5].textContent.replace('S/ ', ''));
            const metodoPagoTabla = celdas[6].textContent;
            const detallesTabla = celdas[7].textContent;

            if (
                clienteTabla === pedido.cliente &&
                restauranteTabla === pedido.restaurante &&
                repartidorTabla === pedido.repartidor &&
                productosTabla === pedido.productos.map(p => `${p.nombre} (x${p.cantidad})`).join(', ') &&
                totalTabla === pedido.total &&
                metodoPagoTabla === pedido.metodo_pago &&
                detallesTabla === pedido.detalles
            ) {
                pedidoDuplicado = true;
            }
        });

        if (pedidoDuplicado) {
            alert(`El pedido para ${restaurante} ya ha sido enviado anteriormente.`);
            return;
        }

        // Enviar pedido si no es duplicado
        if (socket.readyState === WebSocket.OPEN) {
            socket.send(JSON.stringify({ pedido }));
            console.log("Pedido enviado:", pedido);
            alert(`Pedido enviado a ${restaurante}`);
            agregarPedidoATabla(pedido);
        } else {
            alert("Error: No se pudo conectar al servidor.");
        }
    });
});


    const agregarPedidoATabla = (pedido) => {
        const tbody = document.getElementById('pedidosTable').querySelector('tbody');
        const row = document.createElement('tr');
        row.id = `pedido-${pedido.id_pedido}`; // Aseguramos que la fila tenga el id correcto
        row.innerHTML = `
            <td>${pedido.id_pedido}</td>
            <td>${pedido.cliente}</td>
            <td>${pedido.restaurante}</td>
            <td>${pedido.repartidor}</td>
            <td>${pedido.productos.map(p => `${p.nombre} (x${p.cantidad})`).join(', ')}</td>
            <td>S/ ${pedido.total.toFixed(2)}</td>
            <td>${pedido.metodo_pago}</td>
            <td>${pedido.detalles}</td>
            <td id="estado-${pedido.id_pedido}">${pedido.estado}</td>
        `;
        tbody.appendChild(row);
    };

    const actualizarEstadoPedido = (data) => {
        const pedidoId = data.pedido_id;
        const estado = data.estado;
        const mensaje = data.mensaje;

        // Busca el pedido en la tabla usando el ID
        const filaPedido = document.querySelector(`#pedido-${pedidoId}`);
        if (filaPedido) {
            const estadoCell = filaPedido.querySelector(`#estado-${pedidoId}`); // Celda que contiene el estado

            // Actualiza el estado en la interfaz
            estadoCell.textContent = estado === 'aceptar' ? 'Aceptado' : 'Rechazado';

            // Mostrar notificación
            mostrarNotificacion(mensaje);
        }
    };

    // Función para mostrar notificación
    const mostrarNotificacion = (mensaje) => {
        if (Notification.permission === "granted") {
            const notification = new Notification('Actualización de Pedido', {
                body: mensaje,
                icon: 'path/to/icon.png' // Icono opcional
            });
        } else {
            console.log("Permiso para notificaciones no concedido.");
        }
    };
});
</script>

