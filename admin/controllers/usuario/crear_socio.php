<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir el autoload de Composer
require '../../../app/config.php';
require '../../../vendor/autoload.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $tipo_usuario = $_POST['tipo_usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);
    $fecha_nacimiento = filter_var($fecha_nacimiento, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $tipo_usuario = filter_var($tipo_usuario, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($contrasena === $confirmar_contrasena) {
        
        $contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
   

        // Comprobar si el usuario ya existe
        $sentencia = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = :nombre OR email = :correo");
        $sentencia->bindParam('nombre', $nombre);
        $sentencia->bindParam('correo', $correo);
        $sentencia->execute();

        if ($sentencia->rowCount() > 0) {
            if ($sentencia->fetch()['nombre'] == $nombre) {
                echo "El nombre de usuario ya existe, por favor elija otro.";
            } else {
                echo "El correo electr&oacute;nico ya est&aacute; en uso, por favor elija otro.";
            }
        } else {
            

            // Generar un token &uacute;nico para la verificaci&oacute;n
            $token = bin2hex(random_bytes(32));

            // Generar el c&oacute;digo de verificaci&oacute;n
            $codigo = rand(1000, 9999);

            
            // Guardar los datos en la base de datos
            $fecha = date('Y-m-d H:i:s');
            $estado = 1; // Usuario activo pero no confirmado

            try {
                $sentencia = $pdo->prepare("INSERT INTO usuarios
                    (nombre, email, password, rol, id_restaurante, fecha_registro, estado, codigo, token, fecha_nacimiento, esta_cuenta) 
                    VALUES (:nombre, :correo, :password_user, :rol, :id_restaurante, :fecha, :estado, :codigo, :token, :fecha_nacimiento, :esta_cuenta)");

                $sentencia->bindParam('nombre', $nombre);
                $sentencia->bindParam('correo', $correo);
                $sentencia->bindParam('password_user', $contrasena);
                $sentencia->bindParam('rol', $tipo_usuario);
                $sentencia->bindParam('id_restaurante', $id_restaurante);
                $sentencia->bindParam('fecha', $fecha);
                $sentencia->bindParam('estado', $estado);
                $sentencia->bindParam('codigo', $codigo);
                $sentencia->bindParam('token', $token);
                $sentencia->bindParam('fecha_nacimiento', $fecha_nacimiento);
                $esta_cuenta = 'no_confirmado';
                $sentencia->bindParam('esta_cuenta', $esta_cuenta);

                $sentencia->execute();

                // Configuraci&oacute;n de PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // Configuraci&oacute;n del servidor SMTP de Gmail
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'seguridadelectronicahuacho@gmail.com'; // Tu correo
                    $mail->Password   = 'baugzazpvrkxjvju'; // Contrase&ntilde;a de la aplicaci&oacute;n
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Remitente y destinatario
                    $mail->setFrom('seguridadelectronicahuacho@gmail.com', 'Chaskifood');
                    $mail->addAddress($correo); // Destinatario

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Chaskifood - Confirmación de cuenta';

                    // Generar los enlaces de verificaci&oacute;n
                    $verification_link = $URL . "admin/usuario/verificar_codigo.php?token=" . $token;

                    $mail->Body = "Hola " . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ",<br><br>Gracias por registrarte en nuestro sistema. Tu nombre de usuario es <strong>" . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . "</strong> y tu c&oacute;digo de verificaci&oacute;n es <strong>" . $codigo . "</strong>. Haz clic en el siguiente enlace para confirmar tu cuenta: <br><br>
                                   <a href='" . $verification_link . "'>Confirmar mi cuenta</a><br><br>";
                    // Enviar el correo
                    $mail->send();

                    // Mensaje de &eacute;xito
                    session_start();
                    $_SESSION['mensaje'] = "Se registr&oacute; el usuario correctamente. Revisa tu correo para confirmar.";
                    echo "Se registr&oacute; el usuario correctamente. Revisa tu correo para confirmar.<br><br>";
                    // Redirigir despu&eacute;s de 3 segundos
                    echo "<meta http-equiv='refresh' content='3;url=".$verification_link."'>";

                } catch (Exception $e) {
                    session_start();
                    $_SESSION['mensaje'] = "Error al enviar el correo: " . $mail->ErrorInfo;
                    header('Location: ' . $verification_link);
                }

            } catch (PDOException $e) {
                session_start();
                $_SESSION['mensaje'] = "Error al registrar al usuario: " . $e->getMessage();
                /* header('Location: ' . $URL . '/usuarios/'); */
            }

        }
    } else {
        echo "Las contrase&ntilde;as no coinciden.";
    }

} else {
    header('Location: ' . $URL . '/');
    exit;
}



