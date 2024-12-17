module.exports = (socket, connection, io) => {
    // Actualizar cantidad o eliminar detalle
    socket.on('actualizarCantidad', ({ id_detalle, cantidad }) => {
        let sql;
        let queryParams;

        if (cantidad === 0) {
            // Eliminar detalle si la cantidad es 0
            sql = "DELETE FROM detalle_pedido WHERE id_detalle = ?";
            queryParams = [id_detalle];
        } else {
            // Actualizar cantidad y recalcular subtotal
            sql = "UPDATE detalle_pedido SET cantidad = ?, subtotal = (? * (SELECT precio FROM productos WHERE id_producto = detalle_pedido.id_producto)) WHERE id_detalle = ?";
            queryParams = [cantidad, cantidad, id_detalle];
        }

        connection.query(sql, queryParams, (err, results) => {
            if (err) {
                console.error('Error al actualizar la cantidad:', err);
                return;
            }

            // Obtener el `id_pedido` para actualizar el total
            const getPedidoSQL = `
                SELECT id_pedido 
                FROM detalle_pedido 
                WHERE id_detalle = ? 
                UNION SELECT id_pedido FROM detalle_pedido WHERE ? = 0 LIMIT 1`;
            
            connection.query(getPedidoSQL, [id_detalle, cantidad], (err, pedidoResults) => {
                if (err) {
                    console.error('Error al obtener el pedido:', err);
                    return;
                }

                if (pedidoResults.length > 0) {
                    const id_pedido = pedidoResults[0].id_pedido;

                    // Recalcular el total del pedido
                    const updateTotalSQL = `
                        UPDATE pedidos 
                        SET total = (SELECT IFNULL(SUM(subtotal), 0) FROM detalle_pedido WHERE id_pedido = ?)
                        WHERE id_pedido = ?`;

                    connection.query(updateTotalSQL, [id_pedido, id_pedido], (err) => {
                        if (err) {
                            console.error('Error al actualizar el total del pedido:', err);
                            return;
                        }

                        console.log(`Total del pedido actualizado para el pedido ${id_pedido}`);

                        if (cantidad === 0) {
                            // Emitir evento para detalle eliminado
                            socket.emit('cantidadEliminada', { id_detalle });
                            io.emit('actualizarDetalles', { id_detalle, tipo: 'eliminar' });
                        } else {
                            // Emitir evento para detalle actualizado
                            socket.emit('cantidadActualizada', { id_detalle, cantidad });
                            io.emit('actualizarDetalles', { id_detalle, tipo: 'actualizar', cantidad });
                        }

                        // Emitir evento para el nuevo total
                        io.emit('totalActualizado', { id_pedido });
                    });
                }
            });
        });
    });
};
