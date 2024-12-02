<?php
// Incluir la configuración de la base de datos
include(__DIR__ . '/../../../app/config.php');

// Verificar si se recibieron los datos del formulario por AJAX
if (isset($_POST['nombre_usuario']) && isset($_POST['contrasena'])) {
    // Limpiar y asignar las entradas
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contrasena = trim($_POST['contrasena']);

    try {
        // Consulta para verificar si el usuario existe en la base de datos
        $sql = "SELECT * FROM usuarios WHERE nombre = :nombre_usuario AND estado = 1 LIMIT 1";
        $query = $pdo->prepare($sql);
        $query->execute([':nombre_usuario' => $nombre_usuario]);

        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe
        if ($usuario) {
            // Verificar la contraseña
            if (password_verify($contrasena, $usuario['password'])) {
                // Verificar si la cuenta está confirmada
                if ($usuario['esta_cuenta'] === "confirmado") {
                    if ($usuario['rol'] === 'restaurante') {
                        // Verificar si el restaurante tiene un id_restaurante asociado
                        if ($usuario['id_restaurante'] === null) {
                            $respuesta = array('estado'=>'completar_res', 'nombre_usuario' => $nombre_usuario);
                        } else {
                            // Iniciar sesión
                            session_start();
                            $_SESSION['id_usuario'] = $usuario['id_usuario'];
                            $_SESSION['nombre_usuario'] = $usuario['nombre'];
                            $_SESSION['rol'] = $usuario['rol'];
                            $_SESSION['email'] = $usuario['email'];
                            $respuesta = array('estado'=>'success');
                        }
                    } elseif ($usuario['rol'] === 'repartidor') {
                        // Verificar si el repartidor tiene relación con la tabla repartidores
                        $sql = "SELECT COUNT(*) AS existe FROM repartidores WHERE id_usuario = :id_usuario";
                        $query = $pdo->prepare($sql);
                        $query->execute([':id_usuario' => $usuario['id_usuario']]);
                        $existe_repartidor = $query->fetch(PDO::FETCH_ASSOC)['existe'] == 1;

                        if (!$existe_repartidor) {
                            $respuesta = array('estado'=>'completar_rep', 'nombre_usuario' => $nombre_usuario);
                        } else {
                            // Iniciar sesión
                            session_start();
                            $_SESSION['id_usuario'] = $usuario['id_usuario'];
                            $_SESSION['nombre_usuario'] = $usuario['nombre'];
                            $_SESSION['rol'] = $usuario['rol'];
                            $_SESSION['email'] = $usuario['email'];
                            $respuesta = array('estado'=>'success');
                        }
                    } else {
                        // Iniciar sesión para otros roles
                        session_start();
                        $_SESSION['id_usuario'] = $usuario['id_usuario'];
                        $_SESSION['nombre_usuario'] = $usuario['nombre'];
                        $_SESSION['rol'] = $usuario['rol'];
                        $_SESSION['email'] = $usuario['email'];
                        $respuesta = array('estado'=>'success');
                    }
                } else {
                    $respuesta = array('estado'=>'error', 'mensaje'=>'Cuenta no confirmada.');
                }
            } else {
                $respuesta = array('estado'=>'error', 'mensaje'=>'Contraseña incorrecta.');
            }
        } else {
            $respuesta = array('estado'=>'error', 'mensaje'=>'Usuario no encontrado.');
        }
    } catch (Exception $e) {
        // Manejo de errores en la conexión o consulta
        $respuesta = array('estado'=>'error', 'mensaje'=>'Problema en el servidor. Intente nuevamente más tarde.');
    }
} else {
    $respuesta = array('estado'=>'error', 'mensaje'=>'Datos incompletos.');
}
header('Content-Type: application/json');
echo json_encode($respuesta);

