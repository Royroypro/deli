<?php
session_start();
require '../../../app/config.php';

// Verificar si se ha recibido el token a través de la URL y el código de verificación a través del formulario
if (isset($_GET['token']) && isset($_POST['codigo'])) {
    $token = $_GET['token']; // Token recibido desde la URL
    $codigo_ingresado = $_POST['codigo']; // Código ingresado por el usuario

    try {
        // Buscar al usuario con el token proporcionado en la base de datos
        $sentencia = $pdo->prepare("SELECT * FROM usuarios WHERE token = :token");
        $sentencia->bindParam('token', $token);
        $sentencia->execute();

        // Verificar si el usuario con ese token existe
        if ($sentencia->rowCount() > 0) {
            $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

            // Verificar la hora en la que se generó el código y el token
            $hora_actual = new DateTime();
            $hora_codigo = new DateTime($usuario['fecha_registro']);
            $diferencia = $hora_actual->diff($hora_codigo);

            // Comparar el código ingresado y verificar si el token ha caducado
            if ($usuario['codigo'] == $codigo_ingresado && $diferencia->h < 24) {
                // Actualizar el estado del usuario a "Confirmado" (estado = 1) y confirmado = 'si'
                $actualizar = $pdo->prepare("UPDATE usuarios SET estado = 1, esta_cuenta = 'confirmado' WHERE token = :token");
                $actualizar->bindParam('token', $token);
                $actualizar->execute();
                echo "Tu cuenta ha sido confirmada exitosamente.";
                // Consultar el rol del usuario
                $sentencia_rol = $pdo->prepare("SELECT rol FROM usuarios WHERE token = :token");
                $sentencia_rol->bindParam('token', $token);
                $sentencia_rol->execute();
                $rol = $sentencia_rol->fetch(PDO::FETCH_ASSOC)['rol'];
                // Redirigir según el rol del usuario
                if ($rol == 'cliente') {
                    header('Location: ' . $URL . 'login/cliente.php');
                } else {
                    header('Location: ' . $URL . 'login');
                }
        } else {
            // Error: no se encontró un usuario con ese token
            echo "No se encontró el usuario con ese token.";
        }
    }
    } catch (PDOException $e) {
        // Manejo de errores en caso de problemas con la base de datos
        echo "Error al verificar el código: " . $e->getMessage();
        // Loggear el error en un archivo
        $file = fopen("error.log", "a");
        fwrite($file, date("Y-m-d H:i:s") . " - " . $e->getMessage() . "\n");
        fclose($file);
    }
} else {
    // Si no se reciben el token o el código, mostrar un mensaje de error
    echo "Faltan datos para la verificación.";
    // Loggear el error en un archivo
    $file = fopen("error.log", "a");
    fwrite($file, date("Y-m-d H:i:s") . " - Faltan datos para la verificación\n");
    fclose($file);
}


