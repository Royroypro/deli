module.exports = (socket, connection) => {
    let isPollingActive = false; // Bandera para evitar múltiples intervalos

    // Obtener detalles del pedido
    socket.on('getDetallesPedido', (idPedido) => {
        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Solicitud de detalles para el pedido ID: ${idPedido}`);

        const sql = `
            SELECT dp.id_detalle, dp.id_producto, dp.cantidad, dp.subtotal, p.nombre AS nombre_producto
            FROM detalle_pedido dp
            INNER JOIN productos p ON dp.id_producto = p.id_producto
            WHERE dp.id_pedido = ?;
        `;
        connection.query(sql, [idPedido], (err, results) => {
            if (err) {
                console.error(`[${currentTime}] Error al obtener detalles:`, err);
                socket.emit('errorDetallesPedido', { message: 'Error al obtener detalles del pedido' });
                return;
            }
            console.log(`[${currentTime}] Detalles del pedido obtenidos:`, results);
            socket.emit('detallesPedido', results);
        });
    });

    // Asignar ID de repartidor para iniciar polling
    socket.on('setRepartidorId', (idRepartidor) => {
        socket.idRepartidor = idRepartidor;

        // Iniciar polling si no está activo
        if (!isPollingActive) {
            isPollingActive = true;
            console.log(`Polling iniciado para el repartidor ID: ${idRepartidor}`);
            iniciarPolling(socket, connection, idRepartidor);
        }
    });

    // Obtener todos los repartidores
    socket.on('getAllRepartidores', () => {
        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Solicitud de lista de repartidores`);

        const sql = `
            SELECT id_repartidor, id_usuario, id_vehiculo, nombre, apellido_paterno, 
                   apellido_materno, telefono, estado
            FROM repartidores;
        `;
        connection.query(sql, (err, repartidores) => {
            if (err) {
                console.error(`[${currentTime}] Error en la consulta:`, err);
                socket.emit('errorRepartidores', { message: 'Error al obtener repartidores' });
                return;
            }
            console.log(`[${currentTime}] Repartidores obtenidos:`, repartidores);
            socket.emit('repartidores', repartidores);
        });
    });

    // Agregar repartidor a un pedido
    socket.on('agregarRepartidorAlPedido', (data) => {
        const currentTime = new Date().toLocaleString();
        console.log(`[${currentTime}] Solicitud para asignar repartidor`, data);

        const { id_pedido, id_repartidor } = data;

        const sql = `
            UPDATE pedidos 
            SET id_repartidor = ? 
            WHERE id_pedido = ? AND estado = 'pendiente';
        `;
        connection.query(sql, [id_repartidor, id_pedido], (err, result) => {
            if (err) {
                console.error(`[${currentTime}] Error al actualizar el pedido:`, err);
                socket.emit('errorAgregarRepartidor', { message: 'Error al asignar repartidor al pedido' });
                return;
            }

            if (result.affectedRows > 0) {
                console.log(`[${currentTime}] Repartidor asignado exitosamente`);
                socket.emit('repartidorAgregado', { message: 'Repartidor asignado correctamente' });
            } else {
                console.log(`[${currentTime}] No se pudo asignar el repartidor`);
                socket.emit('errorAgregarRepartidor', { message: 'No se pudo asignar repartidor al pedido' });
            }
        });
    });

    // Iniciar el polling
    function iniciarPolling(socket, connection, idRepartidor) {
        let lastPedidosState = []; // Almacenar último estado de pedidos

        const interval = setInterval(() => {
            if (!socket.connected) {
                clearInterval(interval);
                isPollingActive = false;
                console.log(`Polling detenido para el repartidor ID: ${idRepartidor}`);
                return;
            }

            const currentTime = new Date().toLocaleString();
            console.log(`[${currentTime}] Polling de pedidos para repartidor ID: ${idRepartidor}`);

            const sql = `
                SELECT p.id_pedido, r.nombre AS restaurante, c.nombre_cliente AS cliente, c.telefono AS telefono_cliente, 
                       p.estado, p.total, p.fecha, re.nombre AS repartidor, re.telefono AS telefono_repartidor
                FROM pedidos p
                LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
                LEFT JOIN repartidores re ON p.id_repartidor = re.id_repartidor
                WHERE p.id_repartidor = ?; 
            `;

            connection.query(sql, [idRepartidor], (err, pedidos) => {
                if (err) {
                    console.error(`[${currentTime}] Error en la consulta:`, err);
                    return;
                }

                // Verificar cambios en pedidos
                if (JSON.stringify(pedidos) !== JSON.stringify(lastPedidosState)) {
                    lastPedidosState = pedidos;
                    console.log(`[${currentTime}] Pedidos actualizados. Emitiendo a cliente.`);

                    let pedidosPendientes = pedidos.length;

                    if (pedidosPendientes === 0) {
                        socket.emit('pedidosRepartidor', pedidos);
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
                                    console.error(`[${currentTime}] Error al obtener productos:`, err);
                                } else {
                                    pedidos[index].productos = productos.map(prod => 
                                        `${prod.nombre} x ${prod.cantidad} S/ ${prod.precio}`
                                    ).join(', ');
                                }

                                // Emitir datos cuando todos los productos estén listos
                                pedidosPendientes--;
                                if (pedidosPendientes === 0) {
                                    socket.emit('pedidosRepartidor', pedidos);
                                }
                            });
                        });
                    }
                } else {
                    console.log(`[${currentTime}] No hay cambios en los pedidos.`);
                }
            });
        }, 5000); // Intervalo de 5 segundos
    }


    // Detener polling al desconectarse
    socket.on('disconnect', () => {
        console.log(`Cliente desconectado. Deteniendo polling.`);
        isPollingActive = false;
    });
};
