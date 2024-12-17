module.exports = (app, connection, io) => {
    app.post('/agregarProductoPedido', (req, res) => {
        const { id_pedido, id_producto, precio, cantidad = 1 } = req.body;

        console.log('Solicitud recibida:', req.body);

        if (id_pedido && id_producto && precio !== null) {
            const queryProducto = "SELECT stock, nombre FROM productos WHERE id_producto = ?";

            connection.query(queryProducto, [id_producto], (err, results) => {
                if (err) {
                    console.error('Error al consultar el producto:', err);
                    res.status(500).json({ status: false, message: 'Error en la base de datos' });
                    return;
                }

                const producto = results[0];
                if (producto && producto.stock >= cantidad) {
                    const queryCheck = "SELECT cantidad, subtotal FROM detalle_pedido WHERE id_pedido = ? AND id_producto = ?";
                    connection.query(queryCheck, [id_pedido, id_producto], (err, results) => {
                        if (err) {
                            console.error('Error al verificar el producto:', err);
                            res.status(500).json({ status: false, message: 'Error en la base de datos' });
                            return;
                        }

                        if (results.length > 0) {
                            console.log('El producto ya ha sido agregado:', results[0]);
                            res.json({ status: false, message: 'El producto ya ha sido agregado' });
                        } else {
                            const subtotal = cantidad * precio;
                            const queryInsert = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
                            connection.query(queryInsert, [id_pedido, id_producto, cantidad, subtotal], (err, results) => {
                                if (err) {
                                    console.error('Error al insertar el producto:', err);
                                    res.status(500).json({ status: false, message: 'Error en la base de datos' });
                                    return;
                                }

                                console.log('Producto agregado correctamente:', { id_pedido, id_producto, cantidad, subtotal });

                                // Actualizar el stock
                                const queryUpdateStock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
                                connection.query(queryUpdateStock, [cantidad, id_producto], (err) => {
                                    if (err) {
                                        console.error('Error al actualizar el stock:', err);
                                    } else {
                                        console.log('Stock actualizado correctamente para el producto:', id_producto);
                                    }
                                });

                                // Recalcular el total del pedido
                                const queryUpdateTotal = `
                                    UPDATE pedidos 
                                    SET total = (SELECT IFNULL(SUM(subtotal), 0) FROM detalle_pedido WHERE id_pedido = ?)
                                    WHERE id_pedido = ?`;

                                connection.query(queryUpdateTotal, [id_pedido, id_pedido], (err) => {
                                    if (err) {
                                        console.error('Error al actualizar el total del pedido:', err);
                                    } else {
                                        console.log('Total del pedido actualizado correctamente para el pedido:', id_pedido);

                                        // Emitir el evento al cliente
                                        io.emit('actualizarDetalles', {
                                            id_detalle: results.insertId,
                                            id_producto,
                                            nombre_producto: producto.nombre,
                                            cantidad,
                                            subtotal,
                                        });

                                        io.emit('totalActualizado', { id_pedido });
                                    }
                                });

                                res.json({ status: true, message: 'Producto agregado correctamente' });
                            });
                        }
                    });
                } else {
                    console.log('Stock insuficiente para el producto:', id_producto);
                    res.json({ status: false, message: 'Stock insuficiente' });
                }
            });
        } else {
            res.status(400).json({ status: false, message: 'Faltan datos necesarios' });
        }
    });
};

