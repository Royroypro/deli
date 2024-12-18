module.exports = (socket, connection) => {
    let lastPedidosState = []; // Almacenar el estado anterior de los pedidos
    let isPollingActive = false; // Bandera para evitar múltiples intervalos

    // Obtener detalles del pedido
    socket.on('getDetallesPedido', (idPedido) => {
        const currentTime = new Date().toLocaleString(); // Obtener la hora y fecha actual
        console.log(`[${currentTime}] Recibiendo solicitud de detalles para el pedido ID: ${idPedido}`);

        const sql = `
            SELECT dp.id_detalle, dp.id_producto, dp.cantidad, dp.subtotal, p.nombre AS nombre_producto
            FROM detalle_pedido dp
            INNER JOIN productos p ON dp.id_producto = p.id_producto
            WHERE dp.id_pedido = ?;
        `;
        connection.query(sql, [idPedido], (err, results) => {
            if (err) {
                console.error(`[${currentTime}] Error en la consulta SQL:`, err);
                socket.emit('errorDetallesPedido', { message: 'Error al obtener detalles del pedido' });
                return;
            }
            console.log(`[${currentTime}] Detalles del pedido obtenidos:`, results);
            socket.emit('detallesPedido', results);
        });
    });


    // Obtener productos por restaurante
    socket.on('getProductosPorRestaurante', (idRestaurante) => {
        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Recibiendo solicitud de productos para el restaurante ID ${idRestaurante}`);

        const sql = `
            SELECT p.id_producto, p.nombre AS nombre_producto, p.precio, p.descripcion, p.imagen
            FROM productos p
            WHERE p.id_restaurante = ?;
        `;
        connection.query(sql, [idRestaurante], (err, productos) => {
            if (err) {
                console.error(`[${currentTime}] Error en la consulta SQL:`, err);
                socket.emit('errorProductos', { message: 'Error al obtener productos para el restaurante' });
                return;
            }
            console.log(`[${currentTime}] Productos obtenidos para el restaurante ID ${idRestaurante}:`, productos);
            socket.emit('productos', productos);
        });
    });



    // Obtener el id_restaurante desde la solicitud
    socket.on('setRestauranteId', (idRestaurante) => {
        socket.idRestaurante = idRestaurante;

        // Iniciar el polling solo si no está activo
        if (!isPollingActive) {
            isPollingActive = true; // Marcar que el polling está activo
            console.log(`Polling iniciado para el restaurante ID: ${idRestaurante}`);

            iniciarPolling(socket, connection, idRestaurante);
        }
    });

    // Detener el polling cuando el cliente se desconecta
    socket.on('disconnect', () => {
        console.log(`Cliente desconectado. Deteniendo polling.`);
        isPollingActive = false; // Marcar el polling como inactivo
    });
};

function iniciarPolling(socket, connection, idRestaurante) {
    let lastPedidosState = []; // Estado previo de los pedidos

    const interval = setInterval(() => {
        if (!socket.connected) {
            clearInterval(interval);
            console.log(`Polling detenido para el restaurante ID: ${idRestaurante}`);
            return;
        }

        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Polling de pedidos para el restaurante ID ${idRestaurante}`);

        const sql = `
            SELECT p.id_pedido, r.nombre as restaurante, c.nombre_cliente as cliente, p.estado, p.total, p.fecha, re.nombre as repartidor, re.telefono as telefono_repartidor
            FROM pedidos p
            LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            LEFT JOIN repartidores re ON p.id_repartidor = re.id_repartidor
            WHERE p.id_restaurante = ?;
        `;

        connection.query(sql, [idRestaurante], (err, pedidos) => {
            if (err) {
                console.error(`[${currentTime}] Error en la consulta SQL:`, err);
                return;
            }

            if (JSON.stringify(pedidos) !== JSON.stringify(lastPedidosState)) {
                lastPedidosState = pedidos; // Actualizar el estado anterior
                console.log(`[${currentTime}] Pedidos actualizados. Emitiendo a cliente.`);

                // Obtener los productos de cada pedido
                let productosPendientes = pedidos.length;

                if (productosPendientes === 0) {
                    socket.emit('pedidosRestaurante', pedidos);
                } else {
                    pedidos.forEach((pedido, index) => {
                        const sqlProductos = `
                            SELECT dp.cantidad, p.nombre, p.precio
                            FROM detalle_pedido dp
                            INNER JOIN productos p ON dp.id_producto = p.id_producto
                            WHERE dp.id_pedido = ?;
                        `;
                        connection.query(sqlProductos, [pedido.id_pedido], (err, productos) => {
                            if (err) {
                                console.error(`[${currentTime}] Error al obtener productos del pedido ID ${pedido.id_pedido}:`, err);
                            } else {
                                let productosStr = "";
                                productos.forEach((producto) => {
                                    productosStr += `${producto.nombre} x ${producto.cantidad} S/ ${producto.precio}<br>`;
                                });
                                pedidos[index].productos = productosStr;
                            }

                            productosPendientes--;
                            if (productosPendientes === 0) {
                                socket.emit('pedidosRestaurante', pedidos);
                            }
                        });
                    });
                }
            } else {
                console.log(`[${currentTime}] No hay cambios en los pedidos.`);
            }
        });
    }, 5000); // Polling cada 5 segundos
}
