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

    // Modificar la consulta para incluir el ID_cargo y el ID_cliente
    $sql = "SELECT u.id_usuario, u.nombre, u.email, u.password, u.rol, u.id_restaurante, u.fecha_registro, u.estado, u.codigo, u.token, u.fecha_nacimiento, u.esta_cuenta, c.id_cliente, c.direccion, c.telefono, c.puntos_fidelidad, r.id_repartidor
            FROM usuarios u
            LEFT JOIN clientes c ON u.id_usuario = c.id_usuario
            LEFT JOIN repartidores r ON u.id_usuario = r.id_usuario
            WHERE u.id_usuario = :id_usuario_sesion";

    $query = $pdo->prepare($sql);
    $query->execute(['id_usuario_sesion' => $id_usuario_sesion]);
    $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
    // Obtener el cargo y el ID_cliente del usuario desde la consulta
    foreach ($usuarios as $usuario) {
        $nombres_sesion = $usuario['nombre'];
        $rol_sesion = $usuario['rol'];
        $id_restaurante_sesion = $usuario['id_restaurante'];
        $correo_sesion = $usuario['email']; // Aquí estamos agregando el correo
        $id_cliente_sesion = $usuario['id_cliente'];
        $direccion_sesion = $usuario['direccion'];
        $telefono_sesion = $usuario['telefono'];
        $puntos_fidelidad_sesion = $usuario['puntos_fidelidad'];
        $id_repartidor_sesion = $usuario['id_repartidor'];
    }
} else {
    echo "no existe sesion";
    header('Location: ' . $URL . '/login');
}

