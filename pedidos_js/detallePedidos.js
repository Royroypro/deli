module.exports = (socket, connection, io) => {
    socket.on('connection', () => {
        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Nuevo cliente conectado`);

        // Emitir los pedidos actuales para el cliente cuando se conecta
        if (socket.idCliente) {
            console.log(`[${currentTime}] Emitiendo pedidos actuales para el cliente ID ${socket.idCliente}`);
            obtenerPedidosCliente(socket, connection, socket.idCliente, io); // Emitir pedidos cuando se conecta el cliente
        } else {
            console.warn(`[${currentTime}] No se ha configurado un ID de cliente para el cliente conectado.`);
        }

        // Empezar a hacer polling para obtener pedidos actualizados
        iniciarPollingPedidos(socket, connection, io);
    });

    socket.on('setClienteId', (idCliente) => {
        socket.idCliente = idCliente;

        console.log(`[${new Date().toLocaleString()}] Cliente ID ${idCliente} configurado para el cliente`);

        // Emitir los pedidos al cliente al configurar el cliente
        obtenerPedidosCliente(socket, connection, idCliente, io);

        // Iniciar el polling para ese cliente
        iniciarPollingPedidos(socket, connection, io);
    });

    socket.on('actualizarEstadoPedido', (idPedido, nuevoEstado) => {
        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Actualizando estado del pedido ID ${idPedido} a "${nuevoEstado}"`);

        actualizarEstadoPedido(idPedido, nuevoEstado, socket, connection, io);
    });

    socket.on('disconnect', () => {
        console.log(`[${new Date().toLocaleString()}] Cliente desconectado.`);
    });
};

// Función para obtener los pedidos y emitirlos al cliente
function obtenerPedidosCliente(socket, connection, idCliente, io) {
    const currentTime = new Date().toLocaleString();
    console.log(`[${currentTime}] Consultando pedidos para el cliente ID ${idCliente}`);

    const sql = `
        SELECT p.id_pedido, r.nombre AS restaurante, c.nombre_cliente AS cliente, 
               p.estado, p.total, p.fecha, re.nombre AS repartidor, re.telefono AS telefono_repartidor
        FROM pedidos p
        LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante
        LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
        LEFT JOIN repartidores re ON p.id_repartidor = re.id_repartidor
        WHERE p.id_cliente = ?;
    `;

    connection.query(sql, [idCliente], (err, pedidos) => {
        if (err) {
            console.error(`[${currentTime}] Error al obtener pedidos:`, err);
            return;
        }

        console.log(`[${currentTime}] Pedidos obtenidos para el cliente ID ${idCliente}:`, pedidos);

        obtenerProductosPedidos(pedidos, connection, (pedidosConProductos) => {
            // Emitir los pedidos al cliente
            socket.emit('pedidosCliente', pedidosConProductos);
        });
    });
}

// Función para obtener los productos asociados a los pedidos
function obtenerProductosPedidos(pedidos, connection, callback) {
    let productosPendientes = pedidos.length;

    if (productosPendientes === 0) {
        callback(pedidos);
        return;
    }

    pedidos.forEach((pedido, index) => {
        const sqlProductos = `
            SELECT dp.cantidad, p.nombre, p.precio
            FROM detalle_pedido dp
            INNER JOIN productos p ON dp.id_producto = p.id_producto
            WHERE dp.id_pedido = ?;
        `;

        connection.query(sqlProductos, [pedido.id_pedido], (err, productos) => {
            if (err) {
                console.error(`Error al obtener productos del pedido ID ${pedido.id_pedido}:`, err);
                productosPendientes--;
                return;
            }

            let productosStr = productos.map(producto => `${producto.nombre} x ${producto.cantidad} S/ ${producto.precio}`).join('<br>');
            pedidos[index].productos = productosStr;

            productosPendientes--;

            if (productosPendientes === 0) {
                callback(pedidos);
            }
        });
    });
}

// Función para actualizar el estado del pedido
function actualizarEstadoPedido(idPedido, nuevoEstado, socket, connection, io) {
    const currentTime = new Date().toLocaleString();
    const sql = `UPDATE pedidos SET estado = ? WHERE id_pedido = ?`;

    connection.query(sql, [nuevoEstado, idPedido], (err, resultado) => {
        if (err) {
            console.error(`[${currentTime}] Error al actualizar estado del pedido:`, err);
            return;
        }

        console.log(`[${currentTime}] Estado del pedido actualizado con éxito`);

        // Emitir la actualización del pedido al cliente y restaurantes conectados
        obtenerPedidosCliente(socket, connection, socket.idCliente, io);  // Actualizar los pedidos para el cliente que hizo la acción
        io.emit('pedidoActualizado', { id_pedido, nuevoEstado });  // Emitir la actualización a todos los clientes conectados
    });
}

// Función para iniciar el polling de pedidos cada cierto intervalo
function iniciarPollingPedidos(socket, connection, io) {
    const intervalo = 10000;  // Intervalo de 10 segundos (10000 milisegundos)

    setInterval(() => {
        if (socket.idCliente) {
            console.log(`[${new Date().toLocaleString()}] Realizando consulta periódica para el cliente ID ${socket.idCliente}`);
            obtenerPedidosCliente(socket, connection, socket.idCliente, io);
        }
    }, intervalo);
}

