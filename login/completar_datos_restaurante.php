<?php
include "../app/config.php";



/* include "../admin/layout/parte1.php"; */
$nombre_usuario = $_GET['nombre_usuario'];
echo "el restaurante es este: ".$nombre_usuario;

?>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/normalize.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/sweetalert2.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/material.min.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/main.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/jquery-1.11.2.min.js"><\/script>')</script>
	<script src="<?php echo $URL;?>admin/dis/js/material.min.js" ></script>
	<script src="<?php echo $URL;?>admin/dis/js/sweetalert2.min.js" ></script>
	<script src="<?php echo $URL;?>admin/dis/js/jquery.mCustomScrollbar.concat.min.js" ></script>
	<script src="<?php echo $URL;?>admin/dis/js/main.js" ></script>
</head>
<section class="full-width header-well">
	<div class="full-width header-well-icon">
		<i class="zmdi zmdi-store"></i>
	</div>
	<div class="full-width header-well-text">
		<p class="text-condensedLight">
			Completa los siguientes datos para registrar el restaurante
		</p>
	</div>
</section>
<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
	<div class="mdl-tabs__tab-bar">
		<a href="#tabNewRestaurante" class="mdl-tabs__tab is-active">Completar</a>
	</div>
	<div class="mdl-tabs__panel is-active" id="tabNewRestaurante">
		<div class="mdl-grid">
			<div class="mdl-cell mdl-cell--12-col">
				<div class="full-width panel mdl-shadow--2dp">
					<div class="full-width panel-tittle bg-primary text-center tittles">
						Nuevo Restaurante
					</div>
					<div class="full-width panel-content">
						<form action="../admin/controllers/restaurantes/completar_datos.php/" method="post" enctype="multipart/form-data">
							<div class="mdl-grid">
								<div class="mdl-cell mdl-cell--12-col">
									<legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; RESTAURANT DATA</legend><br>
								</div>
								<div class="mdl-cell mdl-cell--12-col">
									<input type="hidden" name="nombre_usuario" value="<?php echo $nombre_usuario; ?>">
								</div>
								<div class="mdl-cell mdl-cell--12-col">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="nombre" name="nombre" required>
										<label class="mdl-textfield__label" for="nombre">Nombre del Restaurante</label>
										<span class="mdl-textfield__error">Invalid nombre</span>
									</div>
								</div>
								<div class="mdl-cell mdl-cell--12-col">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="direccion" name="direccion" required>
										<label class="mdl-textfield__label" for="direccion">Dirección</label>
										<span class="mdl-textfield__error">Invalid direccion</span>
									</div>
								</div>
								<div class="mdl-cell mdl-cell--12-col">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="horario" name="horario" pattern="^(0?[1-9]|1[0-2]):[0-5][0-9] (AM|PM) - (0?[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$" required>
										<label class="mdl-textfield__label" for="horario">EjempLo: Horario (8:00 AM - 9:00 PM)</label>
										<span class="mdl-textfield__error">Invalid horario format</span>
									</div>
								</div>
								<div class="mdl-cell mdl-cell--12-col">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="tel" pattern="-?[0-9+()- ]*(\.[0-9]+)?" id="contacto" name="contacto">
										<label class="mdl-textfield__label" for="contacto">Contacto</label>
										<span class="mdl-textfield__error">Invalid contacto</span>
									</div>
								</div>
								<div class="mdl-cell mdl-cell--12-col">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="file" id="imagen_logo" name="imagen_logo">
										<label class="mdl-textfield__label" for="imagen_logo">Logo del Restaurante</label>
									</div>
								</div>
								<p class="text-center">
									<button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored bg-primary" id="btn-addRestaurante">
										<i class="zmdi zmdi-plus"></i>
									</button>
									<div class="mdl-tooltip" for="btn-addRestaurante">Add restaurante</div>
								</p>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</section>
