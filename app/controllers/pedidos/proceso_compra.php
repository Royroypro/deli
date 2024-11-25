<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carrito = json_decode($_POST['carrito'], true);

    if ($carrito) {
        $total = $carrito['total'];
        $productos = $carrito['productos'];

        // Procesar los datos (ejemplo: guardar en base de datos)
        foreach ($productos as $producto) {
            $id_producto = $producto['id_producto'];
            $nombre = $producto['nombre'];
            $precio = $producto['precio'];
            $cantidad = $producto['cantidad'];

            // Guardar lógica aquí
        }

        echo "Compra procesada con éxito. Total: S/ $total.";
    } else {
        echo "Error: No se recibieron datos del carrito.";
    }
}
?>
