module.exports = (socket, connection, io) => {
    let idCliente = null;
    let idRestaurante = null;

    socket.on('connection', () => {
        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Nuevo cliente conectado`);

        if (idCliente) {
            console.log(`[${currentTime}] Emitiendo pedidos actuales para el cliente ID ${idCliente}`);
            obtenerPedidos(socket, connection, idCliente, io); // Emitir pedidos cuando se conecta el cliente

            // Establecer intervalos para actualizar los pedidos de este cliente
            const actualizarPedidosInterval = setInterval(() => {
                obtenerPedidos(socket, connection, idCliente, io); // Emitir los pedidos actualizados
            }, 3000); // 5 segundos de intervalo para actualizar

            // Limpiar el intervalo cuando el cliente se desconecta
            socket.on('disconnect', () => {
                clearInterval(actualizarPedidosInterval); // Detener la actualización periódica cuando se desconecta
                console.log(`[${new Date().toLocaleString()}] Cliente desconectado`);
            });
        } else {
            console.warn(`[${currentTime}] No se ha configurado un ID de cliente para el cliente conectado.`);
        }
    });

    socket.on('setClienteId', (id) => {
        idCliente = id;

        console.log(`[${new Date().toLocaleString()}] Cliente ID ${idCliente} configurado para el cliente`);

        // Emitir los pedidos al cliente al configurar el cliente
        obtenerPedidos(socket, connection, idCliente, io);
    });

    socket.on('setRestauranteId', (id) => {
        idRestaurante = id;

        console.log(`[${new Date().toLocaleString()}] Restaurante ID ${idRestaurante} conectado`);

        // Emitir los pedidos para el restaurante al conectarse
        obtenerPedidosRestaurante(socket, connection, idRestaurante, io);
    });

    socket.on('actualizarEstadoPedido', (idPedido, nuevoEstado) => {
        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Actualizando estado del pedido ID ${idPedido} a "${nuevoEstado}"`);

        actualizarEstadoPedido(idPedido, nuevoEstado, socket, connection, io);
    });

    socket.on('disconnect', () => {
        console.log(`[${new Date().toLocaleString()}] Cliente o restaurante desconectado.`);
    });
};

// Función para obtener los pedidos y emitirlos al cliente
function obtenerPedidos(socket, connection, idCliente, io) {
    const currentTime = new Date().toLocaleString();
    console.log(`[${currentTime}] Consultando pedidos para el cliente ID ${idCliente}`);

    const sql = `
        SELECT p.id_pedido, r.nombre as restaurante, c.nombre_cliente as cliente, 
               p.estado, p.total, p.fecha, re.nombre as repartidor, re.telefono as telefono_repartidor
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
            io.to(socket.id).emit('pedidosCliente', pedidosConProductos);
        });
    });
}

// Función para obtener los pedidos para los restaurantes
function obtenerPedidosRestaurante(socket, connection, idRestaurante, io) {
    const currentTime = new Date().toLocaleString();
    console.log(`[${currentTime}] Consultando pedidos para el restaurante ID ${idRestaurante}`);

    const sql = `
        SELECT p.id_pedido, r.nombre as restaurante, c.nombre_cliente as cliente, 
               p.estado, p.total, p.fecha, re.nombre as repartidor, re.telefono as telefono_repartidor
        FROM pedidos p
        LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante
        LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
        LEFT JOIN repartidores re ON p.id_repartidor = re.id_repartidor
        WHERE p.id_restaurante = ?;
    `;

    connection.query(sql, [idRestaurante], (err, pedidos) => {
        if (err) {
            console.error(`[${currentTime}] Error al obtener pedidos para el restaurante:`, err);
            return;
        }

        console.log(`[${currentTime}] Pedidos obtenidos para el restaurante ID ${idRestaurante}:`, pedidos);

        obtenerProductosPedidos(pedidos, connection, (pedidosConProductos) => {
            // Emitir los pedidos al restaurante conectado
            io.to(socket.id).emit('pedidosRestaurante', pedidosConProductos);
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

// Función para actualizar el estado del pedido y emitir cambios a clientes y restaurantes
function actualizarEstadoPedido(idPedido, nuevoEstado, socket, connection, io) {
    const currentTime = new Date().toLocaleString();
    const sql = `UPDATE pedidos SET estado = ? WHERE id_pedido = ?`;

    connection.query(sql, [nuevoEstado, idPedido], (err, resultado) => {
        if (err) {
            console.error(`[${currentTime}] Error al actualizar estado del pedido:`, err);
            return;
        }

        console.log(`[${currentTime}] Estado del pedido actualizado con éxito`);

        // Emitir la actualización del pedido a todos los clientes conectados
        obtenerPedidos(socket, connection, socket.idCliente, io);  // Actualizar los pedidos para el cliente que hizo la acción
        io.emit('pedidoActualizado', { id_pedido, nuevoEstado });  // Emitir la actualización a todos los clientes conectados
    });
}

