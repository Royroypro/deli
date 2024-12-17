<?php
include('../app/config.php');
include('../layout/sesion.php');
include "../admin/layout/parte1.php";


?>
<?php

/* include "../layout/parte1.php";  */


?>
<style>
    /*  body {
        margin-top: 70px;
    } */
</style>

<section class="container-fluid py-3 bg-danger text-white rounded">
    <div class="d-flex align-items-center">
        <i class="zmdi zmdi-shopping-cart h3 me-3"></i>
        <div>
            <p class="h4 m-0">Pedidos</p>
        </div>
    </div>
</section>

<div class="my-3">
    <div class="d-flex justify-content-center">
        <button id="enableSoundButton" class="btn btn-warning w-100 w-md-auto">
            Habilitar sonido
        </button>
    </div>
</div>
<!-- Agregar el CSS de Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Agregar el JS de Bootstrap, asegúrate de que esté después de jQuery si lo usas, o bien después de tu propio JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<div class="table-responsive">
    <table id="pedidosTable" class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>N° Pedido</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Repartidor</th>
                <th>Productos</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody id="pedidosTableBody">
            <!-- Las filas de los pedidos se agregarán dinámicamente aquí -->
        </tbody>
    </table>
</div>

<!-- Estilos adicionales -->
<style>
    /* Estilo personalizado para el encabezado */
    .bg-danger {
        background-color: #FF6347 !important;
        width: 100%;
        height: 50px;
    }

    .table {
        font-size: 14px;
    }

    .table th,
    .table td {
        text-align: center;
        vertical-align: middle;
    }

    /* Ajustes para botones */
    .btn-warning {
        background-color: #FFA500;
        color: white;
        border-radius: 5px;
        font-weight: bold;
    }

    .btn-warning:hover {
        background-color: #FF8C00;
    }

    /* Mejorar tabla en pantallas pequeñas */
    @media (max-width: 768px) {

        .table th,
        .table td {
            font-size: 12px;
            padding: 8px;
        }

        .table-responsive {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 576px) {

        .table th,
        .table td {
            font-size: 10px;
            padding: 6px;
        }

        .h4 {
            font-size: 20px;
        }

        .btn-warning {
            font-size: 12px;
            padding: 8px;
        }
    }
</style>


<script src="http://cespedes.ddns.net:8080/socket.io/socket.io.js"></script>
<script>
    // Conexión al servidor WebSocket
    const socket = io.connect('http://cespedes.ddns.net:8080');
    const idRestaurante = <?php echo $id_restaurante_sesion; ?>; // Obtener el idRestaurante de PHP

    // Variables para almacenar estados
    let lastPedidosState = JSON.parse(localStorage.getItem('lastPedidosState')) || {}; // Estado previo de pedidos
    let isSoundEnabled = localStorage.getItem('isSoundEnabled') === 'true'; // Estado del sonido

    // Botón para habilitar sonido
    const enableSoundButton = document.getElementById('enableSoundButton');
    if (isSoundEnabled) enableSoundButton.style.display = 'none'; // Ocultar si ya está habilitado

    enableSoundButton.addEventListener('click', () => {
        isSoundEnabled = true;
        localStorage.setItem('isSoundEnabled', 'true');
        enableSoundButton.style.display = 'none';
        alert('Sonido habilitado');
    });

    // Función para reproducir sonido
    function reproducirSonido() {
        const audio = new Audio('notification-9-158194.mp3');
        audio.play();
    }

    // Función principal para obtener los pedidos
    function obtenerPedidosRestaurante() {
        socket.emit('setRestauranteId', idRestaurante);
    }

    // Evento al recibir pedidos del servidor
    socket.on('pedidosRestaurante', (pedidos) => {
        const pedidosTableBody = document.getElementById('pedidosTableBody');
        pedidosTableBody.innerHTML = ''; // Limpiar la tabla

        const nuevoEstado = {}; // Estado actual de pedidos

        pedidos.forEach(pedido => {
            // Crear fila para la tabla
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${pedido.id_pedido}</td>
                <td>${pedido.fecha}</td>
                <td>
                    ${pedido.cliente}
                    <button type="button" class="mdl-button mdl-button--colored mdl-button--primary" onclick="abrirModalDetallesCliente('${pedido.cliente}')">Ver más</button>
                </td>
                <td>
                    ${pedido.repartidor ? pedido.repartidor : ''}
                    ${pedido.repartidor ? `
                    <a href="https://wa.me/${pedido.telefono_repartidor.startsWith('+51') ? pedido.telefono_repartidor : '+51' + pedido.telefono_repartidor}" target="_blank" class="mdl-button mdl-button--colored" style="background-color: green; color: white;">
                        <i class="zmdi zmdi-phone"></i> Contactar
                    </a>
                    <button type="button" class="mdl-button mdl-button--colored" style="background-color: blue; color: white;" onclick="abrirModalAgregarRepartidor(${pedido.id_pedido})">
                        Editar Repartidor
                    </button>` : `
                    <button type="button" class="mdl-button mdl-button--colored" style="background-color: blue; color: white;" onclick="abrirModalAgregarRepartidor(${pedido.id_pedido})">
                        Agregar Repartidor
                    </button>`}
                </td>
                <td class="productos" id="productos-${pedido.id_pedido}">${pedido.productos ? '' : 'Ninguno'}</td>
                <td style="color: ${pedido.estado === 'aceptado' ? 'green' : 'red'}">${pedido.estado === 'cancelar' ? 'Cancelado' : pedido.estado}
                    <button type="button" class="mdl-button mdl-button--icon" onclick="abrirModalCambiarEstado(${pedido.id_pedido}, '${pedido.estado}')">
                        <i class="zmdi zmdi-edit"></i>
                    </button>
                </td>
                <td>S/ ${pedido.total}</td>
                <td>
                    <a href="mas_detalles_restaurante.php?id=${pedido.id_pedido}" class="mdl-button">Ver detalles</a>
                </td>
            `;
            pedidosTableBody.appendChild(row);

            // Actualizar productos si están disponibles
            if (pedido.productos) {
                actualizarProductos(pedido.productos, pedido.id_pedido);
            } else {
                socket.emit('getDetallesPedido', pedido.id_pedido);
            }

            // Detectar cambios y nuevos pedidos para reproducir sonido
            if (
                !lastPedidosState[pedido.id_pedido] || // Si el pedido es nuevo
                lastPedidosState[pedido.id_pedido] !== pedido.estado // Si el estado cambia
            ) {
                if (isSoundEnabled) reproducirSonido();
            }

            // Guardar el estado actual del pedido
            nuevoEstado[pedido.id_pedido] = pedido.estado;
        });

        // Actualizar estado local y persistirlo en localStorage
        lastPedidosState = nuevoEstado;
        localStorage.setItem('lastPedidosState', JSON.stringify(nuevoEstado));
    });

    // Función para actualizar la celda con productos
    function actualizarProductos(productosStr, idPedido) {
        const productosCell = document.getElementById(`productos-${idPedido}`);
        productosCell.innerHTML = productosStr;
    }

    // Llamada inicial para obtener pedidos
    obtenerPedidosRestaurante();





    // Función para abrir la modal de agregar repartidor
    function abrirModalAgregarRepartidor(idPedido, estado) {
        document.getElementById('repartidorModal').setAttribute('data-idpedido', idPedido);
        document.getElementById('repartidorModal').setAttribute('data-estado', estado);

        var modal = new bootstrap.Modal(document.getElementById('repartidorModal'));
        modal.show();
    }

     // Solicitar todos los repartidores
     socket.emit('getAllRepartidores');

// Escuchar el evento 'repartidores' que el servidor emite
socket.on('repartidores', (repartidores) => {
    const repartidorSelect = document.getElementById('repartidorSelect');

    // Limpiar opciones anteriores
    repartidorSelect.innerHTML = '<option value="">Seleccione un repartidor...</option>';

    // Agregar los repartidores al select
    repartidores.forEach(repartidor => {
        const option = document.createElement('option');
        option.value = repartidor.id_repartidor;
        option.textContent = `${repartidor.nombre} ${repartidor.apellido_paterno} ${repartidor.apellido_materno}`;
        repartidorSelect.appendChild(option);
    });
});

// Escuchar el evento de error si no se pueden obtener los repartidores
socket.on('errorRepartidores', (error) => {
    console.error("Error al obtener los repartidores:", error.message);
});

// Escuchar el evento de respuesta al agregar repartidor
socket.on('repartidorAgregado', (response) => {
    alert(response.message);
    // Cerrar el modal
    var modal = new bootstrap.Modal(document.getElementById('repartidorModal'));
    modal.hide();
});


    
</script>

<!-- Modal para agregar repartidor -->
<div class="modal" id="repartidorModal" tabindex="-1" aria-labelledby="repartidorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repartidorModalLabel">Asignar Repartidor al Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarRepartidor">
                    <div class="form-group">
                        <label for="repartidorSelect">Seleccione un Repartidor</label>
                        <select class="form-control" id="repartidorSelect" required>
                            <!-- Aquí se cargarán los repartidores -->
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Asignar Repartidor</button>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Modal for client details -->
<style>
    #modal-detalles-cliente {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        max-width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }

    .mdl-dialog__title {
        font-size: 1.5em;
        font-weight: bold;
        text-align: center;
    }

    #cliente-detalles-content {
        font-size: 1.2em;
        text-align: justify;
    }
</style>

<div id="modal-detalles-cliente" class="mdl-dialog" style="display: none;">
    <h4 class="mdl-dialog__title">Detalles del Cliente</h4>
    <div class="mdl-dialog__content">
        <p id="cliente-detalles-content">Cargando detalles...</p>
    </div>
    <div class="mdl-dialog__actions">
        <button type="button" class="mdl-button close" onclick="cerrarModalDetallesCliente()">Cerrar</button>
    </div>
</div>





<script>
                    function abrirModalDetallesCliente(nombreCliente) {
                        const modal = document.getElementById('modal-detalles-cliente');
                        const content = document.getElementById('cliente-detalles-content');
                        fetch(`detalles_cliente.php?nombre=${encodeURIComponent(nombreCliente)}`)
                            .then(response => {
                                if (response.ok) {
                                    return response.json();
                                } else {
                                    throw new Error('Error al obtener detalles del cliente');
                                }
                            })
                            .then(detalles => {
                                const {
                                    nombre_cliente,
                                    direccion,
                                    telefono,
                                    puntos_fidelidad
                                } = detalles;
                                content.innerHTML = `
                <h4>${nombre_cliente}</h4>
                <p>Dirección: ${direccion}</p>
                <p>Teléfono: ${telefono}</p>
                <p>Puntos de fidelidad: ${puntos_fidelidad}</p>
            `;
                            })
                            .catch(error => console.error('Error al obtener detalles del cliente:', error));
                        modal.style.display = 'block';
                    }

                    function cerrarModalDetallesCliente() {
                        const modal = document.getElementById('modal-detalles-cliente');
                        modal.style.display = 'none';
                    }
                </script>











<script>
   // Función para abrir la modal de agregar repartidor
function abrirModalAgregarRepartidor(idPedido, estado) {
    // Establecer atributos en el modal para referencia del pedido
    document.getElementById('repartidorModal').setAttribute('data-idpedido', idPedido);
    document.getElementById('repartidorModal').setAttribute('data-estado', estado);

    // Mostrar el modal
    var modal = new bootstrap.Modal(document.getElementById('repartidorModal'));
    modal.show();

   
}

// Solicitar todos los repartidores al servidor
socket.emit('getAllRepartidores');

// Escuchar el evento 'repartidores' que el servidor emite
socket.on('repartidores', (repartidores) => {
    const repartidorSelect = document.getElementById('repartidorSelect');

    // Limpiar opciones anteriores
    repartidorSelect.innerHTML = '<option value="">Seleccione un repartidor...</option>';

    // Agregar los repartidores al select
    repartidores.forEach(repartidor => {
        const option = document.createElement('option');
        option.value = repartidor.id_repartidor;
        option.textContent = `${repartidor.nombre} ${repartidor.apellido_paterno} ${repartidor.apellido_materno}`;
        repartidorSelect.appendChild(option);
    });
});

// Escuchar el evento de error si no se pueden obtener los repartidores
socket.on('errorRepartidores', (error) => {
    console.error("Error al obtener los repartidores:", error.message);
});

// Manejar el envío del formulario para asignar el repartidor al pedido
document.getElementById('formAgregarRepartidor').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevenir que el formulario se envíe de manera tradicional

    // Obtener el id del pedido y el repartidor seleccionado del modal
    const idPedido = document.getElementById('repartidorModal').getAttribute('data-idpedido');
    const repartidorId = document.getElementById('repartidorSelect').value;

    if (repartidorId) {
        // Emitir el evento al servidor para asignar el repartidor al pedido
        socket.emit('agregarRepartidorAlPedido', {
            id_pedido: idPedido,
            id_repartidor: repartidorId
        });
    } else {
        alert('Por favor, seleccione un repartidor.');
    }
});

// Escuchar respuesta del servidor sobre la asignación del repartidor
socket.on('repartidorAgregado', (response) => {
    alert(response.message);
    // Cerrar el modal
    var modal = new bootstrap.Modal(document.getElementById('repartidorModal'));
    modal.hide();
});

// Escuchar respuesta del servidor sobre la asignación del repartidor
socket.on('repartidorAgregado', (response) => {
    alert(response.message);
});

// Escuchar errores de la asignación del repartidor
socket.on('errorAgregarRepartidor', (error) => {
    console.error("Error al asignar repartidor:", error.message);
    alert(error.message);
});



</script>
</div>
</div>
</div>

<style>
    #modal-cambiar-estado {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        max-width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }
</style>

<div id="modal-cambiar-estado" class="mdl-dialog">
    <div class="mdl-dialog__content">
        <h4>Cambiar estado del pedido</h4>

        <p style="text-align: center; background-color: #ffc107; padding: 10px; border-radius: 5px;">Estado actual: <b id="estado-actual"></b></p>
        <form id="frm-cambiar-estado">
            <?php
            $estados = ['pendiente', 'aceptado', 'preparacion', 'enviado', 'entregado', 'cancelar'];
            echo '<select class="mdl-textfield__input" id="estado" name="estado" required>';
            echo '<option value="">Seleccione un estado</option>';
            foreach ($estados as $estado) {
                $selected = $estado_actual === $estado ? 'selected' : '';
                echo "<option value=\"{$estado}\" $selected>{$estado}</option>";
            }
            echo '</select>';
            ?>
            <br>
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input type="hidden" id="id_pedido" name="id_pedido" value="">
                <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" onclick="cambiarEstadoPedido()">
                    Cambiar
                </button>
            </div>
        </form>
    </div>
    <div class="mdl-dialog__actions">
        <button type="button" class="mdl-button close" onclick="cerrarModalCambiarEstado()">Cerrar</button>
    </div>
</div>

<script>
    function abrirModalCambiarEstado(id_pedido, estado_actual) {
        const modal = document.getElementById('modal-cambiar-estado');
        const select = document.getElementById('estado');
        const id_pedido_input = document.getElementById('id_pedido');
        const estado_actual_span = document.getElementById('estado-actual');
        id_pedido_input.value = id_pedido;
        select.value = estado_actual;
        estado_actual_span.textContent = estado_actual;
        modal.style.display = 'block';
    }

    function cerrarModalCambiarEstado() {
        const modal = document.getElementById('modal-cambiar-estado');
        modal.style.display = 'none';
    }

    document.getElementById('frm-cambiar-estado').addEventListener('submit', function(event) {
        event.preventDefault();
        const id_pedido = document.getElementById('id_pedido').value;
        const estado = document.getElementById('estado').value;
        if (estado) {
            fetch(`cambiar_estado_pedido_restaurante.php?id_pedido=${id_pedido}&estado=${encodeURIComponent(estado)}`)
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Error al cambiar el estado del pedido');
                    }
                })
                .then(response => {
                    if (response.estado === 'success') {
                        cerrarModalCambiarEstado();
                        obtenerPedidosRestaurante();
                    } else {
                        alert(response.mensaje);
                    }
                })
                .catch(error => console.error('Error al cambiar el estado del pedido:', error));
        } else {
            alert('Seleccione un estado');
        }
    });

    function cambiarEstadoPedido() {
        document.getElementById('frm-cambiar-estado').dispatchEvent(new Event('submit'));
    }
</script>


</body>

</html>