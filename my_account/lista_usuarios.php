<?php


$consulta = $pdo->prepare("SELECT * FROM usuarios");
$consulta->execute();
$usuarios = $consulta->fetchAll();

foreach ($usuarios as $usuario) {
    $id_usuario = $usuario['id_usuario'];
    $nombre = $usuario['nombre'];
    $email = $usuario['email'];
    $password = $usuario['password'];
    $rol = $usuario['rol'];
    $id_restaurante = $usuario['id_restaurante'];
    $fecha_registro = $usuario['fecha_registro'];
    $estado = $usuario['estado'];
    $codigo = $usuario['codigo'];
    $token = $usuario['token'];
    $fecha_nacimiento = $usuario['fecha_nacimiento'];
    $esta_cuenta = $usuario['esta_cuenta'];
}



?>

