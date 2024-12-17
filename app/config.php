<?php
//Definir las constantes para la conexion a la base de datos
define('SERVIDOR','cespedes.ddns.net');
define('PUERTO','3306');
define('USUARIO','root');
define('PASSWORD','*Royner123123*');
define('BD','deli');

//Definir la variable $servidor con los datos de la conexion
$servidor = "mysql:host=".SERVIDOR.";port=".PUERTO.";dbname=".BD;

try{
    //Realizar la conexion a la base de datos
    $pdo = new PDO($servidor,USUARIO,PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    //echo "La conexión a la base de datos fue con exito";
}catch (PDOException $e){
    //print_r($e);
    echo "Error al conectar a la base de datos";
}

//Definir la variable $URL con la ruta del sistema
$URL = "https://royner.ddns.net/deli/";

//Definir la zona horaria
date_default_timezone_set("America/Lima");
//Definir la fecha y hora actual
$fechaHora = date('Y-m-d H:i:s');


