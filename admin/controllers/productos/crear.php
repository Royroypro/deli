<?php

include_once '../../../app/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    try {

        $nombre = $_POST['Nombre'];
        $stock = $_POST['Stock'];
        $descripcion = $_POST['Descripcion'];
        $id_categoria_producto = $_POST['id_categoria_producto'];
        $imagen = $_FILES['imagen'];

        // Generar un nombre para la imagen
        $nombre_imagen = $id_categoria_producto . '_' . $nombre . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION);

        // Subir la imagen
        $ruta_imagen = '../../imgs/productos/productos/' . $nombre_imagen;

        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }

        if (!move_uploaded_file($imagen['tmp_name'], $ruta_imagen)) {
            throw new Exception('No se pudo subir la imagen.');
        }

        // Insertar producto en la base de datos
        $sql = "INSERT INTO producto (Nombre, Stock, Descripcion, Estado, id_categoria_producto, imagen) 
                VALUES (:nombre, :stock, :descripcion, 1, :id_categoria_producto, :imagen)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id_categoria_producto', $id_categoria_producto);
        $stmt->bindParam(':imagen', $nombre_imagen);
        
        $stmt->execute();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
