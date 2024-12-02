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
                    // Verificar si el usuario es cliente
                    if ($usuario['rol'] === 'cliente') {
                        // Verificar si el usuario tiene una cuenta relacionada en la tabla clientes
                        $sql_cliente = "SELECT COUNT(*) AS existe FROM clientes WHERE id_usuario = :id_usuario";
                        $query_cliente = $pdo->prepare($sql_cliente);
                        $query_cliente->execute([':id_usuario' => $usuario['id_usuario']]);
                        $existe_cliente = $query_cliente->fetch(PDO::FETCH_ASSOC)['existe'] == 1;

                        if ($existe_cliente) {
                            // Iniciar sesión
                            session_start();
                            $_SESSION['id_usuario'] = $usuario['id_usuario'];
                            $_SESSION['nombre_usuario'] = $usuario['nombre'];
                            $_SESSION['rol'] = $usuario['rol'];
                            $_SESSION['email'] = $usuario['email'];
                            $respuesta = array('estado'=>'success');
                        } else {
                            // Redirigir a completar_datos.php
                            $respuesta = array('estado' => 'completar', 'nombre_usuario' => $nombre_usuario);
                            header('Content-Type: application/json');
                            echo json_encode($respuesta);
                            exit;
                        }
                    } else {
                        $respuesta = array
                        
                        ('estado'=>
                        'error',
                        'mensaje'=>
                        'Este usuario es un repartidor o restaurante (si eres un repartidor o restaurante <a href="' . $URL . 'login/">inicia sesión aqui en SOCIOS</a> ).
                        Cree una cuenta en la ventana de la derecha como cliente para iniciar sesión. ');
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


