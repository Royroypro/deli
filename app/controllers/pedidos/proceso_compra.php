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
    
</head>

<body>


<style>
    /* Contenedor principal del carrito */
    .carrito-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 400px; /* Un poco más grande para mayor espacio */
        margin: 20px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: 'Arial', sans-serif;
    }

    /* Título del carrito */
    .titulo-carrito {
        font-size: 28px; /* Más grande para destacar */
        font-weight: 700; /* Negrita para más énfasis */
        color: #FF6347; /* Rojo tomate, relacionado con la comida rápida */
        margin-bottom: 15px;
        text-align: center;
    }

    /* Lista de carrito sin puntos */
    .lista-carrito {
        list-style: none;
        padding: 0;
        margin: 0;
        width: 100%;
    }

    /* Estilo para cada item de restaurante */
    .restaurante-item {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f2f2f2; /* Línea divisoria suave */
    }

    /* Nombre del restaurante */
    .restaurante-nombre {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }

    /* Lista de productos */
    .productos-lista {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    /* Estilo para cada producto */
    .producto-item {
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background-color: #FFFAF0; /* Fondo suave beige */
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Detalles del restaurante */
    .detalle-restaurante {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        justify-content: space-between;
    }

    /* Etiquetas dentro de los detalles */
    .label {
        font-weight: 600;
        color: #FF6347; /* Color acorde al tema */
        margin-right: 8px;
    }

    /* Campos de prioridad y método de pago */
    .prioridad, .metodoPago {
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #FF6347; /* Rojo para resaltar */
        width: 170px;
        background-color: #FFF3E0; /* Fondo suave */
        font-size: 14px;
        color: #333;
    }

    /* Detalles del pedido */
    .detalles {
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #FF6347;
        width: 170px;
        height: 50px;
        background-color: #FFF3E0;
    }

    /* Botón para enviar el pedido */
    .enviarPedidoRestaurante {
        background-color: #FF6347; /* Rojo vibrante */
        color: white;
        padding: 14px 30px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 600;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    /* Efecto hover sobre el botón */
    .enviarPedidoRestaurante:hover {
        background-color: #FF4500; /* Rojo más fuerte en hover */
        transform: scale(1.05); /* Agranda ligeramente el botón al pasar el mouse */
    }

    /* Agregar una animación sutil al cargar el carrito */
    .carrito-container {
        animation: fadeIn 0.5s ease-out;
    }

    /* Estilos para pantallas pequeñas */
    @media (max-width: 768px) {
        .carrito-container {
            padding: 20px;
        }

        .titulo-carrito {
            font-size: 20px;
        }

        .restaurante-item {
            margin-bottom: 20px;
        }

        .producto-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .detalle-restaurante {
            flex-direction: column;
            align-items: flex-start;
        }

        .enviarPedidoRestaurante {
            padding: 10px 20px;
        }
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }
</style>



<form id="form-carrito" action="enviar_pedido.php" method="POST">
    <div class="carrito-container">
        <h2 class="titulo-carrito">Carrito de Compras</h2>
        <ul id="carrito" class="lista-carrito">
            <?php foreach ($productosPorRestaurante as $restaurante => $productos): ?>
                <li class="restaurante-item">
                    <strong class="restaurante-nombre"><?php echo htmlspecialchars($restaurante); ?></strong>
                    <ul class="productos-lista">
                        <?php foreach ($productos as $index => $producto): ?>
                            <li class="producto-item">
                                <?php
                                $consulta = $pdo->prepare("SELECT imagen FROM productos WHERE nombre = :nombre");
                                $consulta->execute(['nombre' => $producto['nombre']]);
                                $resultado = $consulta->fetch();
                                ?>
                                <img src="../../../admin/imgs/productos/productos/<?php echo htmlspecialchars($resultado['imagen']); ?>" width="50" height="50" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <input type="hidden" name="productos[<?php echo htmlspecialchars($restaurante); ?>][<?php echo $index; ?>][nombre]" value="<?php echo htmlspecialchars($producto['nombre']); ?>" />
                                <input type="hidden" name="productos[<?php echo htmlspecialchars($restaurante); ?>][<?php echo $index; ?>][cantidad]" value="<?php echo $producto['cantidad']; ?>" />
                                <input type="hidden" name="productos[<?php echo htmlspecialchars($restaurante); ?>][<?php echo $index; ?>][precio]" value="<?php echo $producto['precio']; ?>" />
                                <?php echo htmlspecialchars($producto['nombre']); ?> x <?php echo htmlspecialchars($producto['cantidad']); ?> - S/ <?php echo number_format($producto['precio'], 2); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="detalle-restaurante">
                        <label for=""> Precio de Delivery</label>
                        <label for="prioridad_<?php echo htmlspecialchars($restaurante); ?>" class="label">Prioridad:</label>
                        <select id="prioridad_<?php echo htmlspecialchars($restaurante); ?>" name="prioridad_<?php echo htmlspecialchars($restaurante); ?>" class="prioridad">
                            <option value="baja">Rapido(tengo hambre S/. 7) </option>
                            <option value="media">Media(puedo esperar un poquito S/. 5)</option>
                            <option value="alta">Normal(toma tu tiempo S/. 4)</option>
                        </select>
                    </div>
                    <div class="detalle-restaurante">
                        <label for="metodoPago_<?php echo htmlspecialchars($restaurante); ?>" class="label">Método de Pago:</label>
                        <select id="metodoPago_<?php echo htmlspecialchars($restaurante); ?>" name="metodoPago_<?php echo htmlspecialchars($restaurante); ?>" class="metodoPago">
                            <option value="efectivo">Efectivo</option>
                            <option value="yape">Yape</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                    
                    <div class="detalle-restaurante">
                        <label for="detalles_<?php echo htmlspecialchars($restaurante); ?>" class="label">Detalles:</label>
                        <textarea id="detalles_<?php echo htmlspecialchars($restaurante); ?>" name="detalles_<?php echo htmlspecialchars($restaurante); ?>" cols="20" rows="1" class="detalles"></textarea>
                    </div>
                    <button type="submit" data-restaurante="<?php echo htmlspecialchars($restaurante); ?>" class="enviarPedidoRestaurante">Enviar Pedido a <?php echo htmlspecialchars($restaurante); ?></button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</form>


   
</body>
