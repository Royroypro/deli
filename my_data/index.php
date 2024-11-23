<?php
include ('../app/config.php');
include ('../layout/sesion.php');
include ('../layout/parte1.php');
include ('../app/controllers/cargo/listado_de_cargo.php');
include ('../app/controllers/my_data/datos_perfil.php');

?>




<!DOCTYPE html>
<html lang="es">
<head>
	<title>Mis Datos</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<link rel="stylesheet" href="<?php echo $URL; ?>/plant/css/main.css">
</head>
<body>

		<!-- Panel mis datos -->
		<div class="container-fluid">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="zmdi zmdi-refresh"></i> &nbsp; MIS DATOS</h3>
				</div>
				<div class="panel-body">

                <form action="<?php echo $URL; ?>/app/controllers/my_data/actualizar_perfil.php" autocomplete="off" method="post">
    <fieldset>
        <legend><i class="zmdi zmdi-account-box"></i> &nbsp; Información personal</legend>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group label-floating">
                        <label class="control-label">DNI/CEDULA *</label>
                        <input pattern="[0-9-]{1,10}" class="form-control" type="text" name="dni-up" required maxlength="10" 
                            value="<?php echo htmlspecialchars($usuario['Dni']); ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <label class="control-label">Nombres *</label>
                        <input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,100}" class="form-control" type="text" name="nombre-up" required maxlength="100" 
                            value="<?php echo htmlspecialchars($usuario['Nombre_personal']); ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <label class="control-label">Apellido paterno *</label>
                        <input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,100}" class="form-control" type="text" name="apellido_paterno-up" required maxlength="100" 
                            value="<?php echo htmlspecialchars($usuario['Apellido_paterno']); ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <label class="control-label">Apellido materno *</label>
                        <input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,250}" class="form-control" type="text" name="apellido_materno-up" required maxlength="250" 
                            value="<?php echo htmlspecialchars($usuario['Apellido_materno']); ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <label class="control-label">Teléfono</label>
                        <input pattern="[0-9+]{1,11}" class="form-control" type="text" name="celular-up" maxlength="11" 
                            value="<?php echo htmlspecialchars($usuario['Celular']); ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                        <label class="control-label">Cargo *</label>
                        <select class="form-control" name="id_cargo-up" required>
                            
                            <?php foreach ($cargos as $cargo) { ?>
                                <option value="<?php echo $cargo['ID_cargo']; ?>" <?php echo (isset($id_cargo_sesion) && $id_cargo_sesion == $cargo['ID_cargo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cargo['Nom_cargo']); ?>
                                </option>
                            <?php } ?>
                        </select>



                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group label-floating">
                        <label class="control-label">Dirección *</label>
                        <textarea name="direccion-up" class="form-control" rows="2" maxlength="250" required><?php echo htmlspecialchars($usuario['Direccion']); ?></textarea>
                    </div>
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
		
	


</body>
</html>