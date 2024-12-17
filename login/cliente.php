<?php 

include '../app/config.php';

?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login/Register</title>
        <link rel="stylesheet" href="../food/login.css">
        <link rel="icon" href="../food/Images/Restaurants/download.png" type="image/icon type">
    </head>
<body>
    
  <div class="container">
      <div class="loginbg">
      
          <div class="box signin">
          <h1 style="text-align: center;">Cliente</h1>
              <h2>¿Ya tienes una cuenta?</h2>
             
              <button class="signinbtn">Iniciar sesión</button>
          </div>
          <div class="box signup">
          <h1 style="text-align: center;">Cliente</h1>
            <h2>¿No tienes una cuenta?</h2>
            
            <button class="signupbtn">Regístrate</button>
          </div>
      </div>
      <div class="formbx">
      

        <div class="form signinform">
        <form id="loginForm">
                <h2>Chaski.  Cito</h2>
                <h3>Iniciar sesión</h3>
                <input type="text" name="nombre_usuario" placeholder="Nombre de usuario">
                <div id="alert" style="color: red; font-weight: bold;"></div>
                <input type="password" name="contrasena" placeholder="Contraseña">
                <input type="button" value="Iniciar sesión" onclick="submitLoginForm()">
                <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
            </form>
         </div>

        <script>
            function submitLoginForm() {
                const form = document.getElementById('loginForm');
                const formData = new FormData(form);
                fetch('<?php echo $URL;?>app/controllers/login/ingreso.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const alert = document.getElementById('alert');
                    alert.innerHTML = data.mensaje;
                    if(data.estado === 'success') {
                        window.location.href = '<?php echo $URL;?>index.php';
                    } else if (data.estado === 'completar') {
                        window.location.href = '<?php echo $URL;?>login/completar_datos_cliente.php?nombre_usuario=' + formData.get('nombre_usuario');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        </script>




      

        <div class="form signupform">
        <form action="../admin/controllers/usuario/crear_socio.php" method="POST">
                <h3>Regístrate</h3>
                <input type="text" name="nombre" placeholder="Ingresa tu nombre">
                <input type="text" name="correo" placeholder="Correo electrónico">
                <input type="date" name="fecha_nacimiento" placeholder="Ingresa tu fecha de nacimiento">
                <input type="hidden" name="tipo_usuario" value="cliente">
                <input type="password" name="contrasena" placeholder="Contraseña">
                <input type="password" name="confirmar_contrasena" placeholder="Confirmar contraseña">
                
                <input type="submit" value="Registrarse">
            </form>
        </div>
      </div>
  </div>
<script>
    const signinbtn = document.querySelector('.signinbtn');
    const signupbtn = document.querySelector('.signupbtn');
    const formbx = document.querySelector('.formbx'); 
    const body = document.querySelector('body')

    signupbtn.onclick = function(){
        formbx.classList.add('active')
        body.classList.add('active')
    }
    signinbtn.onclick = function(){
        formbx.classList.remove('active')
        body.classList.remove('active')
    }
    </script>
      
</body>
</html>
