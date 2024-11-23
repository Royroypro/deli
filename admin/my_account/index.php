<?php
include ('../app/config.php');
include ('../layout/sesion.php');
include ('../layout/parte1.php');
include ('../app/controllers/usuario/listado_de_usuario.php');


?>




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


<form action="<?php echo $URL; ?>/app/controllers/my_account/actualizar.php" atocomplete="off" method="POST">
    <fieldset>
        <legend><i class="zmdi zmdi-key"></i> &nbsp; Datos de la cuenta</legend>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group label-floating">
                        <label class="control-label">Nombre de usuario *</label>
                        <input pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ]+" class="form-control" type="text" name="usuario-up" required="" value="<?php echo $nombres_sesion; ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <p>Agregar Correo de Recuperación</p>
                        <label class="control-label">E-mail</label>
                        <input class="form-control" type="email" name="email-up" maxlength="50" value="<?php echo $correo_sesion; ?>">
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <br>
	
					<!-- Mostrar el mensaje si existe -->
					<?php if (isset($_SESSION['mensaje'])): ?>
						<div class="alert <?php echo $_SESSION['mensaje'] === 'Datos actualizados con éxito' ? 'alert-success' : 'alert-danger'; ?>">
							<?php echo $_SESSION['mensaje']; ?>
						</div>
						<?php unset($_SESSION['mensaje']); ?> <!-- Limpiar el mensaje después de mostrarlo -->
					<?php endif; ?>

    <fieldset>
        <legend><i class="zmdi zmdi-lock"></i> &nbsp; Contraseña</legend>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group label-floating">
                        <label class="control-label">Contraseña actual *</label>
                        <input class="form-control password-toggle" type="password" name="password-up" required="" maxlength="70">
                        <span class="toggle-password"><i class="zmdi zmdi-eye"></i></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <label class="control-label">Nueva contraseña *</label>
                        <input class="form-control password-toggle" type="password" name="newPassword1-up" required="" maxlength="70">
                        <span class="toggle-password"><i class="zmdi zmdi-eye"></i></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <label class="control-label">Repita la nueva contraseña *</label>
                        <input class="form-control password-toggle" type="password" name="newPassword2-up" required="" maxlength="70">
                        <span class="toggle-password"><i class="zmdi zmdi-eye"></i></span>
                    </div>
                </div>
            </div>
        </div>

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
    <fieldset>
        <legend><i class="zmdi zmdi-star"></i> &nbsp; Nivel de privilegios</legend>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <p class="text-left">
                        <div class="label label-success">Nivel 1</div> Control total del sistema
                    </p>
                    <p class="text-left">
                        <div class="label label-primary">Nivel 2</div> Permiso para registro y actualización
                    </p>
                    <p class="text-left">
                        <div class="label label-info">Nivel 3</div> Permiso para registro
                    </p>
                </div>
            </div>
        </div>
    </fieldset>
    <p class="text-center" style="margin-top: 20px;">
        <button type="submit" class="btn btn-success btn-raised btn-sm"><i class="zmdi zmdi-refresh"></i> Actualizar</button>
    </p>
</form>




				</div>
			</div>
		</div>
		
	</section>

</body>
</html>


<script>
function confirmacionEliminar(e, form) {
    e.preventDefault(); // Prevenir el envío del formulario por defecto

    // Mostrar el modal de confirmación
    var modal = document.getElementById("customConfirmModal");
    modal.style.display = "block";

    // Configurar el comportamiento del botón de confirmación
    document.getElementById("confirmDelete").onclick = function() {
        form.submit(); // Enviar el formulario si el usuario confirma
    };

    // Configurar el comportamiento del botón de cancelación
    document.getElementById("cancelDelete").onclick = function() {
        modal.style.display = "none"; // Cerrar el modal si el usuario cancela
    };

    // Cerrar el modal si el usuario hace clic fuera de él
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
}





</script>

<style>
/* Estilos para el modal personalizado */
#customConfirmModal {
    display: none; /* Ocultar el modal por defecto */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto; /* Hacer que el contenido sea desplazable si es necesario */
    background-color: rgba(0, 0, 0, 0.4); /* Fondo oscuro */
}

/* Estilo del contenido del modal */
#customConfirmModal .modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Estilo del título */
#customConfirmModal .modal-header h2 {
    margin: 0;
}

/* Estilo de los botones */
#customConfirmModal .modal-footer button {
    padding: 10px 20px;
    font-size: 16px;
    margin: 5px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
}

#customConfirmModal .modal-footer #confirmDelete {
    background-color: #3085d6;
    color: white;
}

#customConfirmModal .modal-footer #cancelDelete {
    background-color: #d33;
    color: white;
}
</style>

<!-- Modal de confirmación personalizado -->
<div id="customConfirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>¿Estás seguro?</h2>
        </div>
        <div class="modal-body">
            <p>No podrás revertir esto.</p>
        </div>
        <div class="modal-footer">
            <button type="button" id="confirmDelete">Sí, eliminarlo</button>
            <button type="button" id="cancelDelete">Cancelar</button>
        </div>
    </div>
</div>


