<?php

include "../../../app/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreUsuario = $_POST['nombre_usuario'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $horario = $_POST['horario'];
    $contacto = $_POST['contacto'];

    // Consultar para traer el id del usuario por su nombre
    try {
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre = :nombre_usuario LIMIT 1");
        $stmt->execute(['nombre_usuario' => $nombreUsuario]);
        $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
        $idUsuario = $usuarioData['id_usuario'] ?? null;
    } catch (Exception $e) {
        echo "Error al consultar el ID del usuario: " . $e->getMessage();
        exit;
    }

    // Handle file upload
    if (isset($_FILES['imagen_logo']) && $_FILES['imagen_logo']['error'] == UPLOAD_ERR_OK) {
        $imagenLogo = $_FILES['imagen_logo']['name'];
        $imagenLogoTmp = $_FILES['imagen_logo']['tmp_name'];
        $uploadDir = 'uploads/';
        move_uploaded_file($imagenLogoTmp, $uploadDir . basename($imagenLogo));
    } else {
        $imagenLogo = null;
    }

    // Insertar en la tabla restaurantes
    try {
        $stmt = $pdo->prepare("INSERT INTO restaurantes (nombre, direccion, horario, contacto, imagen_logo) VALUES (:nombre, :direccion, :horario, :contacto, :imagen_logo)");
        $stmt->execute([
            'nombre' => $nombre,
            'direccion' => $direccion,
            'horario' => $horario,
            'contacto' => $contacto,
            'imagen_logo' => $imagenLogo,
        ]);

        $idRestaurante = $pdo->lastInsertId();

        // Actualizar el id_restaurante en la tabla usuarios
        $updateStmt = $pdo->prepare("UPDATE usuarios SET id_restaurante = :id_restaurante WHERE id_usuario = :id_usuario");
        $updateStmt->execute([
            'id_restaurante' => $idRestaurante,
            'id_usuario' => $idUsuario,
        ]);

        echo "Ha sido registrado correctamente";
        echo '<meta http-equiv="Refresh" content="2; url=' . $URL . '/login" />';
        exit;
    } catch (Exception $e) {
        echo "Error al guardar los datos: " . $e->getMessage();
    }
}
?>

