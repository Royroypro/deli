<?php
include('../app/config.php');
include('../layout/sesion.php');
include "../admin/layout/parte1.php";

// Obtener id_pedido desde la URL
$id_pedido = isset($_GET['id']) ? intval($_GET['id']) : null;

// Consulta para obtener detalles del pedido
$consulta = $pdo->prepare("SELECT p.id_pedido, p.id_cliente, p.id_restaurante, p.id_repartidor, p.estado, p.total, p.fecha, dp.id_detalle, dp.id_producto, dp.cantidad, dp.subtotal, pr.nombre
    FROM pedidos p
    INNER JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
    INNER JOIN productos pr ON dp.id_producto = pr.id_producto
    WHERE p.id_pedido = :id_pedido");

$consulta->execute(['id_pedido' => $id_pedido]);
$resultados = $consulta->fetchAll();

if ($resultados) {
    // Asignación de datos del pedido
    $id_pedido = $resultados[0]['id_pedido'];
    $id_cliente = $resultados[0]['id_cliente'];
    $id_restaurante = $resultados[0]['id_restaurante'];
    $id_repartidor = $resultados[0]['id_repartidor'];
    $estado = $resultados[0]['estado'];
    $total = $resultados[0]['total'];
    $fecha_pedido = $resultados[0]['fecha'];

    // Preparación de los detalles del pedido
    $detalles = [];
    foreach ($resultados as $resultado) {
        $detalles[] = [
            'id_detalle' => $resultado['id_detalle'],
            'id_producto' => $resultado['id_producto'],
            'nombre_producto' => $resultado['nombre'],
            'cantidad' => $resultado['cantidad'],
            'subtotal' => $resultado['subtotal'],
        ];
    }
}
?>


<div class="full-width divider-menu-h"></div>
<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--12-col">
        <div class="full-width panel mdl-shadow--2dp">
            
            <div class="full-width panel-content">
                <form>
                    <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
                    
                    <div class="mdl-grid">
                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; Información del pedido</legend><br>
                        </div>
                        <!-- Información del cliente y restaurante -->

                        <?php
                        $consulta = $pdo->prepare("SELECT nombre FROM restaurantes WHERE id_restaurante = :id_restaurante");
                        $consulta->execute(['id_restaurante' => $id_restaurante]);
                        $resultado = $consulta->fetch();
                        $nombre_restaurante = $resultado['nombre'] ?? null;
                        ?>
                        <div class="mdl-cell mdl-cell--4-col">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" id="nombre_restaurante" value="<?php echo htmlspecialchars($nombre_restaurante); ?>" required readonly>
                                <label class="mdl-textfield__label" for="nombre_restaurante">Nombre del restaurante</label>
                            </div>
                        </div>
                        <!-- Información del estado, total y fecha del pedido -->
                        
                        <div class="mdl-cell mdl-cell--6-col">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" disabled>
                                <input class="mdl-textfield__input" type="number" id="total" value="<?php echo $total; ?>" required readonly>
                                <label class="mdl-textfield__label" for="total">Total</label>
                            </div>
                        </div>
                        <div class="mdl-cell mdl-cell--6-col">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="date" id="fecha" value="<?php echo date('Y-m-d', strtotime($fecha_pedido)); ?>" required>
                                <label class="mdl-textfield__label" for="fecha">Fecha</label>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; Productos</legend><br>
                        </div>
                        
                        <!-- Botón para agregar producto -->
                        <div class="mdl-cell mdl-cell--12-col">
                            <button id="btn-agregar-producto" class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" type="button" onclick="abrirModalAgregarProducto()">
                                <i class="mdl-color-text--white zmdi zmdi-plus"></i>
                            </button>
                        </div>

<script src="https://royner.ddns.net:8080/socket.io/socket.io.js"></script>
<script>
const socket = io('https://royner.ddns.net:8080');
// Conectar con el servidor WebSocket

// ID del pedido que deseas consultar
const idPedido = <?php echo $id_pedido; ?>; // Puedes obtenerlo dinámicamente según el contexto

// Enviar la solicitud al servidor para obtener los detalles del pedido
socket.emit('getDetallesPedido', idPedido);

// Escuchar los detalles del pedido cuando el servidor los envíe
socket.on('detallesPedido', (detalles) => {
    // Limpiar la lista existente
    const ul = document.getElementById('detalle-lista');
    ul.innerHTML = '';

    // Recorrer los detalles y mostrarlos
    detalles.forEach(detalle => {
        const li = document.createElement('li');
        li.classList.add('mdl-list__item');
        li.id = `detalle_${detalle.id_detalle}`;

        li.innerHTML = `
            <span class="mdl-list__item-primary-content">
                <input class="mdl-textfield__input" type="text" id="nombre_producto_${detalle.id_detalle}" value="${detalle.nombre_producto}" readonly>
                <input class="mdl-textfield__input" type="number" id="cantidad_producto_${detalle.id_detalle}" value="${detalle.cantidad}" readonly>
            </span>
            <span class="mdl-list__item-secondary-action">
                <button class="mdl-button mdl-js-button mdl-button--icon" onclick="event.preventDefault(); modificarCantidad(${detalle.id_detalle}, 1)">
                    <i class="zmdi zmdi-plus"></i>
                </button>
                <button class="mdl-button mdl-js-button mdl-button--icon" onclick="event.preventDefault(); modificarCantidad(${detalle.id_detalle}, -1)">
                    <i class="zmdi zmdi-minus"></i>
                </button>
            </span>
        `;
        ul.appendChild(li);
    });
});



socket.on('actualizarDetalles', (data) => {
    const { id_detalle, tipo = 'agregar', nombre_producto, cantidad } = data;
    const li = document.getElementById(`detalle_${id_detalle}`);
    
    if (tipo === 'agregar') {
        const ul = document.getElementById('detalle-lista');
        const nuevoDetalle = document.createElement('li');
        nuevoDetalle.classList.add('mdl-list__item');
        nuevoDetalle.id = `detalle_${id_detalle}`;
        nuevoDetalle.innerHTML = `
            <span class="mdl-list__item-primary-content">
                <input class="mdl-textfield__input" type="text" id="nombre_producto_${id_detalle}" value="${nombre_producto}" readonly>
                <input class="mdl-textfield__input" type="number" id="cantidad_producto_${id_detalle}" value="${cantidad}" readonly>
            </span>
            <span class="mdl-list__item-secondary-action">
                <button class="mdl-button mdl-js-button mdl-button--icon" onclick="event.preventDefault(); modificarCantidad(${id_detalle}, 1)">
                    <i class="zmdi zmdi-plus"></i>
                </button>
                <button class="mdl-button mdl-js-button mdl-button--icon" onclick="event.preventDefault(); modificarCantidad(${id_detalle}, -1)">
                    <i class="zmdi zmdi-minus"></i>
                </button>
            </span>
        `;
        ul.appendChild(nuevoDetalle);
    } else if (tipo === 'eliminar' && li) {
        li.remove();
    } else if (tipo === 'actualizar' && li) {
        const cantidadInput = li.querySelector(`#cantidad_producto_${id_detalle}`);
        cantidadInput.value = cantidad;
    }
});


// Función para modificar la cantidad (actualizar en el servidor)
function modificarCantidad(idDetalle, cambio) {
    const cantidadInput = document.getElementById(`cantidad_producto_${idDetalle}`);
    let cantidad = parseInt(cantidadInput.value) + cambio;
    cantidadInput.value = cantidad;

    // Enviar la actualización al servidor
    socket.emit('actualizarCantidad', {
        id_detalle: idDetalle,
        cantidad
    });

    if (cantidad === 0) {
        // Si la cantidad es cero, eliminar el detalle
        const li = document.getElementById(`detalle_${idDetalle}`);
        if (li) {
            li.remove();
        }

        // Enviar la solicitud de eliminación al servidor
        socket.emit('eliminarDetalle', {
            id_detalle: idDetalle
        });
    }
}



window.abrirModalAgregarProducto = function (idRestaurante = <?php echo $id_restaurante; ?>) {
    // Crear el modal
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modal.style.display = 'flex';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';
    modal.setAttribute('id', 'modal-agregar-producto');

    // Crear contenido inicial del modal
    modal.innerHTML = `
        <div style="width: 700px; background: white; padding: 20px; border-radius: 5px; max-height: 80%; overflow-y: auto;">
            <h2>Cargando productos...</h2>
        </div>
    `;

    document.body.appendChild(modal);
    // Realizar solicitud al servidor para obtener productos
    socket.emit('getProductosPorRestaurante', idRestaurante);
    
   // Escuchar respuesta del servidor
socket.on('productos', (productos) => {
    console.log('ID de restaurante enviado:', idRestaurante);
    const modal = document.getElementById('modal-agregar-producto');
    if (productos) {
        // Renderizar productos en el modal
        const contenido = `
            <div style="width: 700px; background: white; padding: 20px; border-radius: 5px; max-height: 80%; overflow-y: auto;">
                <h2>Lista de Productos</h2>
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                    ${productos.map(producto => {
                        try {
                            return `
                                <div style="border: 1px solid #ccc; border-radius: 5px; padding: 10px; width: 200px;">
                                    <img src="../admin/imgs/productos/productos/${producto.imagen || '/imagenes/productos/placeholder.png'}" 
                                        alt="${producto.nombre}" 
                                        style="width: 100%; height: 100px; object-fit: cover; border-radius: 5px;">
                                    <h3 style="font-size: 16px;">${producto.nombre}</h3>
                                    <p>${producto.descripcion || 'Sin descripción'}</p>
                                    <p><strong>Precio:</strong> S/ ${producto.precio}</p>
                                    <p><strong>Stock:</strong> ${producto.stock}</p>
                                    <button 
                                        onclick="agregarProductoPedido(${producto.id_producto}, ${producto.precio}, 1)" 
                                        class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
                                        Agregar
                                    </button>
                                </div>
                            `;
                        } catch (error) {
                            console.error(`Error al renderizar producto: ${error.message}`);
                            return '';
                        }
                    }).join('')}
                </div>
                <button onclick="cerrarModal()" class="mdl-button mdl-js-button mdl-button--raised">
                    Cerrar
                </button>
            </div>
        `;
        modal.innerHTML = contenido;
    } else {
        modal.innerHTML = `
            <div style="width: 700px; background: white; padding: 20px; border-radius: 5px; text-align: center;">
                <h2>Error</h2>
                <p>No se pudieron cargar los productos.</p>
                <button onclick="cerrarModal()" class="mdl-button mdl-js-button mdl-button--raised">
                    Cerrar
                </button>
            </div>
        `;
    }
});


// Función para cerrar el modal
function cerrarModal() {
    const modal = document.getElementById('modal-agregar-producto');
    if (modal) {
        modal.remove();
    }
}

// Exponer la función cerrarModal para uso en los botones
window.cerrarModal = cerrarModal;
}

// Función para agregar un producto al pedido
window.agregarProductoPedido = function agregarProductoPedido(id_producto, precio, cantidad) {
    const modal = document.getElementById('modal-agregar-producto');
    const notificationContainer = document.getElementById('notification-container');

    if (!modal) {
        mostrarNotificacion('No se encontró el modal', 'error');
        return;
    }

    fetch('https://royner.ddns.net:8080/agregarProductoPedido', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_pedido: <?php echo $id_pedido; ?>, id_producto, precio, cantidad })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.status) {
            mostrarNotificacion('Producto agregado correctamente', 'success');
        } else if (data.message === 'El producto ya ha sido agregado al pedido') {
            mostrarNotificacion('El producto ya ha sido agregado al pedido', 'warning');
        } else {
            mostrarNotificacion(`Error: ${data.message}`, 'error');
        }
        cerrarModal(); // Cerrar el modal después de intentar agregar el producto
    })
    .catch(error => {
        mostrarNotificacion(`Error al agregar producto al pedido: ${error.message}`, 'error');
    });
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo) {
    const notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        console.error('No se encontró el contenedor de notificaciones');
        return;
    }

    const notification = document.createElement('div');
    notification.className = `notification ${tipo}`;
    notification.textContent = mensaje;

    notificationContainer.appendChild(notification);

    // Eliminar notificación después de 3 segundos
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Función para cerrar el modal
function cerrarModal() {
    const modal = document.getElementById('modal-agregar-producto');
    if (modal) {
        modal.style.display = 'none';
    }
}


// Función para calcular el subtotal y el total
function calcularTotales() {
    const detalles = document.querySelectorAll('#detalle-lista li'); // Seleccionar los detalles
    let subtotal = 0;

    detalles.forEach((detalle) => {
        const cantidad = parseInt(
            detalle.querySelector('input[id^="cantidad_producto_"]').value
        );
        const precio = parseFloat(
            detalle.querySelector('input[id^="nombre_producto_"]').getAttribute('data-precio')
        );

        subtotal += cantidad * precio;
    });

    // Actualizar los valores en la interfaz
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('total').textContent = (subtotal * 1.18).toFixed(2); // Incluye IGV (18%)
}

// Escuchar eventos de modificación de cantidad
function modificarCantidad(idDetalle, cambio) {
    const cantidadInput = document.getElementById(`cantidad_producto_${idDetalle}`);
    let cantidad = parseInt(cantidadInput.value) + cambio;

    if (cantidad < 0) return; // No permitir cantidades negativas

    cantidadInput.value = cantidad;

    // Enviar la actualización al servidor
    socket.emit('actualizarCantidad', {
        id_detalle: idDetalle,
        cantidad,
    });

    if (cantidad === 0) {
        const li = document.getElementById(`detalle_${idDetalle}`);
        if (li) li.remove();
        socket.emit('eliminarDetalle', { id_detalle: idDetalle });
    }

    // Recalcular totales
    calcularTotales();
}

</script>
<div id="notification-container" style="position: fixed; top: 10px; right: 10px; z-index: 9999;"></div>

                        <!-- Lista de productos -->
                        <div class="mdl-cell mdl-cell--12-col">
                            <ul class="mdl-list" id="detalle-lista">
                                <!-- Los detalles se mostrarán aquí -->
                            </ul>
                        </div>




                    </div>

                    <div class="mdl-cell mdl-cell--12-col">
                        
                    
                            <div class="mdl-tooltip" for="btn-refrescarPedido">Refrescar</div>
                        
                    </div>

                </form>
                <button class="mdl-button mdl-js-button mdl-js-ripple-effect" onclick="window.history.back()" style="background-color: orange;">
                                Actualizar
                            </button>
                
                <div class="mdl-cell mdl-cell--12-col" style="text-align: right;">
                    <a href="lista_pedidos_cliente.php" class="mdl-button mdl-js-button mdl-js-ripple-effect" style="background-color: green;">
                        <i class="zmdi zmdi-arrow-left"></i> Ir a lista de pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification {
    padding: 10px 20px;
    margin-bottom: 10px;
    border-radius: 5px;
    color: white;
    font-size: 14px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    animation: fade-in 0.3s ease-out;
}
.notification.success { background-color: #4CAF50; } /* Verde */
.notification.error { background-color: #f44336; }   /* Rojo */
.notification.warning { background-color: #FFC107; } /* Amarillo */

@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>

<?php include "../layout/parte2.php"; ?>