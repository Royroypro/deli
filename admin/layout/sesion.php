<?php
/**
 * Created by PhpStorm.
 * User: HILARIWEB
 * Date: 18/1/2023
 * Time: 15:02
 */

 @include '../plant/control/veri.php';

session_start();
if (isset($_SESSION['sesion_usuario'])) {
    // Obtener el nombre de usuario y el ID del usuario de la sesión
    $nombre_usuario_sesion = $_SESSION['sesion_usuario'];
  

    // Modificar la consulta para incluir el ID_cargo
    $sql = "SELECT u.ID_usuario, u.Nombre_usuario, u.ID_tipousuario, tu.Nombre_tipousuario as rol, p.ID_cargo, u.Correo
            FROM usuario as u
            INNER JOIN tipo_usuario as tu ON u.ID_tipousuario = tu.ID_tipousuario
            INNER JOIN personal as p ON u.id_personal = p.ID_personal
            WHERE Nombre_usuario = :nombre_usuario_sesion";

    $query = $pdo->prepare($sql);
    $query->execute(['nombre_usuario_sesion' => $nombre_usuario_sesion]);
    $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);

    // Obtener el cargo del usuario desde la consulta
    foreach ($usuarios as $usuario) {
        
        $id_usuario_sesion = $usuario['ID_usuario']; // Aquí estamos agregando el ID del usuario
        $nombres_sesion = $usuario['Nombre_usuario'];
        $rol_sesion = $usuario['rol'];
        $id_cargo_sesion = $usuario['ID_cargo']; // Aquí estamos agregando el cargo
        $correo_sesion = $usuario['Correo']; // Aquí estamos agregando el correo
    }
} else {
    echo "no existe sesion";
    header('Location: ' . $URL . '/login');
}

