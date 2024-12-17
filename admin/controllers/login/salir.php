<?php
include_once '../../../app/config.php';
include_once '../../../layout/sesion.php';

session_destroy();
if ($rol_sesion == "cliente") {

    header('Location: ' . $URL . '/login/cliente.php');

}

else if ($rol_sesion == "restaurante" || $rol_sesion == "repartidor") {

    header('Location: ' . $URL . '/login/');
}





/* session_start(); */
/* session_destroy(); */
/* header('Location: ' . $URL . '/login/'); */
