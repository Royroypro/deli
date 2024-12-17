<?php

include('../app/config.php');

$id_pedido = $_POST['id_pedido'];
$estado = $_POST['estado'];

$consulta = $pdo->prepare("UPDATE pedidos SET estado = :estado WHERE id_pedido = :id_pedido");
$consulta->execute([
    'estado' => $estado,
    'id_pedido' => $id_pedido
]);

header("Location: lista_pedidos_cliente.php");
exit();

