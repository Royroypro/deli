
<!-- HTML para el formulario de verificación -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta</title>
    <link rel="stylesheet" href="../../food/login.css">
</head>
<body>
<div class="container">
<div class="form signupform" style="display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div style="max-width: 300px; width: 50%; background-color: #FFFFFF; padding: 20px; border-radius: 10px;">
        <h2>Verificación de Cuenta</h2>
        <p style="color: #888;">Ingresa el código de verificación que hemos enviado a tu correo:</p>
        <form action="../controllers/usuario/verificar_codigo.php?token=<?php echo $_GET['token']; ?>" method="POST">
            <div class="input-container">
                <label for="codigo">Código de Verificación:</label>
                <input type="text" id="codigo" name="codigo" required style="width: 100%">
            </div>
            <button type="submit" style="width: 100%; margin-top: 10px;">Verificar</button>
        </form>
    </div>
</div>

</div>
</body>
</html>

