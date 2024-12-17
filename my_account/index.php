<?php
include ('../app/config.php');
include ('../layout/sesion.php');
include "../admin/layout/parte1.php"; 
?>
<?php include "../layout/parte1.php"; ?>
<style>
	body{
		margin-top: 70px;
	}
</style>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Mi Cuenta</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<link rel="stylesheet" href="<?php echo $URL; ?>/plant/css/main.css">
</head>
<body>


	
		<!-- Content page -->
		<div class="container-fluid">
			<div class="page-header">
			  <h1 class="text-titles"><i class="zmdi zmdi-settings zmdi-hc-fw"></i> MI CUENTA</small></h1>
			</div>
			
		</div>
		<!-- Panel mi cuenta -->
		<div class="container-fluid">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="zmdi zmdi-refresh"></i> &nbsp; MI CUENTA</h3>
				</div>
				<div class="panel-body">
<form action="<?php echo $URL; ?>/app/controllers/my_account/actualizar.php" autocomplete="off" method="POST">
    <fieldset>
        <legend><i class="zmdi zmdi-key"></i> &nbsp; Datos de la cuenta</legend>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group label-floating">
                        <label class="control-label">Nombre de usuario *</label>
                        <input pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ]+" class="form-control" disabled type="text" name="nombre-up" required="" value="<?php echo $nombres_sesion ; ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <label class="control-label">E-mail</label>
                        <input class="form-control" disabled type="email" name="email-up" maxlength="100" value="<?php echo $usuario['email']; ?>">
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <br>

    
<script>
document.querySelectorAll('.toggle-password').forEach(item => {
    item.addEventListener('click', event => {
        const input = item.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        item.querySelector('i').classList.toggle('zmdi-eye');
        item.querySelector('i').classList.toggle('zmdi-eye-off');
    });
});
</script>
    </fieldset>

    <br>
  
    <p class="text-center" style="margin-top: 20px;">
        <a href="<?php echo $URL; ?>/admin/controllers/login/salir.php" class="btn btn-primary btn-raised btn-sm"><i class="zmdi zmdi-power"></i> CERRAR SESION</a>
    </p>
</form>

