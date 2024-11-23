<?php
/**
 * Created by PhpStorm.
 * User: HILARIWEB
 * Date: 18/1/2023
 * Time: 15:02
 */

 @include '../plant/control/veri.php';
session_start();
if (isset($_SESSION['id_usuario'])) {
    // Obtener el ID del usuario de la sesión
    $id_usuario_sesion = $_SESSION['id_usuario'];

    // Modificar la consulta para incluir el ID_cargo
    $sql = "SELECT id_usuario, nombre, email, password, rol, id_restaurante, fecha_registro, estado, codigo, token, fecha_nacimiento, esta_cuenta
            FROM usuarios
            WHERE id_usuario = :id_usuario_sesion";

    $query = $pdo->prepare($sql);
    $query->execute(['id_usuario_sesion' => $id_usuario_sesion]);
    $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
    // Obtener el cargo del usuario desde la consulta
    foreach ($usuarios as $usuario) {
        $nombres_sesion = $usuario['nombre'];
        $rol_sesion = $usuario['rol'];
        $id_restaurante_sesion = $usuario['id_restaurante']; // Aquí estamos agregando el cargo
        $correo_sesion = $usuario['email']; // Aquí estamos agregando el correo
    }
} else {
    echo "no existe sesion";
    header('Location: ' . $URL . '/login');
}


