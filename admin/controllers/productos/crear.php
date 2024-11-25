<?php

include_once '../../../app/config.php';
include_once '../../../layout/sesion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    try {

        $id_restaurante = $id_restaurante_sesion;
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];
        $imagen = $_FILES['imagen'];
        $categorias = $_POST['categorias'];

        if (empty($categorias)) {
            echo "Seleccione por lo menos una categor a";
            exit;
        }

        // Generar un nombre para la imagen
        $nombre_imagen = $id_restaurante . '_' . $nombre . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION);

        // Subir la imagen
        $ruta_imagen = '../../imgs/productos/productos/' . $nombre_imagen;

        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }

        if (!move_uploaded_file($imagen['tmp_name'], $ruta_imagen)) {
            throw new Exception('No se pudo subir la imagen.');
        }

        // Insertar producto en la base de datos
        $sql = "INSERT INTO productos (id_restaurante, nombre, descripcion, precio, stock, imagen) 
                VALUES (:id_restaurante, :nombre, :descripcion, :precio, :stock, :imagen)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_restaurante', $id_restaurante);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':imagen', $nombre_imagen);
        
        $stmt->execute();

        // Relacionar producto con su categoría
        $id_producto = $pdo->lastInsertId();
        foreach ($categorias as $id_categoria) {
            $sql_categoria = "INSERT INTO productos_categorias (id_producto, id_categoria) 
                              VALUES (:id_producto, :id_categoria)";
            
            $stmt_categoria = $pdo->prepare($sql_categoria);
            $stmt_categoria->bindParam(':id_producto', $id_producto);
            $stmt_categoria->bindParam(':id_categoria', $id_categoria);
            $stmt_categoria->execute();
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

